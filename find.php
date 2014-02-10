<?php
/*

Runner's Medium
http://www.runnersmedium.com/

find.php

search for users

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('lib/base.php');

$user->signinCheck(signin());

$search = null;
$doSrch = false;

if (isset($_GET['q']) && notempty($_GET['q'])) {

	// search query
	$search = trim($_GET['q']);
	$doSrch = true;
	
	// defaults
	$rows   = MAX_SEARCH_RESULTS;
	$page   = 1;
	$ext    = new extHelper();
	
	// selected page
	if(isset($_GET['page']) && is_numeric($_GET['page'])) {
		$page = $_GET['page'];
	}
	
	// offset
	$offset = ($page - 1) * $rows;
	$sqlsearch = mysql_real_escape_string($search);
	
	$result = $conn->query("SELECT COUNT(id) FROM users 
		WHERE id != ".mysql_real_escape_string($user->ID())."
		AND ispublic = 1
		AND (username LIKE '%$sqlsearch%'
		OR name LIKE '%$sqlsearch%'
		OR email LIKE '%$sqlsearch%')");
	
	// calc total pages
	$count  = $conn->getRow($result);
	$max    = ceil($count/$rows);

	$result = $conn->query("SELECT username, picture, name, TIMESTAMPDIFF(YEAR, birthday, CURDATE()) AS age, location, gender FROM users
		WHERE id != ".mysql_real_escape_string($user->ID())."
		AND ispublic = 1
		AND (username LIKE '%$sqlsearch%'
		OR name LIKE '%$sqlsearch%'
		OR email LIKE '%$sqlsearch%')
		ORDER BY username LIMIT ".
		mysql_real_escape_string($offset).', '.
		mysql_real_escape_string($rows));
}

$title = 'Runner\'s Medium - Find Rnrs';
require('header.php');
?>

<div id="content">

	<?php messages($error, $message); ?>
		
	<h2>Find Runners</h2>

	<form action="" method="get" id="searchform">
		<fieldset>
			<label for="q">Search for Name, Username or Email</label>
			<input name="q" id="q" type="text" class="search" value="<?php echo format($search); ?>" />
			<input type="submit" value="Search" class="button inline" />
		</fieldset>
	</form>
	
	<?php if ($doSrch) :
			echo '<h3>'.$count.' users found';
			if ($count > 0) {
				echo ':';
			}
			echo '</h3>';
			
			if ($count > 0) {
				echo '<ol id="users">';
				while ($line = $conn->fetchAssoc($result)) :
				
					$theuser  = format($line['username']);
					
					echo '<li>';
					echo '<div class="pic"><a href="'.profile().$theuser.'">'.$user->getAnyPicture($theuser, $line['picture']).'</a></div>';
					
						echo '<div class="details">';
						echo '<h3><a href="'.profile().$theuser.'">'.$theuser.'</a></h3>';
						echo '<strong>Name</strong> '.$line['name'];
						
						if (notempty($line['age'])) {
							echo ' <strong>'.format($line['age']).'</strong> year old ';
						}
						
						if (notempty($line['gender'])) {
							echo ' <strong>'.format($line['gender']).'</strong> ';
						}
						
						if (notempty($line['location'])) {
							echo ' from <strong>'.format($line['location']).'</strong>';
						}
						echo '</div>';
					
					echo '</li>';
					
				endwhile; // results loop
				echo '</ol>';
				
			}
		
		// pagination
		$ext->paging('find?q='.format($search.'&'), $page, $max);
		
	endif; // do search ?>
	
</div>

<?php
require('footer.php');
?>