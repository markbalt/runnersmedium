<?php
/*

Runner's Medium
http://www.runnersmedium.com/

newshoe.php

add new shoe

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('lib/base.php');
$user->signinCheck(signin());
$stop = false;
$prompt = null;

$day   = date('j', time());
$month = date('n', time());
$year  = date('Y', time());

$brand = $model = $price = null;
$retired = 0;

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

		// insert
		$conn->query("INSERT INTO shoes (user, brand, model, price, purchased, retired)
					VALUES (".mysql_real_escape_string($user->ID()).", '$sqlbrand', $sqlmodel, $sqlprice, '$sqldate', $sqlretired)");
					
		// get the new shoe id
		$result = $conn->query("SELECT id FROM shoes WHERE id = LAST_INSERT_ID() LIMIT 1");
        
        if ($conn->rowCount($result) == 1) {
			
			// get the new course id
			$line = $conn->fetchAssoc($result);
	        
			// redirect on success
			redirect(root().'shoes', '<a href="'.root().'newrun?shoes='.format($line['id']).'">Added your shoes.  Click here to run them.</a>');
			
		} else {
            $error = 'there was an error creating the shoes';
        }
	}
}

$title = 'Runner\'s Medium - New Shoe';
require('header.php');
?>

<div id="content">

	<h2>New Shoes</h2>
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