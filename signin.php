<?php
/*

Runner's Medium
http://www.runnersmedium.com/

signin.php

user signin and & auth using persistent cookies and throttled to 2 seconds

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('lib/base.php');

// redirect to home?
if ($user->signinCheck()) {
    redirect(home());
}

$username   = null;
$password   = null;
$rememberme = false;

if (isset($_POST['action']) && $_POST['action'] == 'Sign In') {

	// post vars
	if (isset($_POST['username'])) {
	    $username = $_POST['username'];
	}
	
	if (isset($_POST['password'])) {
	    $password = $_POST['password'];
	}

	if (isset($_POST['rememberme']) && $_POST['rememberme'] == '1') {
		$rememberme = true;
	}
    
    // escape
    $sqlusername = mysql_real_escape_string($username);
   	$sqlpassword = mysql_real_escape_string($password);

    // authenticate user, throttle to 2 seconds
    $result = $conn->query("SELECT id, username, units FROM users
		WHERE (username = '$sqlusername' OR email = '$sqlusername')
		AND password = MD5('$sqlpassword')
		AND lastfail < ADDTIME(NOW(), -2) LIMIT 1");
	
    if ($conn->rowCount($result) == 1) {
   
        $line = $conn->fetchAssoc($result);

		// create persistent cookie
		if ($rememberme) {
			
			// create and store secondary identifier and token so we dont have to store username or password in the cookie
			$salt = 'BALTRUSAITIS';
			
			$cookieid = md5($salt . md5($line['username'] . $salt));
			$token = md5(uniqid(rand(), true));
			
			// update last login and store cookieid & token, kill two birds with one query
			$conn->query('UPDATE users SET lastlogin = CURDATE(), cookie = \''.mysql_real_escape_string($cookieid).'\', token = \''.mysql_real_escape_string($token).'\'
				WHERE id = '.mysql_real_escape_string($line['id']).' LIMIT 1');
			
			// set to expire in 1 week
			$timeout = time() + 60 * 60 * 24 * 7;
			
			// set the cookie
			setcookie('auth', $cookieid.':'.$token, $timeout);
		} else {
			// just update last login
			$conn->query('UPDATE users SET lastlogin = CURDATE() WHERE id = '.mysql_real_escape_string($line['id']).' LIMIT 1');
		}
		
        // call user signin, this redirects the user
        $user->signin($line['id'], $line['username'], home(), $line['units']);
    } else {
    
    	// signin failed
        $error = 'your username and/or password were incorrect';
        
        // update last failure column to throttle logins
        $conn->query("UPDATE users SET lastfail = NOW() WHERE username = '$sqlusername' OR email = '$sqlusername' LIMIT 1");
    }
}

// output buffered header xhtml
$title = 'Runner\'s Medium - Signin';
require('header.php');
?>
        
<div id="content">

    <h2>Sign In</h2>
	
	<?php
		// echo any messages
		if (isset($error)) {
			echo '<div id="error"><span class="oops">Oops</span> '.$error.'</div>';
		} else if (isset($message)) {
			echo '<div id="message"><span class="ok">Okay</span> '.$message.'<a class="close" onclick="closeMessage();">x</a></div>';
		}
	?>
    
    <form action="" method="post" id="signinform">
    	<fieldset>
            <label for="username">Username or Email</label>
            <input name="username" id="username" type="text" />
            
            <label for="password">Password</label>
            <input name="password" id="password" type="password" />
            
            <?php
            	// check remember me?
            	if ($rememberme) {
            		$check = 'checked="checked"';
            	} else {
            		$check = '';
            	}
            ?>
            <br class="clear" />
            <input name="rememberme" id="rememberme" type="checkbox" value="1" class="check" <?php echo $check; ?> />
			<label class="check" for="rememberme">Remember me</label>
            
            <input name="action" type="submit" id="signinbtn" value="Sign In" class="button" />
		</fieldset>
    </form>

    <p>
    	Have you <a href="<?php echo lost(); ?>">lost your password?</a>
    </p>
    
</div>

<?php

// output buffered footer xhtml
require('footer.php');
?>