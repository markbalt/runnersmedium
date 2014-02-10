<?php

/*

Runner's Medium
http://www.runnersmedium.com/

pagedao.class.php

extended reusable page objects

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

class extHelper extends Component
{
	public function __construct()
	{		
	}
	
	public function paging($url, $page, $max)
	{
		// echo pagination page query string param is always last
		if ($max > 1) :
		
			// show paging
			echo '<ul id="page">';
			
				if ($page > 1) {
					$prev = $page-1;		
					echo '<li class="prev"><a href="'.$url.'page='.$prev.'">&laquo; prev</a></li>';
				}
		
				if ($max < MAX_PAGES) {
					for($i = 1; $i <= $max; $i++) {
						if ($i == $page) {
							// select page
							echo '<li class="selected">'.$i.'</li>';
						} else {
							echo '<li><a href="'.$url.'page='.$i.'">'.$i.'</a></li>';
						}
					}
				} else {
					// for more than MAX_PAGES results show first 1, last 1 and 2 before and after the selected page
					$elipsis = true;
						
					for($i = 1; $i <= $max; $i++) {	
						if ($i == $page) {
							// selected page
							echo '<li class="selected">'.$i.'</li>';
							$elipsis = true;
						} elseif ($i <= 1 || $i > $max-1 || ($i >= $page-1 && $i <= $page+1 ) ) {
							echo '<li><a href="'.$url.'page='.$i.'">'.$i.'</a></li>';
							$elipsis = true;
						} elseif ($elipsis) {
							// only show elipsis once per section
							echo '<li class="elipsis">...</li>';
							$elipsis = false;
						}
					}
				}
				
				if ($page < $max) {
					$next = $page+1;
					echo '<li class="next"><a href="'.$url.'page='.$next.'">next &raquo;</a></li>';
				}
		
			echo '</ul>';
		endif;
	}
}

?>