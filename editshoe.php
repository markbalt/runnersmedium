<?php
/*

Runner's Medium
http://www.runnersmedium.com/

editshoe.php

view & edit shoe info

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('lib/base.php');
$user->signinCheck(signin());
$stop = false;
$prompt = null;

// user
$theid = mysql_real_escape_string($user->ID());

$day   = date('j', time());
$month = date('n', time());
$year  = date('Y', time());

$brand = null;
$model = null;
$price = null;

if (!isset($_GET['id']) || !notempty($_GET['id'])) :
	$error = 'shoe ID not provided';
	$stop = true;
elseif (!is_numeric($_GET['id'])) :
	$error = 'invalid shoe ID';
	$stop = true;
else :

	// id provided
	$shoeid = $_GET['id'];
	
	if (isset($_POST['action']) && $_POST['action'] == 'Save') {
	
		// get post data
		if (isset($_POST['brand'])) {
			$brand = $_POST['brand'];
		}
		
		if (isset($_POST['model'])) {
			$model = $_POST['model'];
		}
		
		if (isset($_POST['price'])) {
			$price = $_POST['price'];
		}
		
		if (isset($_POST['day'])) {
			$day = $_POST['day'];
		}
		
		if (isset($_POST['month'])) {
			$month = $_POST['month'];
		}
		
		if (isset($_POST['year'])) {
			$year = $_POST['year'];
		}
		
		if (isset($_POST['status'])) {
			$retired = $_POST['status'];
		}
	
		// validate
		if (strlen($brand) < 1 || strlen($brand) > 32) {
			$error = 'brand must be between 1 and 32 characters';
		} elseif (strlen($model) > 32) {
			$error = 'model must be less than 32 characters';
		} elseif (notempty($price) && !is_numeric($price)) {
			$error = 'price must be numeric';
		} elseif (!is_numeric($day) || !is_numeric($month) || !is_numeric($year) ) {
			$error = 'invalid purchased date';
		} elseif (!checkdate($month, $day, $year) ) {
			$error = 'your purchase date does not exist';
		} elseif ($retired != '0' && $retired != '1') {
        	$error = 'invalid status response';
    	} else {
		
			// escape
			$sqlbrand   = mysql_real_escape_string($brand);
			$sqlmodel   = (notempty($model)) ? '\''.mysql_real_escape_string($model).'\'' : 'NULL';
			$sqlprice   = (notempty($price)) ? mysql_real_escape_string($price) : 'NULL';
			$sqlretired = mysql_real_escape_string($retired);
			$sqldate    = mysql_real_escape_string("$year-$month-$day");

			// update
			$conn->query("UPDATE shoes SET brand = '$sqlbrand', model = $sqlmodel, price = $sqlprice, purchased = '$sqldate', retired = $sqlretired
						WHERE user = ".mysql_real_escape_string($user->ID())." AND id = ".mysql_real_escape_string($shoeid));
			
			$message = '<a href="'.root().'shoes">Successfully updated shoe.  Click here to see all your shoes.</a>';
		}
	} else {
	
		// select shoe info
		$result = $conn->query('SELECT brand, model, price, retired, UNIX_TIMESTAMP(purchased) AS purchased FROM shoes
								WHERE id = '.mysql_real_escape_string($shoeid).' AND user = '.mysql_real_escape_string($user->ID()));
		
		if ($conn->rowCount($result) > 0) {
		
			// grab data
			$line    = $conn->fetchAssoc($result);
			$brand   = $line['brand'];
			$model   = $line['model'];
			$price   = $line['price'];
			$retired = $line['retired'];
			$date    = $line['purchased'];
			
			// parse date
			if($date)
			{
				$month = date('n', $date);
				$day   = date('j', $date);
				$year  = date('Y', $date);
			}
			
		} else {
			$error = 'shoe not found';
			$stop = true;
		}
	}

endif; // id provided

$title = 'Runner\'s Medium - Edit shoe';
require('header.php');
?>

<div id="content">

	<h2>Edit <?php
		if (!is_null($brand)) {
			echo format($brand);
		}
	?> Shoes</h2>
	<?php messages($error, $message); ?>
		
	<?php if (!$stop) : ?>

		<form action="" method="post" id="newshoe">
			<fieldset>
				<label for="brand">Brand</label>
				<input name="brand" id="brand" type="text" value="<?php echo format($brand); ?>" />
				
				<label for="model">Model</label>
				<input name="model" id="model" type="text" value="<?php echo format($model); ?>" />

				<label for="price">Price</label>
				<input name="price" id="price" type="text" value="<?php
					if (is_numeric($price)) {
						echo number_format(format($price), 2);
					} else {
						echo format($price);
					}
					?>" />
				
				<label for="month">Purchased</label>
				
				<select name="month" id="month">
				<?php echo arrayToSelect($user->getMonths(), format($month)); ?>
				</select>
				<select name="day" id="day">
					<?php echo arrayToSelect($user->getDays(), format($day)); ?>
				</select>
				<select name="year" id="year">
					<?php echo arrayToSelect($user->getYears(), format($year)); ?>
				</select>
				
				<label for="active">Status</label>
				<fieldset class="radio">
					<input name="status" id="active" type="radio" value="0" <?php
						if (!$retired) {
							echo 'checked="checked"';
						} ?> /><label for="active">Active</label>
					<input name="status" id="retired" type="radio" value="1" <?php
						if ($retired) {
							echo 'checked="checked"';
						} ?> /><label for="retired">Retired</label>
				</fieldset>
				
				<input name="action" type="submit" value="Save" class="button" />
			</fieldset>
		</form>

	<?php
		elseif (!is_null($prompt)) :
			echo $prompt;	
		endif; // no errors
	?>
</div>

<?php
require('footer.php');
?>