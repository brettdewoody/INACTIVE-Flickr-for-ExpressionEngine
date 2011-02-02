<h1>Photos tagged with <em><?=$_GET['tag']?></em></h1>
<div id="flickrContent">
<?php
	$tagPhotos = $f->photos_search(array("user_id"=>$_GET['nsid'],"tags"=>$_GET['tag'],"extras"=>'description',"page"=>$page,"per_page"=>$perpage,"content_type"=>1));
				
	//print_r($tagPhotos);
				
	// Get total pages and total photos
	$totalPages = $tagPhotos['pages'];
	$totalItems = $tagPhotos['total'];
				
	// Initialize the $images var
	$images = '';
									
	foreach($tagPhotos['photo'] as $photo) {
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