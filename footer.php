<?php
/*

Runner's Medium
http://www.runnersmedium.com/

footer.php

template page footer

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/
?>
        <div id="footer">
        	<h3 class="hide">Footer</h3>
        	<ul>
                <li>&copy; 2009 Runner's Medium</li>
                <li>Created by <a href="http://www.markbaltrusaitis.com"<?php echo profile().'mark'; ?>">Mark Baltrusaitis</a></li>
                <li><a href="<?php echo root(); ?>feed">Public Feed</a></li>
                <li><a href="<?php echo root(); ?>help">Help</a></li>
                <li>Follow us at <a href="http://www.twitter.com/runnersmedium">Twitter</a></li>
            </ul>
        </div>
    </div>

<?php
	// echo scripts at the bottom of the page
	if (isset($scripts)) {
		echo $scripts;
	}
	
	// google analytics
?>

<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-8340851-1");
pageTracker._trackPageview();
} catch(err) {}</script>

</body>
</html>

<?php

// end output buffering and send XHTML
ob_end_flush();

?>