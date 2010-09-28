<h1>Your Groups</h1>
<div id="flickrContent" class="scrollpane narrower" style="overflow-y:scroll; overflow-x:hidden;">
<?php
	// Retrieve galleries from Flickr
	$groups = $f->people_getPublicGroups($_GET['nsid']);
	
	//print_r($groups);
				
	$images = '';
				
	$count = 0;
	foreach($groups as $group) {
		$group_info = $f->groups_getInfo($group['nsid']);
		$group_info = $group_info['group'];
		$group_photo = $f->groups_pools_getPhotos($group['nsid'],NULL,NULL,NULL,1,1);
		$group_photo = $group_photo['photo'][0];
		//print_r($group_photo);
		
		$coverPic = "http://farm" . $group_photo['farm']. ".static.flickr.com/" . $group_photo['server'] . "/" . $group_photo['id'] . "_" . $group_photo['secret'] . '_s.jpg';
		echo '<div class="groupBox">';
		echo '<a class="groupImg" href="browser.php?v=Group&group=' . $group['nsid'] . '&groupname=' . $group_info['name'] . '&' . $URLstring . '"><img align="left" src="' . $coverPic . '" alt="' . $group_info['title'] . '" /></a>';
		echo '<div class="text"><a href="browser.php?v=Group&group=' . $group['nsid'] . '&groupname=' . $group_info['name'] . '&' . $URLstring . '">' . $group_info['name'] . '</a></div>';
		echo '<div class="description">' . $group_info['description'] . '</div>';
		echo '</div>';
		echo '<div class="clear"></div>';
		if ($count == 5 || $count == 11 || $count == 17){echo '<div class="clear"></div>';}
		$count++;
	}
?>
</div>