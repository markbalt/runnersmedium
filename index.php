<?php
/*

Runner's Medium
http://www.runnersmedium.com/

index.php

simple index page with site tour, most active members and community stats

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('lib/base.php');

// redirect to home?
if ($user->signinCheck()) {
    redirect(home());
}

// get site stats
$stats = $conn->query('SELECT COUNT(*) AS runs, ROUND(SUM(distance), 2) AS miles FROM runs');

// most active users TODO : change this to query days run rather than number of runs
$active = $conn->query('SELECT b.username, b.picture, COUNT(*) AS runs FROM runs AS a JOIN users AS b ON (a.user = b.id)
	WHERE b.ispublic = 1 GROUP BY user ORDER BY runs DESC LIMIT 6');

$title = 'Runner\'s Medium - Run Like Crazy';
require('header.php');

?>
        
<div id="content">
	
	<div id="main">
		
		<p id="welcome">
			Welcome to Runner's Medium, the simple and powerful online running journal.  Sign into the latest running community - just like running, it's both free and good for you.  <a href="<?php echo root(); ?>signup" >Join &raquo;</a>
		</p>
		<a href="<?php echo root(); ?>signup" id="tour">Join</a>
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
				
		<h3>Community Stats</h3>
		<?php
			if ($conn->rowCount($stats) > 0) {
				$line = $conn->fetchAssoc($stats);
				
				// community snapshot				
				echo '<div class="snapshot"><em><span id="runs">'.format($line['runs']).'</span> runs logged</em> for <em><span id="miles">'.format_d($line['miles']).'</span> miles</em>.</div>';
			}
		?>
		
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

new PeriodicalExecuter(refreshSnap, 2);
</script>

EOD;

require('footer.php');
?>
