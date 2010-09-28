<h1><em><?=$_GET['groupname']?></em> Group</h1>
<div id="flickrContent" style="overflow-y:scroll; overflow-x:hidden;">
<?php
	// Retrieve galleries from Flickr
	$group = $f->groups_pools_getPhotos($_GET['group'],NULL,NULL,NULL,$perpage, $page);
	
	//print_r($group);
				
	// Get total pages and total photos
	$totalPages = $group['pages'];
	$totalItems = $group['total'];
			
	// Initialize the $images var 
	$images = '';
									
	foreach($group['photo'] as $photo) {
		$photoBaseURL = "http://farm" . $photo['farm']. ".static.flickr.com/" . $photo['server'] . "/" . $photo['id'] . "_" . $photo['secret'];
		$photoID = $photo['id'];
		$photoUser = $photo['owner'];
		$photoSize = "";
		$photoTitle = $photo['title'];
		$photoDescription = $photo['description'];
		$photoArray = urlencode(serialize(array($photoBaseURL, $photoID, $photoSize, $photoTitle, $photoDescription, $photoUser)));
		$images .= '<div class="imgBox">';
		$images .= '<a class="select" href="' . $photoArray . '" rel="' . $photoBaseURL . '" onClick="return false;">';
		$images .= '<img src="' . $photoBaseURL . '_s.jpg"' . ' height="75" width="75"/>';
		$images .= '</a>';
		$images .= '</div>';
	}
				
	echo $images;
?>
</div>
<?php include_once('pagelinks.php'); ?>
?>
</div>