<?php
	require_once($_GET['apppath'] . "third_party/eehive_flickr/libraries/Phpflickr.php");
				
	$f = new Phpflickr($_GET['api'], $_GET['secret']);
	$auth = (isset($_GET['auth'])) ? $_GET['auth'] : '';
	$f->setToken($auth);
	
	$active = $_GET['v'];
	
	// URL string
	$URLstring = '&apppath=' . $_GET['apppath'] . '&themeurl=' . $_GET['themeurl'] . '&fn=' . $_GET['fn'] . '&photourl=' . $_GET['photourl'] . '&api=' . $_GET['api'] . '&secret=' . $_GET['secret'] . '&token=' . $_GET['token'] . '&nsid=' . $_GET['nsid'];
				
	$flickrURL = getAddress();
			
	// Get the page number		
	$page = isset($_GET['p']) ?  $_GET['p'] :  1;
	$perpage = 45;				
				
	function getAddress() {
		$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
		return $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}
	
	// rather than replace with zeros, just escape brackets
	function escapejQuery($str) {
		$str = str_replace("[","\\\[",$str);
		$str = str_replace("]","\\\]",$str);
		return $str;
	}
	
	// Have views use same function for generating thumbs
	function makeImgBox($photo)
	{
		$photoBaseURL = "http://farm" . $photo['farm']. ".static.flickr.com/" . $photo['server'] . "/" . $photo['id'] . "_" . $photo['secret'];
		$photoID = $photo['id'];
		$photoUser = $photo['owner'];
		$photoSize = "";
		$photoTitle = $photo['title'];
		$photoDescription = $photo['description'];
		$photoArray = urlencode(serialize(array($photoBaseURL, $photoID, $photoSize, $photoTitle, $photoDescription, $photoUser)));

		$photoBaseURL = "http://farm" . $photo['farm']. ".static.flickr.com/" . $photo['server'] . "/" . $photo['id'] . "_" . $photo['secret'];
		$photoID = $photo['id'];
		$photoSize = '';
		$photoTitle = $photo['title'];
		$photoDescription = $photo['description'];
		$photoArray = urlencode(serialize(array($photoBaseURL, $photoID, $photoSize, $photoTitle, $photoDescription)));
		$photoData = json_encode(array(
			'size_t' => isset($photo['url_t']) ? $photo['width_t'] . ':' . $photo['height_t'] : '',
			'size_s' => isset($photo['url_s']) ? $photo['width_s'] . ':' . $photo['height_s'] : '',
			'size_m' => isset($photo['url_m']) ? $photo['width_m'] . ':' . $photo['height_m'] : '',
			'size_z' => isset($photo['url_z']) ? $photo['width_z'] . ':' . $photo['height_z'] : '',
			'size_l' => isset($photo['url_l']) ? $photo['width_l'] . ':' . $photo['height_l'] : ''
		));

		$r = '';
		$r .= '<div class="imgBox">';
		$r .= '<a class="select" href="' . $photoArray . '" rel="' . $photoBaseURL . '" data-extras=\'' . $photoData . '\' onClick="return false;">';
		$r .= '<img src="' . $photoBaseURL . '_s.jpg"' . ' height="75" width="75"/>';
		$r .= '</a>';
		$r .= '</div>';
		
		return $r;
	}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Flickr Browser</title>
<link rel="stylesheet" type="text/css" href="<?=$_GET['themeurl']?>css/browser.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
<script type="text/javascript" src="<?=$_GET['themeurl']?>javascript/jquery.jscrollpane.js"></script>
<script>

	// helper function to format url based on requested size
	function returnImageUrl(url, prefix)
	{
		switch(prefix)
		{
			case 'x':
				return url + '_s.jpg';
			break;
			
			case 't':
				return url + '_t.jpg';
			break;
			
			case 's':
				return url + '_m.jpg';
			break;
			
			case 'm':
				return url + '.jpg';
			break;
			
			case 'z':
				return url + '_z.jpg';
			break;

			case 'l':
				return url + '_l.jpg';
			break;
		}
	}
	
	// helper function to only show available sizes
	function showHideLabel(val, size)
	{
		if(val)
		{
			var sizes = val.split(':');
			$('label[for=size_' + size + '] .dims').html(sizes[0] + 'px wide by ' + sizes[1] + 'px tall');
		}
		else
		{
			$('label[for=size_' + size + '] .dims').html('');
			$('label[for=size_' + size + ']').css('display', 'none');
		}
	}

	$(document).ready(function() {
	
		// determine mode by what is passed in URL
		var mode = ('<?php echo $_GET['fn']; ?>' == 'wygwam') ? 'wygwam' : 'fieldtype';
		
		// set up some vars & events specific to wygwam mode
		if(mode == 'wygwam')
		{
			var $sizeChooser = $('#sizeChooser');
			var $imageUrl = $('#image_url');
	
			// handle clicking 'cancel'
			$('#sizeChooserCancel').click(function(e) {
				e.preventDefault();
				$imageUrl.val('');

				// uncheck anything
				$('input[name=size]:checked', $sizeChooser).attr('checked', false);

				$sizeChooser.fadeOut();
			});
			
			// handle clicking of 'select'
			$('form', $sizeChooser).submit(function(e) {
				e.preventDefault();

				// only proceed if a size is selected
				if($('input[name=size]:checked', $sizeChooser).size() > 0) {
					window.parent.CKEDITOR.dialog.getCurrent()._.editor.insertHtml('<img src="' + returnImageUrl($imageUrl.val(), $('input[name=size]:checked', $sizeChooser).val()) + '" />');
					window.parent.CKEDITOR.dialog.getCurrent().hide();
				}
			});
		}
		
		$("a.select").click(function() {
		
			// determine if in wygwam mode or not
			if(mode == 'wygwam')
			{
				var $THIS = $(this);
				$('.img_holder', $sizeChooser).html('').append($THIS.find('img').clone());
				$imageUrl.val($(this).attr('rel'));

				showHideLabel($THIS.data('extras').size_o, 'o');
				showHideLabel($THIS.data('extras').size_t, 't');
				showHideLabel($THIS.data('extras').size_s, 's');
				showHideLabel($THIS.data('extras').size_m, 'm');
				showHideLabel($THIS.data('extras').size_z, 'z');
				showHideLabel($THIS.data('extras').size_l, 'l');

				$sizeChooser.fadeIn();
			}
			else
			{
				// Get the base URL for the selected image
				var picURL = $(this).attr('rel');
				var picData = $(this).attr('href');
				
				// Set the image URL for the hidden input
				$("input[rel='<?=escapejQuery($_GET['fn'])?>']", top.document).attr('value',picData);
					
				// Add the image URL to the hidden field
				$("#<?=escapejQuery($_GET['fn'])?>_image > a.singleImage > span", top.document).html(picURL);
				$("#<?=escapejQuery($_GET['fn'])?>_image > a.singleImage > img", top.document).attr('src', returnImageUrl(picURL, 'x'));
				$("#<?=escapejQuery($_GET['fn'])?>_image > a.singleImage", top.document).attr('href', returnImageUrl(picURL, 'm'));
				
				// Hide the image chooser
				$("div#<?=escapejQuery($_GET['fn'])?>_chooser", top.document).css("display","none");
					
				// Display the image thumbnail and URL
				$("div#<?=escapejQuery($_GET['fn'])?>_image", top.document).css("display","block");
							
				// Bind Fancybox to the new image chooser																													 
				parent.top.$("a.singleImage").fancybox();
						
				// Close the Fancybox
				parent.top.$.fancybox.close();
			}
				
		});
		
		$(function() {
			$('.scrollpane').jScrollPane({scrollbarWidth:11});
		});

	});
</script>
</head>

<body>
	<div id="sizeChooser">
		<span class="img_holder"></span>
		<form>
			<label for="size_x"><input name="size" id="size_x" type="radio" value="x" /> 75px Square</label><br />
			<label for="size_t"><input name="size" id="size_t"type="radio" value="t" /> Thumbnail - <span class="dims"></span></label><br />
			<label for="size_s"><input name="size" id="size_s"type="radio" value="s" /> Small - <span class="dims"></span></label><br />
			<label for="size_m"><input name="size" id="size_m"type="radio" value="m" /> Medium 1 - <span class="dims"></span></label><br />
			<label for="size_z"><input name="size" id="size_z"type="radio" value="z" /> Medium 2 - <span class="dims"></span></label><br />
			<label for="size_l"><input name="size" id="size_l"type="radio" value="l" /> Large - <span class="dims"></span></label><br />
			<input type="hidden" name="image_url" id="image_url" value="" />
			<button id="sizeChooserOK">Insert</button> or <a id="sizeChooserCancel">cancel</a>
		</form>
	</div>

    <div id="flickrBrowser">
        <div id="flickrNav">
            <a <?php if($active == "Main") {echo 'class="active"';}?> href="browser.php?v=Main<?=$URLstring?>">Photostream</a>
            <a <?php if($active == "Sets" || $active == "Set") {echo 'class="active"';}?> href="browser.php?v=Sets<?=$URLstring?>">Sets</a> 
            <a <?php if($active == "Tags" || $active == "Tag") {echo 'class="active"';}?> href="browser.php?v=Tags<?=$URLstring?>">Tags</a>
            <a <?php if($active == "Galleries" || $active == "Gallery") {echo 'class="active"';}?> href="browser.php?v=Galleries<?=$URLstring?>">Galleries</a>
            <a <?php if($active == "Groups" || $active == "Group") {echo 'class="active"';}?> href="browser.php?v=Groups<?=$URLstring?>">Groups</a>
        </div>
		<?php
		
			// what extras do we want from flickr?
			$f_api_extras = 'description,url_sq,url_t,url_s,url_m,url_z,url_l,url_o';
		
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