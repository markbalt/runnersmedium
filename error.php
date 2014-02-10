<?php
/*

Runner's Medium
http://www.runnersmedium.com/

error.php

db error page

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

require('lib/base.php');

?>
        
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
	
<head>
    <title>Runner's Medium - Over Capacity</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    
    <style type="text/css" media="all">

body {
		margin: 4em auto;
		padding: 1em 2em;
		text-align: center;
		font-family: 'Lucida Grande',Arial,Helvetica,Sans-Serif;
		font-size: 0.81em;
		line-height: 1.5em;
		color: #333;
}

h1 {
		margin: 10px 0 20px 0;
		padding: 30px 0;
		color: #FF5165;
		font-size: 2.1em;	
}
	
</style>


</head>

<body>
	<h1>Uh oh.  Someting has gone terribly wrong</h1>
	<p>
		We're working on it.  <a href="<?php echo root(); ?>">Back to home</a>
	</p>
</body>
</html>