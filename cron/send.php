<?php 
/*

Runner's Medium
http://www.runnersmedium.com/

cron/sendthings.php

send email alerts via automation

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('../../domains/runnersmedium.com/html/lib/base.php');
require('../../domains/runnersmedium.com/html/lib/message.class.php');

$success = $failed = 0;

echo date('Y-m-d g:i A')." starting\n";

// mail and message objects
$mail  = new MailComponent();
$nudge = new MessageHelper();

// nudge message template
$nudge->subj = '%FROM_USERNAME% nudged you';
$nudge->body = <<<EOD
%FROM_USERNAME% (%FROM_NAME%) nudged you.
To log a run, follow this link: http://www.runnersmedium.com/signin
See %FROM_USERNAME%'s profile here: http://www.runnersmedium.com/%FROM_USERNAME%
Best,
The Runner's Medium Team

P.S. If you would rather not receive these notices, visit http://www.runnersmedium.com/account/notices
EOD;

// query pending nudges
$result = $conn->query('SELECT a.id, b.username AS username, b.name AS name, b.email, c.username AS from_username, c.name AS from_name
	FROM nudges AS a JOIN users AS b ON (a.nudgee = b.id) JOIN users AS c ON (a.nudger = c.id)
	WHERE b.optin_nudges = 1 AND state = 1');

while ($line = $conn->fetchAssoc($result)) {
	
	// create message
	$alert = $nudge->createMessage($line);
	
	// send it
	if ($mail->send(MAIL_FROM, MAIL_NAME, $line['name'].' <'.$line['email'].'>', $alert['subj'], $alert['body'])) {
	
		// update row
		$conn->query('UPDATE nudges SET state = 2 WHERE id = '.mysql_real_escape_string($line['id']));
		$success++;
		
	} else {
	    error_log('Failed to send message '. $mail->showError());
	    $failed++;
	}
}

$request = new MessageHelper();

// friend request message template
$request->subj = '%FROM_USERNAME% requested your friendship on Runner\'s Medium';
$request->body = <<<EOD
%FROM_USERNAME% (%FROM_NAME%) would like to be your friend.
To confirm this friend follow visit http://www.runnersmedium.com/home
Best,
The Runner's Medium Team

P.S. If you would rather not receive these notices, visit http://www.runnersmedium.com/account/notices
EOD;

// query pending nudges
$result = $conn->query('SELECT a.id, b.username AS username, b.name AS name, b.email, c.username AS from_username, c.name AS from_name
	FROM friends AS a JOIN users AS b ON (a.friend = b.id) JOIN users AS c ON (a.user = c.id)
	WHERE b.optin_friends = 1 AND a.state = 1 AND a.sent = 0');

while ($line = $conn->fetchAssoc($result)) {
	
	// create message
	$alert = $request->createMessage($line);
	
	// send it
	if ($mail->send(MAIL_FROM, MAIL_NAME, $line['name'].' <'.$line['email'].'>', $alert['subj'], $alert['body'])) {
	
		// update row
		$conn->query('UPDATE friends SET sent = 1 WHERE id = '.mysql_real_escape_string($line['id']));
		$success++;
		
	} else {
	    error_log('Failed to send message '. $mail->showError());
	    $failed++;
	}
}

echo date('Y-m-d g:i A').' '.$_SERVER['PHP_SELF']." completed $success messages sent, $failed failed\n";

?>