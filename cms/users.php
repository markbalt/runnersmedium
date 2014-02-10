<?php
/*

Runner's Medium
http://www.runnersmedium.com/

users.php

manage users for admin

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('../lib/base.php');

if (!$user->signinCheck() || !$conn->isAdmin($user->ID())) {
	header('Location: '.signin());
	exit;
}

$sortby   = $mysort = 'id';
$sortopts = array('id' => 'id',
				  'username' => 'username',
				  'runs' => 'most runs',
				  'activity' => 'activity rating',
				  'created' => 'created');

// sort by
if (isset($_GET['sortby'])) {
	if (array_key_exists($_GET['sortby'], $sortopts)) {
		$sortby = $_GET['sortby'];
	}
}

// get sql sort by
switch ($sortby) {
	case 'id': $mysort = 'a.id ASC';
		break;
	case 'username': $mysort = 'a.username ASC';
		break;
	case 'runs': $mysort = 'runs DESC';
		break;
	case 'activity': $mysort = 'activity DESC';
		break;
}

// order
/*if (isset($_GET['order']) && (strtolower($_GET['order']) == 'desc' || strtolower($_GET['order']) == 'asc')) {
		$order = strtolower($_GET['order']);
}*/

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
$search = null;
$doSrch = false;

if (isset($_GET['q']) && notempty($_GET['q'])) {

	// search query
	$search = trim($_GET['q']);
	$doSrch = true;
	
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

	$result = $conn->query("SELECT a.id, a.username, a.name, a.email, a.picture,
		DATE_FORMAT(a.created, '%c/%e/%y') AS created, DATE_FORMAT(a.lastlogin, '%c/%e/%y') AS lastlogin,
		IF(a.lastlogin > SUBDATE(NOW(), INTERVAL 7 DAY) , 1, 0) AS loginlastweek,
		(SELECT COUNT(*) FROM runs WHERE user = a.id) AS runs,
		(SELECT COUNT(*) FROM courses WHERE user = a.id) AS courses,
		(SELECT COUNT(*) FROM shoes WHERE user = a.id) AS shoes,
		(SELECT COUNT(*) FROM friends WHERE user = a.id OR friend = a.id) AS friends,
		(SELECT COUNT(*) FROM likes WHERE user = a.id) AS likes,
		(SELECT (runs) + (courses) + (shoes) +  (friends) + (likes) + (loginlastweek)) AS activity
		FROM users AS a
		WHERE a.id LIKE '%$sqlsearch%'
		OR a.username LIKE '%$sqlsearch%'
		OR a.name LIKE '%$sqlsearch%'
		OR a.email LIKE '%$sqlsearch%'
		ORDER BY ".mysql_real_escape_string($mysort)." LIMIT ".
		mysql_real_escape_string($offset).", ".
		mysql_real_escape_string($rows));

} else {

	// calc total pages
	$result = $conn->query('SELECT COUNT(*) FROM users');
	$count  = $conn->getRow($result);
	$max    = ceil($count/$rows);

	$result = $conn->query('SELECT a.id, a.username, a.name, a.email, a.picture,
		DATE_FORMAT(a.created, \'%c/%e/%y\') AS created, DATE_FORMAT(a.lastlogin, \'%c/%e/%y\') AS lastlogin,
		IF(a.lastlogin > SUBDATE(NOW(), INTERVAL 7 DAY) , 1, 0) AS loginlastweek,
		(SELECT COUNT(*) FROM runs WHERE user = a.id) AS runs,
		(SELECT COUNT(*) FROM courses WHERE user = a.id) AS courses,
		(SELECT COUNT(*) FROM shoes WHERE user = a.id) AS shoes,
		(SELECT COUNT(*) FROM friends WHERE user = a.id OR friend = a.id) AS friends,
		(SELECT COUNT(*) FROM likes WHERE user = a.id) AS likes,
		(SELECT (runs) + (courses) + (shoes) +  (friends) + (likes) + (loginlastweek)) AS activity
		FROM users AS a
		ORDER BY '.mysql_real_escape_string($mysort).' LIMIT '.
		mysql_real_escape_string($offset).', '.
		mysql_real_escape_string($rows));
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>

	<title>CMS - people</title>
		
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="author" content="Mark Baltrusaitis - josieprogramme.com" />

    <link rel="stylesheet" type="text/css" media="screen, projection" href="<?php echo root(); ?>cms/style.css" />
    
    <script src="<?php echo root(); ?>js/jquery/jquery-1.3.js" type="text/javascript"></script>
    <script src="<?php echo root(); ?>js/jquery/jquery.dimensions.js" type="text/javascript"></script>
    <script src="<?php echo root(); ?>js/jquery/jquery.bgiframe.js" type="text/javascript"></script>
    <script src="<?php echo root(); ?>js/jquery/jquery.tooltip.js" type="text/javascript"></script>
	
    <script src="<?php echo root(); ?>js/prototype-1.6.0.3.js" type="text/javascript"></script>
    <script src="<?php echo root(); ?>js/forms.js" type="text/javascript"></script>
	
</head>

<body>
	<ul id="nav">
		<li><a href="<?php echo root(); ?>">Back to Runner's Medium</a></li>
		<li><a href="<?php echo root(); ?>cms">CMS</a></li>
		<li><a href="<?php echo root(); ?>cms/users">Users</a></li>
	</ul>

	<?php
		messages($error, $message);
	?>
	
	<div id="filter">	

		<form action="" method="get" name="searchform" id="searchform">
			<fieldset>
				<label for="q">Search for ID, Name, Username or Email</label>
				<input name="q" id="q" type="text" class="search" value="<?php echo format($search); ?>" />
				<input type="submit" value="Search" class="button" />
				<br class="clear" />
			    <label for="sortby">Show me</label>
		    	<?php
		    		$path = root().'cms/users?q='.format($search).'&sortby=';
		    	?>
			    <select name="sortby" id="sortby" onChange="document.searchform.submit();"><!-- parent.location='<?php echo $path; ?>'+this.options[this.selectedIndex].value;">-->
					<?php echo arrayToSelect($sortopts, format($sortby)); ?>
			    </select>
		    
			</fieldset>
		</form>
	</div>
	<br class="clear" />
	
	<?php
		if ($doSrch) {
			echo '<h2>'.$count.' users found';
			if ($count > 0) {
				echo ':';
			}
			echo '</h2>';
		} else {
			echo '<h2>'.$count.' users</h2>';
		}
			
			if ($count > 0) {
				echo '<ol id="users">';
				while ($line = $conn->fetchAssoc($result)) :
				
					$theuser = format($line['username']);
					$userid  = format($line['id']);
					
					if (strcmp($line['lastlogin'], '0/0/00') == 0) {
						$line['lastlogin'] = 'never';
					} elseif (strcmp($line['lastlogin'], date("n/j/y")) == 0) {
						$line['lastlogin'] = 'today';
					}
					
					?>
					
					<li>
						<div class="pic">
							<a href="'.profile().$theuser.'"><?php echo $user->getAnyPicture($theuser, $line['picture']); ?></a>
						</div>
						
						<div class="activity">
							<?php echo format($line['activity']); ?>
						</div>
											
						<div class="details">
							<h3>
								<a href="<?php echo profile().$theuser; ?>"><?php echo $theuser; ?></a>
							</h3>

							<strong>Name</strong> <?php echo $line['name']; ?><br />
							<strong>Joined</strong> <?php echo format($line['created']); ?>
							<strong>Last seen</strong> <?php echo format($line['lastlogin']); ?>

							<div class="actions">
								<a title="Reset password" href="<?php echo root().'cms/reset?id='.$userid; ?>">Reset password</a> 
								<a title="Delete user" href="<?php echo root(); ?>cms/delete?action=Delete%20selected&check-<?php echo $userid; ?>=on" class="delete">x</a>
							</div>
							
						</div>
					
					</li>
				
				<?php
				endwhile; // results loop
				echo '</ol>';
				
			}
    	
		// pagination
		$ext->paging(root().'cms/users?q='.format($search).'&sortby='.format($sortby).'&', $page, $max);
	?>
	
</body>
</html>