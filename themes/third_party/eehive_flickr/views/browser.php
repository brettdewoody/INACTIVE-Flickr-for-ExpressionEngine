<?php
	require_once($_GET['apppath'] . "third_party/eehive_flickr/libraries/Phpflickr.php");
				
	$f = new Phpflickr($_GET['api'], $_GET['secret']);
	$f->setToken($_GET['auth']);
	
	$active = $_GET['v'];
	
	// URL string
	$URLstring = '&apppath=' . $_GET['apppath'] . '&themeurl=' . $_GET['themeurl'] . '&fn=' . $_GET['fn'] . '&photourl=' . $_GET['photourl'] . '&api=' . $_GET['api'] . '&secret=' . $_GET['secret'] . '&token=' . $_GET['token'] . '&nsid=' . $_GET['nsid'];
				
	$flickrURL = getAddress();
			
	// Get the page number		
	$page = isset($_GET['p']) ?  $_GET['p'] :  1;
	$perpage = 45;				
				
	//echo $f->getErrorMsg();
	//print_r($recent);

	function getAddress() {
		$protocol = $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
		return $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}
	
	// rather than replace with zeros, just escape brackets
	function escapejQuery($str) {
		$str = str_replace("[","\\\[",$str);
		$str = str_replace("]","\\\]",$str);
		return $str;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Flickr Browser</title>
<link rel="stylesheet" type="text/css" href="<?=$_GET['themeurl']?>css/browser.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script type="text/javascript" src="<?=$_GET['themeurl']?>javascript/jquery.jscrollpane.js"></script>
<script>
	$(document).ready(function() {						   
		
		$("a.select").click(function() {
			// Get the base URL for the selected image
			var picURL = $(this).attr('rel');
			var picData = $(this).attr('href');
			
			// Set the image URL for the hidden input
			$("input[rel='<?=escapejQuery($_GET['fn'])?>']", top.document).attr('value',picData);
				
			// Add the image URL to the hidden field
			$("#<?=escapejQuery($_GET['fn'])?>_image > a.singleImage > span", top.document).html(picURL);
			$("#<?=escapejQuery($_GET['fn'])?>_image > a.singleImage > img", top.document).attr('src',picURL + '_s.jpg');
			$("#<?=escapejQuery($_GET['fn'])?>_image > a.singleImage", top.document).attr('href',picURL + '.jpg');
			
			// Hide the image chooser
			$("div#<?=escapejQuery($_GET['fn'])?>_chooser", top.document).css("display","none");
				
			// Display the image thumbnail and URL
			$("div#<?=escapejQuery($_GET['fn'])?>_image", top.document).css("display","block");
						
			// Bind Fancybox to the new image chooser																													 
			parent.top.$("a.singleImage").fancybox();
					
			// Close the Fancybox
			parent.top.$.fancybox.close();
				
		});
		
		$(function() {
			$('.scrollpane').jScrollPane({scrollbarWidth:11});
		});

	});
</script>
</head>

<body>
    <div id="flickrBrowser">
        <div id="flickrNav">
            <a <?php if($active == "Main") {echo 'class="active"';}?> href="browser.php?v=Main<?=$URLstring?>">Photostream</a>
            <a <?php if($active == "Sets" || $active == "Set") {echo 'class="active"';}?> href="browser.php?v=Sets<?=$URLstring?>">Sets</a> 
            <a <?php if($active == "Tags" || $active == "Tag") {echo 'class="active"';}?> href="browser.php?v=Tags<?=$URLstring?>">Tags</a>
            <a <?php if($active == "Galleries" || $active == "Gallery") {echo 'class="active"';}?> href="browser.php?v=Galleries<?=$URLstring?>">Galleries</a>
            <a <?php if($active == "Groups" || $active == "Group") {echo 'class="active"';}?> href="browser.php?v=Groups<?=$URLstring?>">Groups</a>
        </div>
		<?php 
			if ($active == "Main") {
				include_once('photostream.php');
			} elseif ($active == "Sets") { 
				include_once('sets.php');
			} elseif ($active == "Set") { 
				include_once('setphotos.php');
			} elseif ($active == "Tags") { 
				include_once('tags.php'); 
			} elseif ($active == "Tag") { 
				include_once('tagphotos.php');
			} elseif ($active == "Galleries") { 
				include_once('galleries.php');
			} elseif ($active == "Gallery") { 
				include_once('galleryphotos.php');
			} elseif ($active == "Groups") { 
				include_once('groups.php');
			} elseif ($active == "Group") { 
				include_once('groupphotos.php');
			} 
		?>
        <div style="clear:both;"></div>
    </div>
</body>
</html>