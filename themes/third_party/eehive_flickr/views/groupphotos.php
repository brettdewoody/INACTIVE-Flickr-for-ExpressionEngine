<h1><em><?=$_GET['groupname']?></em> Group</h1>
<div id="flickrContent" style="overflow-y:scroll; overflow-x:hidden;">
<?php
	// Retrieve galleries from Flickr
	$group = $f->groups_pools_getPhotos($_GET['group'],NULL,NULL,$f_api_extras,$perpage, $page);
	
	//print_r($group);
				
	// Get total pages and total photos
	$totalPages = $group['pages'];
	$totalItems = $group['total'];
			
	// Initialize the $images var 
	$images = '';
									
	foreach($group['photo'] as $photo) {
		$images .= makeImgBox($photo);
	}
				
	echo $images;
?>
</div>
<?php include_once('pagelinks.php'); ?>
?>
</div>