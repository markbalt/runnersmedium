<?php
/*

Runner's Medium
http://www.runnersmedium.com/

header.php

template page header

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

// start output buffer
ob_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>

	<title><?php
			if (isset($title)) {
				echo $title;
			} else {
				echo 'Runner\'s Medium';
			}
		?></title>
		
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="keywords" content="running journal, running log, runner's training log, course mapping, running pedometer, online running community" />
	<meta name="description" content="A web-based running journal and online running community." />
	<meta name="author" content="Mark Baltrusaitis - josieprogramme.com" />
	
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo root(); ?>favicon.ico" />
    <link rel="stylesheet" type="text/css" media="screen, projection" href="<?php echo root(); ?>css/style.css" />
    <link rel="stylesheet" type="text/css" media="print" href="<?php echo root(); ?>css/print.css" />
    
    <script type="text/javascript" src="<?php echo root(); ?>js/jquery/jquery-1.3.2.min.js"></script>
    <script type="text/javascript" src="<?php echo root(); ?>js/jquery/jquery.dimensions.js"></script>
    <script type="text/javascript" src="<?php echo root(); ?>js/jquery/jquery.bgiframe.js"></script>
    <script type="text/javascript" src="<?php echo root(); ?>js/jquery/jquery.tooltip.js"></script>
    <script type="text/javascript" src="<?php echo root(); ?>js/jquery/jquery.dropdownPlain.js"></script>
    
    <!--[if lte IE 7]>
        <link rel="stylesheet" type="text/css" media="screen" type="text/css" href="<?php echo root(); ?>css/ie.css" />
    <![endif]-->
    
    <!--[if IE 6]>
        <link rel="stylesheet" type="text/css" media="screen" type="text/css" href="<?php echo root(); ?>css/ie6.css" />
    <![endif]-->
	
    <script type="text/javascript" src="<?php echo root(); ?>js/prototype-1.6.0.3.js"></script>
    <script type="text/javascript" src="<?php echo root(); ?>js/forms.min.js"></script>

	<?php
		if (isset($head)) {
			echo $head;
		}
	?>
</head>

<body<?php
	if (isset($body)) {
		echo ' '.$body;
	}
?>>
    <div class="wrapper">
		<div id="header">
			<ul id="access" class="hide">
				<li><a href="#content" title="Skip to content" accesskey="0">Skip to content</a></li>
				<li><a href="#nav" title="Skip to navigation" accesskey="2">Skip to navigation</a></li>
			</ul>
			
            <h1><a href="<?php echo root(); ?>" accesskey="1" title="Runner's Medium - Home">Runner's Medium</a></h1>
            
            <div class="nav">                
                <?php
                if (!$user->signinCheck()) {
                	// not signed in
                    ?>
                    <ul id="nav" class="dropdown nav">
                        <li><a href="<?php echo root(); ?>signin">Sign in</a></li>
                        <li><a href="<?php echo root(); ?>signup">Join</a></li>
                    </ul>
                    <?php
                } else {
                    // signed in
                    ?>
                    <ul id="nav" class="dropdown nav">
                        <li><a href="<?php echo root(); ?>">Home</a>
                        	<ul>
                        		<li><a href="<?php echo root(); ?>newrun">New Run</a></li>
								<li><a href="<?php echo root(); ?>runs">My Runs</a></li>
								<li><a href="<?php echo root(); ?>courses">Courses</a></li>
								<li><a href="<?php echo root(); ?>shoes">Shoes</a></li>
								<li><a href="<?php echo root(); ?>find">Search</a></li>
								<li><a href="<?php echo root(); ?>share">Share</a></li>
							</ul>
                        </li>
                        <li><a href="<?php echo $user->profile(); ?>">Profile</a></li>
                        <li><a href="<?php echo root(); ?>settings">Settings</a></li>
                        <li><a href="<?php echo root(); ?>signout">Sign out</a></li>
                    </ul>
                    
					<?php
                }
                ?>
            </div>
        </div>