<h1>Your photostream</h1>
<div id="flickrContent">
<?php
			
	// Retrieve photos from Flickr
	$recent = $f->people_getPublicPhotos($_GET['nsid'],'',$f_api_extras, $perpage, $page);
				
	//print_r($recent);
	
	// Get total pages and total photos
	$totalPages = $recent['photos']['pages'];
	$totalItems = $recent['photos']['total'];
			
	// Initialize the $images var
	$images = '';
									
	foreach($recent['photos']['photo'] as $photo) {
		$images .= makeImgBox($photo);
	}
				
	echo $images;
?>
</div>
<?php include_once('pagelinks.php'); ?>