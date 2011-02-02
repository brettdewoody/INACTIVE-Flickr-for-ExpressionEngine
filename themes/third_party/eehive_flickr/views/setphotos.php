<?php
	$setPhotos = $f->photosets_getPhotos($_GET['set'],'description','',$perpage, $page,'photos');
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