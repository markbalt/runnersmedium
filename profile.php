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
		redirect(profile().$user->username());
	} else {
		redirect(root());
	}
}

$thisPage = $_GET['user'];
$private  = true;
$myFriend = false;

// is this your page?
$myPage = ($user->username() == $thisPage) ? true : false;

// check if user exists
$result = $conn->query('SELECT a.id, a.picture, a.ispublic FROM users AS a WHERE a.username = \''.mysql_real_escape_string($thisPage).'\' LIMIT 1');

if($conn->rowCount($result) > 0) {

	$title = 'Runner\'s Medium - '.$thisPage.'\'s Profile';
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
		$private = true;
	} else {
	
		// show profile
		$private = false;
		
		// select profile data
		$result = $conn->query('SELECT id, ispublic, name, about, location, url, why, gender, picture, units,
			DATE_FORMAT(NOW(), \'%Y\') - DATE_FORMAT(birthday, \'%Y\') - (DATE_FORMAT(NOW(), \'00-%m-%d\') < DATE_FORMAT(birthday, \'00-%m-%d\')) AS age
			FROM users WHERE id = '.$theid.' LIMIT 1');

		// add data to profile component
		$line = $conn->fetchAssoc($result);
		$profile = new profileComponent($thisPage);

		// populate profile data
		foreach ($line as $key => $value) {
			$profile->$key = $value;
		}
		
		// user units preference
		if ($profile->units == '0') {
			$units = 'mi';
		} else {
			$units = 'km';		
		}
		
		// query summary
		$result = $conn->query('SELECT COUNT(id) AS runs, ROUND(SUM(distance), 2) AS total, SEC_TO_TIME(SUM(TIME_TO_SEC(duration))/SUM(distance)) AS pace
			FROM runs WHERE user = '.$theid);
			
		$line  = $conn->fetchAssoc($result);

		// populate summary
		foreach ($line as $key => $value) {
			$profile->$key = $value;
		}
		
		// query friends
		$friends = $conn->query('SELECT b.username AS username, b.picture AS picture FROM friends AS a JOIN users AS b on a.friend = b.id
			WHERE user = '.$theid.' AND state = 2
			UNION SELECT b.username AS username, b.picture AS picture FROM friends AS a JOIN users AS b ON a.user = b.id
			WHERE friend = '.$theid.' AND state = 2 LIMIT 6');

		if ($signedin) {
			$curuser = mysql_real_escape_string($user->ID());
		} else {
			$curuser = 'NULL';
		}

		// query recent activity
		$myruns = $conn->query('SELECT 0 AS liked, a.id, a.name, ROUND(a.distance, 2) AS distance, a.comments, a.duration AS time,
			TIME_TO_SEC(a.duration) AS secs,
			a.date AS thedate,
			a.created AS created,
			DATE_FORMAT(a.date, \'%b %e %Y\') AS date,
			SEC_TO_TIME(TIME_TO_SEC(a.duration)/a.distance) AS pace,
			b.name AS type, c.name AS course,
			IF((SELECT id FROM likes WHERE run = a.id AND user = '.$curuser.'), 1, 0) AS liked
			FROM runs AS a LEFT JOIN (runtypes AS b) ON (a.type = b.id)
			LEFT JOIN (courses AS c) ON (a.course = c.id)
			WHERE a.user = '.$theid.'
			ORDER BY thedate DESC, created DESC LIMIT 5');
		}
	
} else {
	require('notfound.php');
	exit;
}

$thisPage = format($thisPage);
$body     = 'id="profile"';
require('header.php');
?>

<div id="content">
	<?php
		messages($error, $message);
		
		if(!$private) :
			?>
			<div id="main">

				<div class="pic">
				<?php
					// show pic
					echo $profile->showPicture();
				?>
				</div>
				
				<div id="myusername">
					<h2><?php echo $thisPage; ?></h2>
				</div>
				<br class="clear" />
				
				<?php if ($profile->runs > 0) :
					// user has logged a run
				?>
					<div class="snapshot">
						I have logged <em><?php echo format($profile->runs); ?></em> run<?php if ($profile->runs != 1) {
							echo's';
						} ?> for a total of
						<em><?php echo format_d($profile->total); ?></em> <?php echo $units; ?>.  My average pace is
						<em><?php echo format_t($profile->pace) ?></em> per <?php echo $units; ?>.
					</div>
					
					<h3>Recent Activity</h3>
					
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
								while ($line = $conn->fetchAssoc($myruns)) :
								
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
								?>
								<tr id="item-<?php echo $runid; ?>">
									<td>
										<h4><?php echo format($date); ?></h4>
									</td>
									<td>
										<em><?php echo $dist; ?></em> <?php echo format($user->getUnits(true)); ?> <span class="type"><?php echo format($line['type']); ?></span> run
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
						
							<?php endwhile; // results loop ?>
						</tbody>
					</table>
									
				<?php
				endif; // runs logged
			?>
		</div>
		
		<div id="side">		
			<div id="info">
				<?php
					if (notempty($profile->name)) {		
						echo '<strong>Name</strong> '.format($profile->name).'<br />';
					}
			
					if (notempty($profile->age)) {		
						echo '<strong>'.format($profile->age).'</strong> year old runner ';
					}
			
					if (notempty($profile->location)) {
						echo 'from <strong>'.format($profile->location).'</strong><br />';
					} elseif (notempty($profile->age)) {
						echo '<br />';
					}
			
					if (notempty($profile->about)) {
						echo '<strong>Bio</strong> '.format($profile->about).'<br />';
					}
					
					if (notempty($profile->why)) {
						echo '<strong>I run because</strong> '.format($profile->why).'<br />';
					} elseif (notempty($profile->about)) {
						echo '<br />';
					}
					
					if (notempty($profile->url)) {
						
						// insert http if not there
						if (preg_match('/^http:\/\//', $profile->url) == 0) {
							$profile->url = 'http://'.$profile->url;
						}
						
						echo '<strong>Web</strong> <a href="'.format($profile->url).'" rel="external nofollow">My Site</a>';
					}
				?>
	
			</div>
		
			<?php
			if ($signedin) {
					echo '<div id="actions">';
					
					echo '<h3>Actions</h3>';
						echo '<ul>';
						
						if ($myPage) {
							// my profile
							echo '<li><a href="'.root().'settings/editprofile">edit my profile</a></li>';
						} elseif ($myFriend) {
							// my friend
							echo '<li id="nudge"><a onclick="nudge('.format($theid).')">nudge '.$thisPage.'</a></li>';
							//echo '<li><a href="'.root().'newmessage?to=$thisPage">message '.$thisPage.'</a></li>';
						} else {
							// friend actions
							echo '<li id="addtofriends"><a onclick="addFriend('.format($theid).')">add '.$thisPage.' to my friends</a></li>';
						}
						echo '</ul>';
					echo '</div>';
				}
			?>
			
			<div id="people">
			<?php
				$friendCount = $conn->rowCount($friends);	
				if ($friendCount > 0) {
					
					// show friends
					echo '<div class="heading">';
						echo '<h2>Running Friends</h2>';
						echo '<a class="seeall" href="'.root().$thisPage.'/friends">('.format($friendCount).') see all</a>';
					echo '</div>';
					
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
	
				}
			?>
			</div>
		</div>
		
	<?php
		else:
		?>

			<div id="main">
				<div class="pic">
					<?php
						// show pic
						echo $user->getAnyPicture($thisPage, $line['picture']);
					?>
				</div>
					
				<div id="myusername"><h2><?php echo $thisPage; ?></h2></div>
				<div class="private">
					This profile is private.
				</div>
			</div>
			<div id="side">
				<?php
					if ($signedin) {
						echo '<div id="actions">';
						
						echo '<h3>Actions</h3>';
							echo '<ul>';
							
							if ($myPage) {
								// my profile
								echo '<li><a href="'.root().'settings/editprofile">edit my profile</a></li>';
							} elseif ($myFriend) {
								// my friend
								echo '<li id="nudge"><a onclick="nudge('.format($theid).')">nudge '.$thisPage.'</a></li>';
								//echo '<li><a href="'.root().'newmessage?to=$thisPage">message '.$thisPage.'</a></li>';
							} else {
								// friend actions
								echo '<li id="addtofriends"><a onclick="addFriend('.format($theid).')">add '.$thisPage.' to my friends</a></li>';
							}
							echo '</ul>';
						echo '</div>';
					}
				?>
			</div>

		<?php
		endif; // no error
	?>
	
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