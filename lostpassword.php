<?php
/*

Runner's Medium
http://www.runnersmedium.com/

lostpassword.php

send email including login ticket for lost password request

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('lib/base.php');

$stop  = false;
$email = null;

// redirect to home?
if($user->signinCheck()) {
	redirect(home());
}

// check for submit action
if (isset($_POST['action']) && $_POST['action'] == 'Reset My Password') {

    $email    = $_POST['email'];
    $sqlemail = mysql_real_escape_string($email);
    
    // check the email
    $result = $conn->query("SELECT username FROM users WHERE email = '$sqlemail' LIMIT 1");
    
    if ($conn->rowCount($result) > 0) {
        // get username
        $line = $conn->fetchAssoc($result);
        $username = $line['username'];
        
        $ticket = new TicketsComponent($conn);
        $url = URL()."/confirmlostpassword/$email/".$ticket->set($email);
        
        $body = <<<EOD
Hello,\r\n\r\n
Your username is:\r\n\r\n
$username\r\n\r\n
Open this link in your browser:\r\n\r\n
$url\r\n\r\n
Then you can access your account and update your password.\r\n\r\n
Yours,\r\n
The Runner's Medium Team
EOD;
        
        // send instructions message
        $mail = new MailComponent();
        if ($mail->send(MAIL_FROM, MAIL_NAME, $email, "Reset your Runner's Medium Password", $body)) {
            // write confirmation message
            $stop = true;
        } else {
            $error = 'failed to send email';
        }
        
    } else {
        $error = 'no user found with that email address';
    }

}

$title = 'Runner\'s Medium - Lost Password Recovery';
require('header.php');
?>
        
<div id="content">

	<?php if ($stop) : ?>
		<h2>Lost Password Recovery</h2>
		You will receive an email shortly with your username and a link to reset your password.
	<?php else : ?>
		<h2>Lost Password Recovery</h2>
		<?php messages($error, $message); ?>

		<form action="" method="post" id="lostpasswordform">
			<fieldset>
				<div class="comment">We'll send you your username and a link to reset your password.</div>				
				<label for="email">Email</label>
				<input name="email" id="email" type="text" value="<?php echo format($email); ?>" />
				<input name="action" type="submit" value="Reset My Password" class="button" />
			</fieldset>
		</form>
	<?php endif; // stop ?>
	
</div>

<?php
require('footer.php');
?>