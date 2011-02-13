<h1>Photos tagged with <em><?=$_GET['tag']?></em></h1>
<div id="flickrContent">
<?php
	$tagPhotos = $f->photos_search(array("user_id"=>$_GET['nsid'],"tags"=>$_GET['tag'],"extras"=>$f_api_extras,"page"=>$page,"per_page"=>$perpage,"content_type"=>1));
				
	//print_r($tagPhotos);
				
	// Get total pages and total photos
	$totalPages = $tagPhotos['pages'];
	$totalItems = $tagPhotos['total'];
				
	// Initialize the $images var
	$images = '';
									
	foreach($tagPhotos['photo'] as $photo) {
		$images .= makeImgBox($photo);
	}
				
	echo $images;
?>
</div>
<?php include_once('pagelinks.php'); ?>