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

// styles for table
$styles = array('row', 'alt');

// grab site stats
$stats = $conn->fetchAssoc($conn->query('SELECT (SELECT COUNT(*) FROM users) AS members,
	COUNT(*) AS runs,
	ROUND(SUM(distance), 2) AS miles,
	(SELECT COUNT(*) FROM courses) AS courses,
	(SELECT ROUND(SUM(distance), 2) FROM courses) AS `miles mapped`,
	(SELECT COUNT(*) FROM shoes) AS shoes,
	(SELECT COUNT(*) FROM friends WHERE state = 2) AS friendships,
	(SELECT COUNT(*) FROM likes) AS likes,
	(SELECT COUNT(*) FROM quits) AS quits
	FROM runs'));
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>

	<title>CMS - home</title>
		
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

	<h2>Stats</h2>	
	<table id="stats">
		<thead>
			<th>Metric</th>
			<th>Total</th>
		</thead>
		<tbody>
		<?php
			$i=0;
			foreach ($stats as $key => $value) {
			?>
			
			<tr class="<?php echo $styles[$i++ % 2]; ?>">
				<td><?php echo format($key); ?></td>
				<td><?php echo format($value); ?></td>
			</tr>
			
			<?php } // metrics loop ?>
			
		</tbody>
	</table>
		
</body>
</html>