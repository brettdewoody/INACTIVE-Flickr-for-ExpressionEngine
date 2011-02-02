<h1>Your Galleries</h1>
<div id="flickrContent" class="scrollpane narrower" style="overflow-y:scroll; overflow-x:hidden;">
<?php
	// Retrieve sets from Flickr
	$galleries = $f->galleries_getList($_GET['nsid']);
				
	//print_r($galleries);
				
	foreach($galleries['galleries']['gallery'] as $gallery) {
		$coverPic = "http://farm" . $gallery['primary_photo_farm']. ".static.flickr.com/" . $gallery['primary_photo_server'] . "/" . $gallery['primary_photo_id'] . "_" . $gallery['primary_photo_secret'] . '_s.jpg';
		echo '<div class="galleryBox">';
		echo '<a class="galleryImg" href="browser.php?v=Gallery&gallery=' . $gallery['id']  . $URLstring . '"><img src="' . $coverPic . '" alt="' . $gallery['title'] . '" /></a>';
		echo '<div class="text"><a href="browser.php?v=Gallery&gallery=' . $gallery['id'] . $URLstring . '">' . $gallery['title'] . '</a></div>';
		echo '<div class="notes">' . $gallery['count_photos'] . ' photos</div>';
		echo '<div class="description">' . $gallery['description'] . '</div>';
		echo "</div>";
		echo '<div class="clear"></div>';
	}
?>
</div>