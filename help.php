<?php
/*

Runner's Medium
http://www.runnersmedium.com/

help.php

support page

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('lib/base.php');

$title = 'Runner\'s Medium - Help';
$body  = 'id="help"';
require('header.php');
?>

<div id="content">
	<div id="main">
    	<h2>What is Runner's Medium?</h2>
    	<p>
    		Runner's Medium is an web-based running journal and online community.  The journal allows you to:
    	</p>
		<ol>
			<li>Map and measure your running courses</li>
			<li>Log and track your runs</li>
			<li>Connect with other runners</li>
			<li>Share your progress</li>
		</ol>
    	
    	<h3>What is a public beta?</h3>
    	<p>
    		The site is still in testing phases, signup is public but there may still be bugs and quirks as we iron out the app.  Most features are in place and many more are on the way.  Feedback is always encouraged.
    	</p>
    	<h3>Do I need anything special to use it?</h3>
    	<p>
    		This site does not require special shoes or hardware.  You may enter your distances manually.
    	</p>
    	<h3>Does this site cost anything?</h3>
    	<p>
    		Runner's Medium is completely free.
    	</p>
    	<h3>How do I make my profile private?</h3>
    	<p>
    		Profiles are public by default.  A public profile is viewable by everyone.  When a profile is private, only you and your friends can see your updates.  To change your profile privacy go to Settings on the main menu and select the Edit Profile tab.  You can then choose public or private and click Save.
    	</p>
    	<h3 id="public_profile">What does it mean to make my profile public?</h3>
    	<p>
			If your profile is public, anyone who visits your profile can see the following information:
    	</p>
		<ul>
			<li>your username</li>
			<li>your picture</li>
			<li>distance, time and location for your last 5 runs</li>
			<li>how many runs you have logged</li>
			<li>your total distance</li>
			<li>your average pace</li>
			<li>a list of your friends</li>
			<li>and your profile info (excluding weight - this is only used to calculate calories)</li>
		</ul>
		<p>
    		If your profile is private, no one except your friends can see the above information.  Other users can still search for you and request your friendship.  The only public information is the following:
    	</p>
		<ul>
			<li>your username</li>
			<li>your picture</li>
		</ul>
    	<h3>How do I log a run?</h3>
    	<p>
    		Sign in and click Run.  Enter the time and distance for your run - these are the only required values.  If you would like to enter a course, select Add New under Run &gt; Courses. We will save the distance and location for your course automatically for future runs.  After you map the course select Run It.  With a course added, you only have to enter the time for that day's run.  To add a pair of shoes select Add New under Run &gt; Shoes.
    	</p>
    	<h3>How do I map a course?</h3>
    	<p>
    		Sign in and select Run &gt; Courses.  A course is required to log a run.  Enter a location and click at each interval to map your route automatically measure the distance of the course.  Click the start interval again to close a loop.  Click Start Over to clear all points or click Step Back to clear the last point.  Only the location and distance are required to create a course.  You do not have to map the route in order to save and run a course.  Click Save to add the course.
    	</p>
    	<h3>Why should I enter my shoes?</h3>
    	<p>
    		Runner's Medium will track the distance run on your shoes and calculate their efficiency if you enter their purchase price.  This can be useful in determining when to replace shoes.  You can archive old shoes by retiring them: click Retire under the shoe brand and model when the time comes.  Select the 'Show Retired Footwear' checkbox to see all retired and active shoes.
    	</p>
    	<h3>How do I make friends?</h3>
    	<p>
    		Go to Run &gt; Find to search for other runners by name, username or email.  On a user's profile who is not your friend you can simply click 'Add to Friends' to send a friendship request.  The user will have the opportunity to accept or deny your friendship then next time they sign in.
    	</p>
    	<h3>How do I accept friendship?</h3>
    	<p>
    		Go to your user home and click Accept or Deny to any pending requests from users under the Requests heading.  Or click 'Add to Friends' on any user's profile who has already requested your friendship - this will automatically add them as a friend.
    	</p>
    	<h3>How do I share my progress?</h3>
    	<p>
    		Make your profile public under Settings &gt; Edit Profile &gt; Make my profile public?  Then you can share your profile URL to anyone.  Visit Run &gt; Share to find more ways to share your progress.
    	</p>
       	<h3>How do I delete my account</h3>
    	<p>
			To completely and irrevocably delete your account go to the Settings &gt; Account tab and click the delete link on the bottom of the page.  Click Continue to Confirm your account deletion.  All your runs, courses, shoes and friends will be removed.
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