<?php
/*

Runner's Medium
http://www.runnersmedium.com/

feed.php

public feed for runs from all users

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('lib/base.php');

$signedin = $user->signinCheck();

if ($signedin) {
	$curuser = mysql_real_escape_string($user->ID());
} else {
	$curuser = 'NULL';
}

// query public feed
$result = $conn->query('SELECT a.id AS runid, a.name, ROUND(a.distance, 2) AS distance, a.duration AS time,
	TIME_TO_SEC(a.duration) AS secs,
	a.date AS thedate,
	a.created AS created,
	DATE_FORMAT(a.date, \'%b %e %Y\') AS date,
	SEC_TO_TIME(TIME_TO_SEC(a.duration)/a.distance) AS pace,
	IF((SELECT id FROM likes WHERE run = a.id AND user = '.$curuser.'), 1, 0) AS liked,
	b.name AS type, c.name AS course, d.username, d.units
	FROM runs AS a LEFT JOIN (runtypes AS b) ON (a.type = b.id)
	LEFT JOIN (courses AS c) ON (a.course = c.id)
	JOIN (users AS d) ON (a.user = d.id)
	WHERE d.ispublic = 1
	ORDER BY thedate DESC, created DESC LIMIT 20');

// most active users TODO : change this to query days run rather than number of runs
$active = $conn->query('SELECT b.username, b.picture, COUNT(*) AS runs FROM runs AS a JOIN users AS b ON (a.user = b.id)
	WHERE b.ispublic = 1 GROUP BY user ORDER BY runs DESC LIMIT 6');

$title = 'Runner\'s Medium - Public feed';

require('header.php');
?>

<div id="content">
	<div id="main">
		<h2>Latest Runs From Everyone</h2>

		<?php if ($conn->rowCount($result) > 0) : ?>

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
							<tr id="item-<?php echo $runid; ?>" class="notme">
								
								<td>
									<h4><a href="<?php echo profile().$theuser; ?>"><?php echo $theuser; ?></a></h4>
								</td>
							
								<td>
									ran a <em><?php echo $dist; ?></em> <?php echo $units; ?> <span class="type"><?php echo format($line['type']); ?></span> run
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
									<a <?php if ($signedin) {
											echo 'onclick="like('.$runid.')"';
										} else {
											echo 'href="'.root().'signin"';
										} ?> class="like<?php if ($signedin && $line['liked']) {
											echo ' liked';
										} ?>" id="like-<?php echo $runid; ?>"><?php if ($signedin && $line['liked']) {
											echo 'unlike';
										} else {
											echo 'like';
										}?> this run</a>
								</td>
							</tr>
					
						<?php
							endwhile; // results loop
						?>
					</tbody>
				</table>
				
		<?php
			else :
				echo '<div id="prompt">There are no runs yet</div>';
			endif; // runs logged
		?>
				
	</div>

	<div id="side">
	
	<h3>Most Active Members</h3>
		<div id="people">
		<?php		
			if ($conn->rowCount($active) > 0) {
				while($line = $conn->fetchAssoc($active)) {

					// set to default if picture path is null
					if (is_null($line['picture'])) {
						$picture = root().DEFAULT_PIC;
					} else {
						$picture = root().PIC_DIR.format($line['picture']);
					}
					
					$theuser = format($line['username']);
					
					echo '<a href="'.profile().$theuser.'"><img src="'.$picture.'" title="'.$theuser.'" alt="'.$theuser.'" class="picture" /></a>';
				}
			}
		?>
		</div>
	</div>
</div>

<?php

$scripts = <<<EOD

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