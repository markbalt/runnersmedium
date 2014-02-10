<?php
/*

Runner's Medium
http://www.runnersmedium.com/

courses.php

view & manage courses

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('lib/base.php');
$user->signinCheck(signin());
$stop = false;

// user
$theid = $user->ID();

// display a message?
if (isset($_SESSION['msg']) && $_SESSION['msg'] != '') {
	$message = $_SESSION['msg'];
	
	// don't show this message again
	unset($_SESSION['msg']);
}

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
$result = $conn->query('SELECT COUNT(id) FROM courses WHERE user = '.mysql_real_escape_string($theid));
$count  = $conn->getRow($result);
$max    = ceil($count/$rows);

if ($count > 0) {
	$result = $conn->query('SELECT a.id, a.name, ROUND(a.distance, 2) AS distance, a.city, a.comments, b.username,
		(SELECT COUNT(id) FROM runs WHERE course = a.id AND user = '.$theid.') AS runs,
		(SELECT ROUND(SUM(distance), 2) FROM runs WHERE course = a.id AND user = '.$theid.') AS total,
		(SELECT SEC_TO_TIME(AVG(TIME_TO_SEC(duration))) FROM runs WHERE course = a.id AND user = '.$theid.') AS ave,
		(SELECT date FROM runs WHERE course = a.id AND user = '.$theid.' ORDER BY date DESC LIMIT 1) AS lastran
		FROM courses AS a LEFT JOIN users AS b ON (a.user = b.id) WHERE user = '.$theid.' ORDER BY lastran DESC LIMIT '.
		mysql_real_escape_string($offset).', '.
		mysql_real_escape_string($rows));

	// check for invalid page parameter	
	if ($conn->rowCount($result) == 0) {
		$error = '<a href="'.root().'courses">no entries for that page</a>';
		$stop  = true;
	}
	
} else {
	
	// no runs yet
	$message = '<a href="'.root().'newcourse">you have not entered any courses yet.  Click here to map your first course.</a>';
	$stop    = true;
}

$title = 'Runner\'s Medium - Courses';
require('header.php');
?>

<div id="content">
	<?php
		messages($error, $message);
		
		if (!$stop && $conn->rowCount($result) > 0) :
	?>
		
		<div class="heading">
			<h2>My Courses</h2>
			<a href="<?php echo root(); ?>newcourse">map a new course</a>
		</div>
		
		<div id="main">
			<table id="feed">
				<colgroup>
					<col />
					<col class="alt" />
					<col />
				</colgroup>
				<thead>
					<tr>
						<td>Name &amp; location</td>
						<td>Details</td>
						<td>Actions</td>
					</tr>
				</thead>
				<tbody>
					<?php
						while ($line = $conn->fetchAssoc($result)) :
						
							// id
							$courseid = format($line['id']);
							
							// others						
							if (notempty($line['distance'])) {
								$dist = format_d($line['distance']);
							} else {
								$dist = 0;
							}
							
							if (empty($line['total'])) {
								$total = 0;
							} else {
								$total = format_d($line['total']);
							}
							
							if ($total > 0) {
								$ave = format_t($line['ave']);
							} else {
								$ave = 0;
							}
							
							if ($line['runs'] == 1) {
								$ranit = format($line['runs']).' run';
							} else {
								$ranit = format($line['runs']).' runs';
							}
						?>
	
						<tr id="item-<?php echo $courseid; ?>">
							<td>
								<h4><?php echo format($line['name']); ?></h4>
								
								<?php echo format($line['city']); ?>
							</td>
							
							<td>
								distance: <em><?php echo $dist; ?></em> <?php echo format($user->getUnits(true)); ?><br />
								<?php echo $ranit; ?>  for <?php echo $total; ?> <?php echo $user->getUnits(true); ?><br />
								ave time completed: <?php echo $ave; ?><br />
								created by: <em><?php echo $line['username']; ?></em>
							</td>
							
							<td class="actions">
								<a href="<?php echo root(); ?>newrun?course=<?php echo $courseid; ?>">run it</a>
								<a href="<?php echo root(); ?>editcourse?id=<?php echo $courseid; ?>">edit</a>
								<a class="delete" onclick="deleteItem('course', <?php echo $courseid; ?>)">x</a>
							</td>
						</tr>
					
					<?php endwhile; // results loop ?>
				</tbody>
			</table>
		</div>

		<?php
			// pagination
			$ext->paging('courses?', $page, $max);			
		?>
		
	<div id="side"></div>
			
	<?php
		endif; // no results
	?>
</div>

<?php
require('footer.php');
?>