<?php
	$galleryPhotos = $f->galleries_getPhotos($_GET['gallery'],$f_api_extras,$perpage, $page);
	$galleryInfo = $f->galleries_getInfo($_GET['gallery']);
				
	//print_r($galleryPhotos);
?>
<h1><em><?=$galleryInfo['gallery']['title']?></em> Gallery</h1>
<div id="flickrContent">
	
<?php
	// Get total pages and total photos
	$totalPages = $galleryPhotos['photos']['pages'];
	$totalItems = $galleryPhotos['photos']['total'];
	
	// Initialize the $images var
	$images = '';
									
	foreach($galleryPhotos['photos']['photo'] as $photo) {
		$images .= makeImgBox($photo);
	}
				
	echo $images;
?>
</div>
<?php include_once('pagelinks.php'); ?>