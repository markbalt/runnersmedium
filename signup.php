<?php
/*

Runner's Medium
http://www.runnersmedium.com/

signup.php

authenticate and create user account and redirect to home

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('lib/base.php');
require_once('lib/recaptchalib.php');

// redirect to home?
if ($user->signinCheck()) {
    redirect(home());
}

$username = $password = $email = $name = $invitecode = null;

// do recaptcha?
$recaptcha = false;

// check for submit action
if (isset($_POST['action']) && $_POST['action'] == 'Sign Up') {

	// get post data
	if (isset($_POST['username'])) {
	    $username = $_POST['username'];
	}
	
	if (isset($_POST['password'])) {
	    $password = $_POST['password'];
	}
	
	if (isset($_POST['email'])) {
	    $email = $_POST['email'];
	}
	
	if (isset($_POST['name'])) {
	    $name = $_POST['name'];
	}

	if ($recaptcha) {	
		// recaptcha response
		$resp = recaptcha_check_answer(RECAPTCHA_PRIVATE_KEY, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]); 
	}
	
    // validate input in case scripting is disabled this is normally handled by javascript
    if ($conn->usernameExists($username)) {
    	$error = 'username is taken.  Please choose another';
    } if (strlen($username) < MIN_USERNAME || strlen($username) > MAX_USERNAME || preg_match('/^\w+$/', $username) == 0) {
        $error = "Invalid username";
    } elseif (strlen($password) < MIN_PASSWORD || strlen($password) > MAX_PASSWORD || strcmp($password, $username) == 0) {
        $error = 'invalid password';
    } elseif (strlen($email) < MIN_EMAIL || strlen($email) > MAX_EMAIL || eregi('^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$', $email) == 0) {
        $error = 'invalid email';
    } elseif ($conn->emailExists($email)) {
    	$error = 'a user with that email address already exists.  Have you <a href="'.root().'lostpassword">lost your password</a>?';
    } elseif (strlen($name) == 0) {
        $error = 'please specify a name';
    } elseif (strlen($name) > MAX_NAME) {
        $error = 'name cannot be more than '.MAX_NAME.' characters';
    } elseif ($recaptcha && !$resp->is_valid) {
    	$error = 'The reCAPTCHA wasn\'t entered correctly';
    } elseif (is_null($error)) {
 
	    // escape
        $sqlusername = mysql_real_escape_string($username);
        $sqlpassword = mysql_real_escape_string($password);
        $sqlemail    = mysql_real_escape_string($email);
        $sqlname     = mysql_real_escape_string($name);
        
        // insert new user
        $conn->query("INSERT INTO users (username, password, email, name, lastlogin) VALUES ('$sqlusername', MD5('$sqlpassword'), '$sqlemail', '$sqlname', CURDATE())");
        
        // get the new user id and redirect to the user's home page
		$result = $conn->query("SELECT id, username FROM users WHERE id = LAST_INSERT_ID() LIMIT 1");
        
        if ($conn->rowCount($result) == 1) {
			// call user signin
			$line = $conn->fetchAssoc($result);
			$user->signin($line['id'], $line['username'], home());
		} else {
            $error = 'there was an error creating the new user';
        }
    }
}

$title = 'Runner\'s Medium - Sign up'; 
require('header.php');
?>

<div id="content">

    <h2>Sign Up</h2>
	<?php messages($error, $message); ?>
    <form action="" method="post" id="signupform" onsubmit="return signup();">
    	<fieldset>
            <label for="username">Choose a unique username</label>
            <input name="username" id="username" type="text" onkeyup="userExists()" value="<?php echo format($username); ?>" /><span class="inlineerror" id="username-err"></span>
            
            <div id="profileurl"><?php echo profile().'<strong>username</strong>'; ?></div>
            
            <label for="password">Password</label>
            <input name="password" id="password" type="password" value="<?php echo format($password); ?>" /><span class="inlineerror" id="password-err"></span>

            <label for="email">Email</label>
            <input name="email" id="email" type="text" value="<?php echo format($email); ?>" /><span class="inlineerror" id="email-err"></span>

            <label for="name">First and Last Name</label>
            <input name="name" id="name" type="text" value="<?php echo format($name); ?>" /><span class="inlineerror" id="name-err"></span>
            
            <?php
            	if ($recaptcha) {
					echo '<div id="repcaptcha">'.recaptcha_get_html(RECAPTCHA_PUBLIC_KEY).'</div>';
				}
			?>
			
            <div class="comment">By clicking 'Sign Up' below, you are agreeing to the <a href="<?php echo root(); ?>terms">terms of service</a>.</div>
            <input name="action" type="submit" value="Sign Up" class="button" />
       </fieldset>
    </form>
    
</div>

<?php
require('footer.php');
?>