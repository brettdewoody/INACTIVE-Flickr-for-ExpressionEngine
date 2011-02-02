<h1>Your Sets</h1>
<div id="flickrContent" class="scrollpane narrower" style="overflow-y:scroll; overflow-x:hidden;">
<?php
	// Retrieve sets from Flickr
	$sets = $f->photosets_getList($_GET['nsid']);
				
	//print_r($sets);
				
	$images = '';
				
	$count = 0;
	foreach($sets['photoset'] as $photoset) {
		$coverPic = "http://farm" . $photoset['farm']. ".static.flickr.com/" . $photoset['server'] . "/" . $photoset['primary'] . "_" . $photoset['secret'] . '_s.jpg';
		echo '<div class="setBox">';
		echo '<a href="browser.php?v=Set&set=' . $photoset['id'] . '&' . $URLstring . '"><img src="' . $coverPic . '" alt="' . $photoset['title'] . '" /></a>';
		echo '<div class="text"><a href="browser.php?v=Set&set=' . $photoset['id'] . '&' . $URLstring . '">' . $photoset['title'] . '</a></div>';
		echo '<div class="subtext"><strong>' . $photoset['photos'] . '</strong> photos</div>';
		echo "</div>";
		if ($count == 5 || $count == 11 || $count == 17){echo '<div class="clear"></div>';}
		$count++;
	}
?>
</div>