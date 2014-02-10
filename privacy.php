<?php
/*

Runner's Medium
http://www.runnersmedium.com/

privacy.php

privacy policy

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('lib/base.php');

$title = 'Runner\'s Medium - Privacy Policy';
$body  = 'id="help"';
require('header.php');
?>

<div id="content">
	<div id="main">
		<h2>Privacy Policy</h2>
    	<p>
		Your privacy is very important to us. Accordingly, we have developed this Policy in order for you to understand how we collect, use, communicate and disclose and make use of personal information. The following outlines our privacy policy.
		</p>
		
		<ul>
			<li>
				To become a member of this site it is required to provide your email address and name.  Once a member, you may provide other information if you like.  All personal information collected is provide you with the product and services made available through this site.
			</li>
			<li>
				We will collect and use of personal information solely with the objective of fulfilling those purposes specified by us and for other compatible purposes, unless we obtain the consent of the individual concerned or as required by law.		
			</li>
			<li>
				We will only retain personal information as long as necessary for the fulfillment of those purposes. 
			</li>
			<li>
				We will collect personal information by lawful and fair means and, where appropriate, with the knowledge or consent of the individual concerned. 
			</li>
			<li>
				Personal data should be relevant to the purposes for which it is to be used, and, to the extent necessary for those purposes, should be accurate, complete, and up-to-date. 
			</li>
			<li>
				We will protect personal information by reasonable security safeguards against loss or theft, as well as unauthorized access, disclosure, copying, use or modification.
			</li>
			<li>
				We will make readily available to customers information about our policies and practices relating to the management of personal information. 
			</li>
		</ul>
		
		<p>
			We are committed to conducting our business in accordance with these principles in order to ensure that the confidentiality of personal information is protected and maintained. 
		</p>
	</div>

	<div id="side">
		<h3>Help &amp; Support</h3>
		<ul>
            <li><a href="<?php echo root(); ?>help">Help</a></li>
            <li><a href="<?php echo root(); ?>privacy">Privacy Policy</a></li>
            <li><a href="<?php echo root(); ?>terms">Terms</a></li>
            <li><a href="<?php echo root(); ?>contact">Contact</a></li>
        </ul>
	</div>
</div>

<?php
require('footer.php');
?>