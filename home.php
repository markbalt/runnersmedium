<?php
/*

Runner's Medium
http://www.runnersmedium.com/

home.php

user home shows snapshot, running feed, current friends

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('lib/base.php');
$user->signinCheck(signin());

// user
$units = $user->getUnits(true);
$theid = mysql_real_escape_string($user->ID());

// TODO: display a message?
if (isset($_SESSION['msg']) && $_SESSION['msg'] != '') {
	$message = $_SESSION['msg'];
	
	// don't show this message again
	unset($_SESSION['msg']);
}

// query summary
$result = $conn->query('SELECT COUNT(id) AS runs, ROUND(SUM(distance), 2) AS total, SEC_TO_TIME(SUM(TIME_TO_SEC(duration))/SUM(distance)) AS pace
	FROM runs WHERE user = '.$theid);

$line  = $conn->fetchAssoc($result);
$runs  = $line['runs'];
$total = $line['total'];
$pace  = $line['pace'];

if ($runs == 1) {
	$what = 'run';
} else {
	$what = 'runs';
}

// query friends
$friends = $conn->query('SELECT b.username AS username, b.picture AS picture FROM friends AS a JOIN users AS b on a.friend = b.id
	WHERE user = '.$theid.' AND state = 2
	UNION SELECT b.username AS username, b.picture AS picture FROM friends AS a JOIN users AS b ON a.user = b.id
	WHERE friend = '.$theid.' AND state = 2 LIMIT 6');

// query recent feed
$feed = $conn->query('SELECT a.id AS runid, a.distance, DATE_FORMAT(a.date, \'%b %e %Y\') AS date, a.duration AS time, b.username, b.units, b.id, b.picture, c.name AS type, d.name AS course,
	IF((SELECT id FROM likes WHERE run = a.id AND user = '.mysql_real_escape_string($user->ID()).'), 1, 0) AS liked
	FROM runs AS a JOIN users AS b ON a.user = b.id
	JOIN runtypes AS c ON a.type = c.id LEFT JOIN courses AS d ON a.course = d.id
	WHERE a.user IN (SELECT friend AS user FROM friends WHERE user = '.$theid.' AND state = 2
	UNION SELECT user AS user FROM friends WHERE friend = '.$theid.' AND state = 2)
	OR a.user = '.$theid.' ORDER BY a.date DESC LIMIT '.mysql_real_escape_string(MAX_RECENT_RESULTS));

// inbox items... do not show nudges older than 30 days
$notices = $conn->query('SELECT \'request\' AS type, a.id, a.created, b.username FROM friends AS a JOIN users AS b ON a.user = b.id
	WHERE a.friend = '.$theid.' AND a.state = 1
	UNION SELECT \'nudge\' AS type, a.id, a.created, b.username FROM nudges AS a JOIN users AS b ON a.nudger = b.id
	WHERE a.nudgee = '.$theid.' AND a.state IN (1, 2) AND a.created > SUBDATE(NOW(), INTERVAL 30 DAY) ORDER BY created');

$title = 'Runner\'s Medium - '.$user->username().'\'s Home';
require('header.php');
?>

<div id="content">
	<?php
		messages($error, $message);
	?>
	<div id="main">
	
		<div class="snapshot">
			I have logged <em><?php echo format($runs); ?></em> run<?php if ($runs != 1) {
				echo's';
			} ?> for a total of
			<em><?php echo format_d($total); ?></em> <?php echo $units; ?>.  My average pace is
			<em><?php echo format_t($pace) ?></em> per <?php echo $units; ?>.
		</div>
		
		<?php
			if ($conn->rowCount($notices) > 0 || $conn->rowCount($feed) > 0) {
				
				// show activity and inbox nav
				?>
				<ul id="tabnav">
					<li><a class="select" href="#activity" >Activity</a></li>
					<li><a href="#inbox">Inbox (<span id="inbox-count"><?php echo format($conn->rowCount($notices)); ?></span>)</a></li>
				</ul>
				<br class="clear" />
				<?php
			}

			// inbox		
			if ($conn->rowCount($notices) > 0) {
				
				// show requests
				echo '<div id="inbox"><ul id="requests">';
				while($line = $conn->fetchAssoc($notices)) {
					
					$theuser = format($line['username']);
					$theid   = format($line['id']);
					
					if ($line['type'] == 'request') {
						echo '<li id="request-'.$theid.'"><span id="request-user-'.$theid.'"><a href="'.profile().$theuser.'">'.$theuser.'</a></span> would like to be your friend.  <a onclick="acceptFriend('.$theid.')">accept</a> or <a onclick="denyFriend('.$theid.')">deny</a></li>';
					} elseif ($line['type'] == 'nudge') {
						echo '<li id="nudge-'.$theid.'"><span id="nudge-user-'.$theid.'"><a href="'.profile().$theuser.'">'.$theuser.'</a></span> nudged you.  <a onclick="dismissNudge('.$theid.')">dismiss</a>';						
					}
				}
				echo '</ul></div>';
			}

			// activity feed
			if ($conn->rowCount($feed) > 0) { ?>
				<div id="activity">
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
								while ($line = $conn->fetchAssoc($feed)) :
								
									// id
									$runid   = format($line['runid']);
									$theuser = format($line['username']);
									
									// user units preference
									if ($line['units'] == '0') {
										$units = 'mi';
									} else {
										$units = 'km';
									}
									
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
								?>
								<tr id="item-<?php echo $runid; ?>"<?php
																
									if ($line['id'] != $user->ID()) {
										echo ' class="notme"';
									}
									
									?>>
									
									<?php
										if ($line['id'] == $user->ID()) {
											echo '<td><h4>'.format($date).'</h4></td>';
										} else {
											echo '<td><h4><a href="'.profile().$theuser.'">'.$theuser.'</a></h4></td>';
										}
									?>
								
									<td>
										<?php
											if ($line['id'] != $user->ID()) {
												echo 'ran a ';
											}
										?>
										
										<em><?php echo $dist; ?></em> <?php echo $units; ?> <span class="type"><?php echo format($line['type']); ?></span> run
										<?php
											// course
											if (!is_null($line['course'])) {
												echo '@'.format($line['course']);
											}
										?>
									</td>
									<td>
										time: <em><?php echo format_t($line['time']); ?></em> <br />
									</td>
									<td>
										<a onclick="like(<?php echo $runid; ?>)" class="like<?php if ($line['liked']) {
											echo ' liked';
										} ?>" id="like-<?php echo $runid; ?>"><?php if ($line['liked']) {
											echo 'unlike';
										} else {
											echo 'like';
										}?> this run</a>
									</td>
								</tr>
						
							<?php endwhile; // results loop ?>
						</tbody>
					</table>
				</div>
			<?php
			} // has feed
		?>

	</div>
	
	<div id="side">
	
		<div id="people">
		<?php
			$friendCount = $conn->rowCount($friends);	
			if ($friendCount > 0) {
				
				// show friends
				echo '<div class="heading"><h2>Running Friends</h2>';
				echo '<a href="'.root().'friends">('.format($friendCount).') see all</a></div>';
				
				while($line = $conn->fetchAssoc($friends)) {

					// set to default if picture path is null
					if (is_null($line['picture'])) {
						$picture = root().DEFAULT_PIC;
					} else {
						$picture = root().PIC_DIR.format($line['picture']);
					}
					
					$theuser = format($line['username']);
					
					echo '<a href="'.profile().$theuser.'"><img src="'.$picture.'" title="'.$theuser.'" alt="'.$theuser.'" class="picture" /></a>';
				}

			} else {
				// no friends yet
				echo '<div class="heading"><h2>Running Friends</h2>';
				echo '<a href="'.root().'find">find some</a></div>';
			}
		?>
		</div>
		
	</div>
	
</div>

<?php
$scripts = '<script type="text/javascript" src="'.root().'js/tabs.min.js"></script>';

$scripts .= <<<EOD
    
<script type="text/javascript">
jQuery(function() {
	
	jQuery('#people img').tooltip({
		track: true,
		delay: 0,
		showURL: false,
		fade: 250
	});
});
</script>

EOD;
require('footer.php');
?>