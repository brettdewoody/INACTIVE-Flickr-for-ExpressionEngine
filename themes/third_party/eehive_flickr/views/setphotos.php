<?php
	$setPhotos = $f->photosets_getPhotos($_GET['set'],$f_api_extras,'',$perpage, $page,'photos');
	$setInfo = $f->photosets_getInfo($_GET['set']);
				
	//print_r($setInfo);
?>
<h1><?=$setInfo['title']?> Set</h1>
<div id="flickrContent">
	
<?php
	// Get total pages and total photos
	$totalPages = $setPhotos['photoset']['pages'];
	$totalItems = $setPhotos['photoset']['total'];
	
	// Initialize the $images var
	$images = '';
									
	foreach($setPhotos['photoset']['photo'] as $photo) {
		$images .= makeImgBox($photo);
	}
				
	echo $images;
?>
</div>
<?php include_once('pagelinks.php'); ?>