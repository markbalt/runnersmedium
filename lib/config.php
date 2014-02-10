<?php
/*

Runner's Medium
http://www.runnersmedium.com/

config.php

app config

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

// mail config
define('MAIL_FROM', 'team@runnersmedium.com');
define('MAIL_NAME', '"Runner\'s Medium"');
define('MAIL_TO', 'team@runnersmedium.com');

// upload data
define('PIC_DIR', 'upload/');
define('DEFAULT_PIC', 'images/default.png');
define('PIC_WIDTH', 85);
define('PIC_HEIGHT', 85);
define('MAX_PIC_SIZE', 2097152);

// min max for validation
define('MIN_USERNAME', 3);
define('MAX_USERNAME', 15);
define('MIN_PASSWORD', 6);
define('MAX_PASSWORD', 15);
define('MIN_EMAIL', 6);
define('MAX_EMAIL', 45);
define('MAX_NAME', 45);
define('MAX_ABOUT', 140);
define('MAX_LOCATION', 45);
define('MAX_URL', 90);
define('MAX_WHY', 140);
define('MAX_WEIGHT', 65535);

// number of results for each page
define('MAX_RESULTS', 7);

// number of results for feed on home page
define('MAX_RECENT_RESULTS', 7);

// number of search results for each page
define('MAX_SEARCH_RESULTS', 10);

// number of pages to show in pagination before they are obscured by an elipsis
define('MAX_PAGES', 7);

// unit ids
define('MI', 0);
define('KM', 1);

?>