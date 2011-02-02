<h1>Your photostream</h1>
<div id="flickrContent">
<?php
			
	// Retrieve photos from Flickr
	$recent = $f->people_getPublicPhotos($_GET['nsid'],'','description', $perpage, $page);
				
	//print_r($recent);
	
	// Get total pages and total photos
	$totalPages = $recent['photos']['pages'];
	$totalItems = $recent['photos']['total'];
			
	// Initialize the $images var
	$images = '';
									
	foreach($recent['photos']['photo'] as $photo) {
		$photoBaseURL = "http://farm" . $photo['farm']. ".static.flickr.com/" . $photo['server'] . "/" . $photo['id'] . "_" . $photo['secret'];
		$photoID = $photo['id'];
		$photoSize = "";
		$photoTitle = $photo['title'];
		$photoDescription = $photo['description'];
		$photoArray = urlencode(serialize(array($photoBaseURL, $photoID, $photoSize, $photoTitle, $photoDescription)));
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