<?php
/*

Runner's Medium
http://www.runnersmedium.com/

confirmreset.php

validate and confirm login request from lost password and redirect to password page

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('lib/base.php');

if ($user->signinCheck()) {
	redirect(root());
}

if (isset($_GET['email']) && isset($_GET['hash'])) {

	// authenticate ticket   
    $email  = $_GET['email'];
    $hash   = $_GET['hash'];
    $ticket = new TicketsComponent($conn);
    
    // authenticate user
    if ($ticket->get($hash) == $email) {
    	$sqlemail = mysql_real_escape_string($email);
        $result = $conn->query("SELECT id, username FROM users WHERE email = '$sqlemail' LIMIT 1");
        
        // authenticate user, throttle to 15 seconds
	    $result = $conn->query("SELECT id, username, units FROM users
	    						WHERE (email = '$sqlemail')
	    						AND lastfail < ADDTIME(NOW(), -15) LIMIT 1");
    
        if ($conn->rowCount($result) == 1) {
            $line = $conn->fetchAssoc($result);

            // delete the ticket
            $ticket->del($hash);
			
			// after signin redirect to password update in profile
            $user->signin($line['id'], $line['username'], password(), $line['units']);
        } else {
            $error = 'user not found.  Please check link or <a href="'.lost().'">try again</a>';
        }
    } else {
        $error = 'invalid email and/or expired token.  Please check link or <a href="'.lost().'">try again</a>';
    }
} else {
    $error = 'user email and/or token not provided.  Please check link or <a href="'.lost().'">try again</a>';
}

require('header.php');
?>
        
<div id="content">
    <?php messages($error, $message); ?>
</div>

<?php
require('footer.php');
?>