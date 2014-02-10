<?php
/*

Runner's Medium
http://www.runnersmedium.com/

ajax/check.php

validate and check availability of username from ajax call

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('../lib/base.php');

if (isset($_GET['username']) && notempty($_GET['username'])) : 
	
	// validate username
	$username = $_GET['username'];

	if (strtoupper($username) == strtoupper($user->username())) {
		$res = profile().'<strong>'.format($username).'</strong>';
	} elseif ($conn->usernameExists($username)) {
		$res = '<span class="inlineerror">username is taken, please choose another</span>';
	} elseif (strlen($username) > MAX_USERNAME) {
		$res = '<span class="inlineerror">that username is too long, choose something shorter</span>';
	} elseif (preg_match('/^\w+$/', $username) == 0) {
        $res = '<span class="inlineerror">invalid username</span>';
    } else {
		$res = profile().'<strong>'.format($username).'</strong>';
	}
	
else :

	// profile url
	$res = profile().'<strong>username</strong>';

endif;

echo json_encode(array('response' => $res));
?>