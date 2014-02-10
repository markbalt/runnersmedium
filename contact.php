<?php
/*

Runner's Medium
http://www.runnersmedium.com/

contact.php

simple contact form

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('lib/base.php');

$title = 'Runner\'s Medium - Contact';
$body  = 'id="help"';
require('header.php');
?>

<div id="content">
	<div id="main">
		<h2>Contact Us</h2>
		<p>
			Feel free to contact us with questions, ideas, or inquiries.  Send an email directly to
			<code class="email">
				<a href="mailto:team@runnersmedium.com">the Runner's Medium team</a>
			</code>
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