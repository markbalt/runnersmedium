<?php
/*

Runner's Medium
http://www.runnersmedium.com/

profile.php

show user profile info, snapshot and friend actions if applicable

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('lib/base.php');

$signedin = $user->signinCheck();

if (!isset($_GET['user'])) {
	// no user specified, redirect to index or current user's profile
	if($signedin) {
		redirect(friends($user->username()));
	} else {
		redirect(root());
	}
}

$thisPage = $_GET['user'];
$stop     = false;
$private  = true;
$myFriend = false;

// is this your page?
$myPage = ($user->username() == $thisPage) ? true : false;

// check if user exists
$result = $conn->query('SELECT a.id, a.picture, a.ispublic FROM users AS a WHERE a.username = \''.mysql_real_escape_string($thisPage).'\' LIMIT 1');

if($conn->rowCount($result) > 0) {

	$title = 'Runner\'s Medium - '.$thisPage.'\'s Friends';
	$line = $conn->fetchAssoc($result);
	
	$theid = mysql_real_escape_string($line['id']);
	
	// is this user my friend?
	if (!$myPage && $signedin) {
		$myid = mysql_real_escape_string($user->ID());
		$result = $conn->query('SELECT COUNT(*) FROM friends WHERE (user = '.$theid.' AND friend = '.$myid.' AND state = 2) OR (friend = '.$theid.' AND user = '.$myid.' AND state = 2)');
		
		if ($conn->getRow($result)) {
			$myFriend = true;
		}
	}
	
	// check profile privacy
	if(!$myPage && !$myFriend && $line['ispublic'] == 0) {
		$stop  = true;
		$error = $thisPage.'\'s profile is private'; 
	} else {
	
		// show profile
		$private = false;
		
		// defaults
		$rows = MAX_RESULTS;
		$page = 1;
		$ext  = new extHelper();
		
		// selected page
		if(isset($_GET['page']) && is_numeric($_GET['page'])) {
			$page = $_GET['page'];
		}
		
		// offset
		$offset = ($page - 1) * $rows;
		
		// calc total pages
		$result = $conn->query('SELECT COUNT(*) FROM friends WHERE state = 2 AND (user = '.$theid.' OR friend = '.$theid.')');
		$count  = $conn->getRow($result);
		$max    = ceil($count/$rows);
		
		if ($count > 0) {
			
			// query friends			
			$result = $conn->query('SELECT a.id AS friendid, b.id, b.username, b.picture, b.name, a.created
				FROM friends AS a JOIN users AS b on a.friend = b.id
				WHERE user = '.$theid.' AND state = 2
				UNION SELECT a.id AS friendid, b.id, b.username, b.picture, b.name, a.created
				FROM friends AS a JOIN users AS b ON a.user = b.id
				WHERE friend = '.$theid.' AND state = 2
				ORDER BY created DESC LIMIT '.
				mysql_real_escape_string($offset).', '.
				mysql_real_escape_string($rows));
			
			// check for invalid page parameter	
			if ($conn->rowCount($result) == 0) {
				$error = '<a href="'.friends($thisPage).'">No entries for that page.</a>';
				$stop  = true;
			}
		} else {
	
			// no friends yet
			$stop = true;
			
			if ($myPage) {
				$error = '<a href="'.root().'find">You have not added any friends yet.  Click here to find some running friends.</a>';
			} else {
				$error = '<a href="'.profile().$thisPage.'">'.$thisPage.'</a> has not added any friends yet.';
			}
		}
	}
	
} else {
	require('notfound.php');
	exit;
}

$thisPage = format($thisPage);

require('header.php');
?>

<div id="content">

	<?php
	messages($error, $message);
	
	if (!$stop && $conn->rowCount($result) > 0) :
	
		// who's friends?
		if ($myPage) {
			echo '<h2>You have ';
		} else {
			echo '<h2>'.$thisPage.' has ';
		}
		
		// number of friends
		echo  '<span id="friends-count">'.$count.'</span> Friend';
		if ($count > 1){
			echo 's';
		}
		echo '</h2>';
		
		echo '<ol id="users">';
		while ($line = $conn->fetchAssoc($result)) :
		
			$theuser = format($line['username']);
			$theid   = format($line['id']);
			$friendid = format($line['friendid']);
							
			echo '<li id="friend-'.$friendid.'">';
			echo '<div class="pic"><a href="'.profile().$theuser.'">'.$user->getAnyPicture($theuser, $line['picture']).'</a></div>';
			
				echo '<div class="details">';
					echo '<h3><a href="'.profile().$theuser.'">'.$theuser.'</a></h3>';
					echo '<strong>Name</strong> '.$line['name'];
					
					if ($myPage) {
						echo '<div class="actions"><span id="remove-'.$friendid.'"><a onclick="removeFriend('.$friendid.')">Remove</a></span></div>';
					}
				echo '</div>';
			
			echo '</li>';
			
		endwhile; // results loop
		echo '</ol>';
		
		// pagination
		$ext->paging(friends($thisPage).'?', $page, $max);
	
	endif; // no results
	?>
			
</div>

<?php

require('footer.php');

?>