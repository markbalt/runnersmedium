<?php
/*

Runner's Medium
http://www.runnersmedium.com/

picture.php

view, update or reset user profile picture/avatar

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('lib/base.php');

$user->signinCheck(signin());
$picture = new ImageHelper();

// select current profile data
$result = $conn->query('SELECT picture FROM users WHERE id = '.mysql_real_escape_string($user->ID()).' LIMIT 1');

if ($conn->rowCount($result) == 0) {
	$error = 'User session error';
} else {
	$line = $conn->fetchAssoc($result);
	$picturePath = $line['picture'];
}
	
// check for submit action
if (isset($_POST['action']) && $_POST['action'] == 'Upload It') {

	// get correct global - this can probably be removed - only applies for php versions < 4.1.0
	if (!isset($_FILES) && isset($HTTP_POST_FILES)) {
		$_FILES = $HTTP_POST_FILES;
	}
	
	// is image selected?
	if (!isset($_FILES['thefile']['name']) || $_FILES['thefile']['name'] == '') {
		$error = 'image not found';
	} else {
		
		$picture->setTempName($_FILES['thefile']['tmp_name']);
		$picture->setUploadName(basename($_FILES['thefile']['name']));
		$picture->setFileType($_FILES['thefile']['type']);

		// check type and resize the picture
		if (filesize($_FILES['thefile']['tmp_name']) > MAX_PIC_SIZE) {
			$error = 'maximum picture size is 2MB';
		} elseif (!$picture->checkType()) {
			$error = $picture->showError();
		} elseif (!$picture->resizeCrop(PIC_WIDTH, PIC_HEIGHT)) {
			$error = $picture->showError();
		} elseif (!$picture->moveFile(PIC_DIR)) {
			$error = $picture->showError();
		} else {
		
			// remove previous picture if one exists
			if ($picturePath) {
				$picture->removeFile(PIC_DIR.$picturePath);
			}
		
			// file upload was successful
			$picturePath = mysql_real_escape_string($picture->get());
				
			// update user data
			$conn->query('UPDATE users SET picture = \''.$picturePath.'\' WHERE id = '.mysql_real_escape_string($user->ID()).' LIMIT 1');
			$message = 'your profile picture has been saved';
		}
	}
}

if (isset($_POST['action']) && $_POST['action'] == 'Reset my picture') {

	// remove previous picture if one exists
	if ($picturePath) {
		$picture->removeFile(PIC_DIR.$picturePath);
	}
	
	// reset picture
	$picturePath = null;

	$conn->query('UPDATE users SET picture = NULL WHERE id = '.mysql_real_escape_string($user->ID()).' LIMIT 1');
	$message = 'your profile picture was reset';
}

// set to default if picture path is null
if (is_null($picturePath)) {
	$picturePath = root().DEFAULT_PIC;
} else {
	$picturePath = root().PIC_DIR.$picturePath;
}

$title = 'Runner\'s Medium - Picture';
require('header.php');
?>

<div id="content">

	<ul id="tabnav">
		<li><a href="<?php echo root(); ?>settings/account">Account</a></li>
		<li><a href="<?php echo root(); ?>settings/editprofile">Edit Profile</a></li>
		<li><a class="select" href="<?php echo root(); ?>settings/picture">Picture</a></li>			
		<li><a href="<?php echo root(); ?>settings/password">Password</a></li>
		<li><a href="<?php echo root(); ?>settings/notices">Notices</a></li>
	</ul>
	<br class="clear" />

	<?php messages($error, $message); ?>
			
	<div class="section">
		<img src="<?php echo $picturePath; ?>" alt="<?php echo format($user->username()) ?>'s picture" class="picture" />
	</div>
	
	<div class="section">
		<form action="" method="post" id="pictureform" enctype="multipart/form-data">
			<fieldset>
				<label for="thefile">Choose a picture</label>
				<input type="file" name="thefile" id="thefile" class="file" />
				<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_PIC_SIZE; ?>" />
				<div class="comment">Maximum size of 2MB. JPG, GIF or PNG</div>
				
				<input name="action" type="submit" value="Upload It" class="button inline" />
				<input name="action" type="submit" value="Reset my picture" class="button inline" />
			</fieldset>
		</form>
	</div>
	<br class="clear" />

</div>

<?php
require('footer.php');
?>