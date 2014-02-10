<?php
/*

Runner's Medium
http://www.runnersmedium.com/

shoes.php

view & manage shoes

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('lib/base.php');
$user->signinCheck(signin());
$stop = false;

// user
$theid = mysql_real_escape_string($user->ID());

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

$showall = false;
$showsql = null;

// selected page
if(isset($_GET['page']) && is_numeric($_GET['page']))  {
	$page = $_GET['page'];
}

// showall
if (isset($_GET['showall'])) {
	if ($_GET['showall'] == 'true') {
		$showall = true;
	}
}

// offset
$offset = ($page - 1) * $rows;

// show retired?
if ($showall) {
	$onshowall = "onclick=\"parent.location='".root()."shoes?showall=false'\"";
	$url = root().'shoes?showall=true&';
} else {
	$showsql = ' AND retired = 0 ';
	$onshowall = "onclick=\"parent.location='".root()."shoes?showall=true'\"";
	$url = root().'shoes?showall=false&amp;';
}

// calc total pages
$line  = $conn->fetchAssoc($conn->query('SELECT COUNT(id) AS total, (SELECT COUNT(id) FROM shoes WHERE user = '.$theid.$showsql.') AS this_query  FROM shoes WHERE user = '.$theid));
$count = $line['this_query'];
$max   = ceil($count/$rows);

$totalShoes = $line['total'];

if ($count > 0) {
	$result = $conn->query('SELECT id, CONCAT(IFNULL(brand, \'\'), \' \', IFNULL(model, \'\')) AS shoe, price, purchased, DATE_FORMAT(purchased, \'%c/%d/%y\') AS date, IF(retired=1,\'retired\',\'active\') AS status,
		(SELECT ROUND(SUM(distance), 2) FROM runs WHERE user = '.$theid.' AND shoe = shoes.id) AS distance,
		ROUND(shoes.price/(SELECT SUM(distance) FROM runs WHERE user = '.$theid.' AND shoe = shoes.id), 2) AS eff
		FROM shoes WHERE user = '.$theid.$showsql.'
		ORDER BY purchased DESC LIMIT '.
		mysql_real_escape_string($offset).', '.
		mysql_real_escape_string($rows));
		
	// check for invalid page parameter	
	if ($conn->rowCount($result) == 0) {
		$error = '<a href="'.root().'shoes">no entries for that page</a>';
		$stop  = true;
	}
	
} else {
	
	// no runs yet
	$message = '<a href="'.root().'newshoe">you do not have any active shoes.  Click here to enter your shoes</a>';
	
	if ($totalShoes > 0) {
		$message .= ' or <a href="'.root().'shoes?showall=true">click here to see your retired shoes</a>';
	}
	
	$stop    = true;
}
$title = 'Runner\'s Medium - Shoes';
require('header.php');
?>

<div id="content">
	<?php
		messages($error, $message);
		
		if (!$stop && $conn->rowCount($result) > 0) :
	?>
		<div class="heading">
			<h2>My Shoes</h2>
			<a href="<?php echo root(); ?>newshoe">enter new shoes</a>
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
						<td>Shoe</td>
						<td>Details</td>
						<td>Actions</td>
					</tr>
				</thead>
				<tbody>
					<?php
						while ($line = $conn->fetchAssoc($result)) :
						
							// id
							$shoeid = format($line['id']);
							$status = format($line['status']);
	
							// others							
							if (notempty($line['price'])) {
								$price = 'for $'.number_format(format($line['price']), 2);
							} else {
								$price = null;
							}
							
							if (notempty($line['distance'])) {
								$dist = format_d($line['distance']);
							} else {
								$dist = 0;
							}
							
							if (notempty($line['eff'])) {
								$eff = '$'.format($line['eff']);
							} else {
								$eff = '$0';
							}
						?>
					
						<tr id="item-<?php echo $shoeid; ?>">
							<td>
								<h4><?php echo format($line['shoe']); ?></h4>
								<span class="status" id="status-<?php echo $shoeid; ?>"><?php echo $status ?></span>
							</td>
							<td>
								<em><?php echo $dist; ?></em> <?php echo format($user->getUnits(true)); ?><br />
								purchased on <?php echo format($line['date']); ?> <?php echo $price; ?><br />
								efficiency: <?php echo $eff.' '.$user->getUnits(true); ?>
							</td>
							
							<td class="actions">
								<?php
									if ($status == 'active') {
										echo '<a id="retirebtn-'.$shoeid.'" onclick="retire('.$shoeid.')">retire</a>';
									} else {
										echo '<a id="retirebtn-'.$shoeid.'" onclick="retire('.$shoeid.')">un-retire</a>';
									}
								?>
								<a href="<?php echo root(); ?>editshoe?id=<?php echo $shoeid; ?>">edit</a>
								<a class="delete" onclick="deleteItem('shoe', <?php echo $shoeid; ?>)">x</a>
							</td>
						</tr>
					
					<?php endwhile; // results loop ?>
				</tbody>
			</table>
		</div>

		<?php
			// pagination
			$ext->paging($url, $page, $max);			
		?>
		
		<div id="side">
			<?php if ($totalShoes > 0) :
				?>
				<div class="filter">
					<fieldset>
						<?php if ($showall) {
							echo '<input id="showretired" type="checkbox" class="check single" checked="checked" '.$onshowall.' />';
						} else {
							echo '<input id="showretired" type="checkbox" class="check single" '.$onshowall.' />';
						} ?>
						<label for="showretired" class="check">Show retired footwear</label>
					</fieldset>
				</div>
			<?php endif; // any shoes ?>
		</div>
			
	<?php
		endif; // no results
	?>
</div>
	
<?php
require('footer.php');
?>