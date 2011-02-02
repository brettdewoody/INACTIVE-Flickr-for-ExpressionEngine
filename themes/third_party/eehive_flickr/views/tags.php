<h1>Your Tags</h1>
<div id="flickrContent" class="scrollpane narrower" style="overflow-y:scroll; overflow-x:hidden;">
<?php
	// Retrieve sets from Flickr
	$tags = $f->tags_getListUserPopular($_GET['nsid'],100);
			
	$fontMin = "11";
	$fontMax = "28";
	$size = $fontMin;
				
	$numTags = count($tags);
	sort($tags);
				
	$increment = intval($numTags/($fontMax-$fontMin));
				
	for ($i=0; $i < $numTags; $i++) {
		$output[$tags[$i][_content]] = $size ;
		if ($increment == 0 || $i % $increment == 0 )  { 
		$size++;
		}
	}
				
	ksort($output);
	
	echo '<div class="text">';
	foreach ($output as $tg => $sz) {
		echo '&nbsp;<a href="browser.php?v=Tag&tag=' . $tg . $URLstring . '" style="font-size: '.$sz.'px;">'.$tg.'</a> ';
	}
	echo '</div>';
	
	//print_r($tags);
?>
</div>
