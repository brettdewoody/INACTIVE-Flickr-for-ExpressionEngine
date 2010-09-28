<?php
	$galleryPhotos = $f->galleries_getPhotos($_GET['gallery'],'description',$perpage, $page);
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