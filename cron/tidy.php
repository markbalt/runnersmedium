<?php 
/*

Runner's Medium
http://www.runnersmedium.com/

cron/tidyup.php

mysql database maintainance script

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('../lib/base.php');

echo date('Y-m-d g:i A')." starting\n";

// delete dismissed nudges older than 1 week
$conn->query('DELETE FROM `nudges` WHERE `state` = 3 AND `created` < SUBDATE(NOW(), INTERVAL 7 DAY)');

// delete auth tickets older than 24 hours
$conn->query('DELETE FROM `tickets` WHERE `created` < SUBDATE(NOW(), INTERVAL 1 DAY);');

echo date('Y-m-d g:i A')." completed\n";