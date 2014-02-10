<?php
/*

Runner's Medium
http://www.runnersmedium.com/

delete.php

provide feedback and delete user account

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('lib/base.php');

$user->signinCheck(signin());
$showConfirm = false;

if (isset($_POST['action']) && $_POST['action'] == 'Continue...') {   
    
    // pass feedback and show confirm message
    $reason   = $_POST['reason'];
    $feedback = $_POST['feedback'];
    
	if(strlen($feedback) > 140)
	{
		// we don't want to make them any less happy so just truncate the string
		$feedback = substr($feedback, 0, 140);
	}
	
	$showConfirm = true;

} elseif (isset($_POST['action']) && $_POST['action'] == 'Confirm') {

	// remove account, sign out and redirect to index
    $reason   = $_POST['reason'];
    $feedback = $_POST['feedback'];
    
	if(strlen($feedback) > 140)
	{
		// we don't want to make them any less happy so just truncate the string
		$feedback = substr($feedback, 0, 140);
	}
	
	// see if the user has a picture
	$result = $conn->query('SELECT picture FROM users WHERE id = '.mysql_real_escape_string($user->ID()).' LIMIT 1');

	// remove the image if so
	if ($conn->rowCount($result) > 0) {
		$line = $conn->fetchAssoc($result);
		$picturePath = $line['picture'];
		
		// remove picture
		$picture = new ImageHelper();
		$picture->removeFile(PIC_DIR.$picturePath);
	}
	
	$reason   = mysql_real_escape_string($reason);
	$feedback = mysql_real_escape_string($feedback);
	$username = mysql_real_escape_string($user->username());
	
	// insert quit and call delete user to remove all references
	$conn->query("INSERT INTO quits (username, reasoncode, feedback) VALUES ('$username', '$reason', '$feedback')");
	$conn->deleteUser($user->ID());
	$user->signOut(root());
}
require('header.php');
?>

<div id="content">
	<?php messages($error, $message); ?>
	
		<?php 
		if ($showConfirm) {
			?>
			<h2>Delete Account</h2>
			<div class="comment">Are you sure you want to delete your Runner's Medium account?
				<p><strong>NOTICE:</strong> Your account will be immediately and completely removed. You cannot undo this action.  Thanks for using Runner's Medium!</p>
			</div>
			<form action="" method="POST" id="deleteform">
				<fieldset>
					<input name="reason" type="hidden" value="<?php echo format($reason); ?>" />
					<input name="feedback" type="hidden" value="<?php echo format($feedback); ?>" />
					<input value="Confirm" name="action" type="submit" class="button inline" />
					<input value="Cancel" type="button" class="button inline" onClick="parent.location='<?php echo root(); ?>'" />
				</fieldset>
			</form>
			<?php
		} else {
		?>
			<div class="fieldset tabnav">
				<h2>Delete Account</h2>
				<div class="comment">Would you like to leave Runner's Medium?  If you like, tell us why and click "Continue" below to remove your account. Thanks.</div>
				<form action="" method="POST" id="deleteform">
					<fieldset>					
					
						<fieldset class="radio">
							<div class="listradio">
								<input name="reason" id="reason1" type="radio" value="1" /><label for="reason1">I don't find Runner's Medium useful</label>
							</div>
							<div class="listradio">
								<input name="reason" id="reason2" type="radio" value="2" /><label for="reason2">I find the site to be slow</label>
							</div>
							<div class="listradio">
								<input name="reason" id="reason3" type="radio" value="3" /><label for="reason3">I find Runner's Medium confusing to use</label>
							</div>
							<div class="listradio">
								<input name="reason" id="reason4" type="radio" value="4" /><label for="reason4">I think it's too much work to use Runner's Medium</label>
							</div>
							<div class="listradio">
								<input name="reason" id="reason5" type="radio" value="5" /><label for="reason5">Runner's Medium is missing something I want</label>
							</div>
							<div class="listradio">
								<input name="reason" id="reason6" type="radio" value="6" /><label for="reason6">I have started using a different training log site</label>
							</div>
							<div class="listradio">
								<input name="reason" id="reason7" type="radio" value="7" /><label for="reason7">I just wanted to try Runner's Medium out</label>
							</div>
							<div class="listradio">
								<input name="reason" id="reason8" type="radio" value="8" checked="checked" ><label for="reason8">Other</label>
							</div>
						</fieldset>
						
						<label for="feedback">Feedback</label>
						<input name="feedback" id="feedback" type="text" value="" />
	
						<input value="Continue..." name="action" type="submit" class="button" />
					</fieldset>
				</form>
			</div>
		<?php
		}
		?>
</div>

<?php
require('footer.php');
?>