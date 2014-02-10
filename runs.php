<?php
/*

Runner's Medium
http://www.runnersmedium.com/

runs.php

view & manage runs

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
$result = $conn->query('SELECT COUNT(id) FROM runs WHERE user = '.mysql_real_escape_string($theid));
$count  = $conn->getRow($result);
$max    = ceil($count/$rows);

if ($count > 0) {
	$result = $conn->query('SELECT a.id, a.name, ROUND(a.distance, 2) AS distance, a.comments, a.duration AS time,
		TIME_TO_SEC(a.duration) AS secs,
		a.date AS thedate,
		a.created AS created,
		DATE_FORMAT(a.date, \'%b %e %Y\') AS date,
		weight AS weight,
		SEC_TO_TIME(TIME_TO_SEC(a.duration)/a.distance) AS pace,
		b.name AS type, c.name AS course,
		(SELECT COUNT(*) FROM likes WHERE run = a.id) AS likes
		FROM runs AS a LEFT JOIN (runtypes AS b) ON (a.type = b.id)
		LEFT JOIN (courses AS c) ON (a.course = c.id)
		WHERE a.user = '.$theid.'
		ORDER BY thedate DESC, created DESC LIMIT '.
		mysql_real_escape_string($offset).', '.
		mysql_real_escape_string($rows));
	
	// check for invalid page parameter	
	if ($conn->rowCount($result) == 0) {
		$error = '<a href="'.root().'runs">no entries for that page</a>';
		$stop  = true;
	}
	
} else {
	
	// no runs yet
	$message = '<a href="'.root().'newrun">you have not entered any runs yet.  Click here to log your first run.</a>';
	$stop    = true;
}

$title = 'Runner\'s Medium - My runs';
require('header.php');
?>

<div id="content">
	<?php
		messages($error, $message);
		
		if (!$stop && $conn->rowCount($result) > 0) : ?>
	
		<div class="heading">
			<h2>My Runs</h2>
			<a href="<?php echo root(); ?>newrun">log a new run</a>
		</div>
	
		<div id="main">	
			<table id="feed">
				<colgroup>
					<col class="alt" />
					<col />
					<col class="alt" />
					<col />
				</colgroup>
				<thead>
					<tr>
						<td>Date</td>
						<td>Details</td>
						<td>Times</td>
						<td>Actions</td>
					</tr>
				</thead>
				<tbody>
					<?php
						while ($line = $conn->fetchAssoc($result)) :
						
							// id
							$runid = format($line['id']);
							
							// date
							if ($line['date'] == date('M j Y')) {
								$date = 'today';
							} else {
								$date = substr($line['date'], 0, -5);
							}

							// no zero distance						
							if (notempty($line['distance'])) {
								$dist = format_d($line['distance']);
							} else {
								$dist = 0;
							}
							
							// calories
							if (notempty($line['weight'])) {
								$cals = $user->calcRunCals($dist, $line['secs']/60, $line['weight']);
							} else {
								$cals = null;
							}
						?>
						<tr id="item-<?php echo $runid; ?>">
							<td>
								<h4><?php echo format($date); ?></h4>
								<span class="likes"><?php
								
									if (!empty($line['likes'])) {
										
										// people like this run
										echo format($line['likes']);
										
										if ($line['likes'] > 1) {
											echo ' likes';
										} else {
											echo ' like';
										}
									}
								
								?></span>
							</td>
							<td>
								<em><?php echo $dist; ?></em> <?php echo format($user->getUnits(true)); ?> <span class="type"><?php echo format($line['type']); ?></span> run<br />
								<?php
									// course
									if (!is_null($line['course'])) {
										echo '@'.format($line['course']);
									}
								?>
							</td>
							<td>
								time: <em><?php echo format_t($line['time']); ?></em> <br />
								pace: <em><?php echo format_t($line['pace']); ?></em> per <?php echo format($user->getUnits(true)); ?>
								
								<?php
									// calories
									if (!is_null($cals)) {
										echo '<br />cals: '.format($cals);
									}
								?>
							</td>
							<td class="actions">
								<a href="<?php echo root(); ?>editrun?id=<?php echo $runid; ?>" title="edit">edit</a>
								<a class="delete" onclick="deleteItem('run', <?php echo $runid; ?>)" title="delete">x</a>
							</td>
						</tr>
				
					<?php endwhile; // results loop ?>
				</tbody>
			</table>
		</div>

		<?php
			// pagination
			$ext->paging('runs?', $page, $max);			
		?>
		
	<div id="side"></div>
			
	<?php
		endif; // no results
	?>
</div>

<?php
require('footer.php');
?>