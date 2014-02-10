<?php
/*

Runner's Medium
http://www.runnersmedium.com/

share.php

code snippets and ways to share user profiles

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('lib/base.php');

$user->signinCheck(signin());
$private  = true;

// check for submit action
if (isset($_POST['action']) && $_POST['action'] == 'Make my profile public') {

	$conn->query('UPDATE users SET ispublic = 1 WHERE id = '.mysql_real_escape_string($user->ID()).' LIMIT 1');
	$message = 'your profile is public now';
	$private = false;

} else {
	// check profile privacy
	$result = $conn->query('SELECT ispublic FROM users WHERE id = '.mysql_real_escape_string($user->ID()).' LIMIT 1');
	
	if($conn->rowCount($result) > 0) {
		$line = $conn->fetchAssoc($result);
	
		if($line['ispublic'] == 0) {
			$private = true;
		} else {
			$private = false;
		}
		
	} else {
		$error = 'user error';
	}
}

$title = 'Runner\'s Medium - Share';
require('header.php');
?>

<div id="content">

	<h2>Share your Progress</h2>
	<?php messages($error, $message); ?>
		
	<?php if ($private) : ?>
		To share your progress you'll have to make your profile public. <a href="<?php echo root(); ?>help#public_profile">What does this mean?</a>
	
		<fieldset>
			<form action="" method="post" name="topublicform" id="topublicform">
				<input name="action" type="submit" value="Make my profile public" class="button" />
			</form>
		</fieldset>
			
	<?php else : ?>
	
		<p>
			You can share your profile URL:
			<code>
				<a href="<?php echo $user->profile(); ?>"><?php echo $user->profile(); ?></a>
			</code>
		</p>
		
		<p>
			or you can use the following code to embed your profile as basic HTML that you can style with CSS: 
			<code>
				<?php echo format($user->getSnippet()); ?>
			</code>
		</p>
					
	<?php endif; // profile is private ?>

</div>

<?php
require('footer.php');
?>