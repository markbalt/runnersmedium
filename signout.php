<?php
/*

Runner's Medium
http://www.runnersmedium.com/

signout.php

signout user and redirect to root

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('lib/base.php');

$user->signOut(root());

require('header.php');
?>
        
<div id="content">
    <a href="<?php echo root(); ?>signin">
    	<span id="prompt">
    		Signed out successfully.  Click here to sign back in.
    	</span>
    </a>
</div>

<?php
require('footer.php');
?>