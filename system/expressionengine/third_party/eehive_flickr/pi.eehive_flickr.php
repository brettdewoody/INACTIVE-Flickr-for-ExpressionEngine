<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once PATH_THIRD . 'eehive_flickr/config.php';
require_once PATH_THIRD . 'eehive_flickr/helper.php';

$plugin_info = array(
	'pi_name' => EEHIVE_FLICKR_NAME,
	'pi_version' => EEHIVE_FLICKR_VER,
	'pi_author' => EEHIVE_FLICKR_AUTHOR,
	'pi_author_url' => EEHIVE_FLICKR_DOCS,
	'pi_description' => EEHIVE_FLICKR_DESC,
	'pi_usage' => Eehive_flickr::usage()
);

class Eehive_flickr {

	function __construct()
	{
		$this->EE = get_instance();

		$this->helper = new Eehive_flickr_helper();
	}
	// END

	function photostream() {
		
		$template = $this->EE->TMPL->tagdata;
		
		$numPhotos = $this->EE->TMPL->fetch_param('limit');
		$numPhotos = $numPhotos != '' ?  $numPhotos :  10;
		
		// Load the flickr class
		$flickr = $this->_flickr();
		$f = $flickr[0];
		$flickr_settings = $flickr[1];
		
		// Get the desired size, or default to square
		$sz = $this->_size($this->EE->TMPL->fetch_param('size'));
		
		// Retrieve photostream from Flickr
		$recent = $f->people_getPublicPhotos($flickr_settings['option_nsid'], 1, 'description, url_s, url_m, url_o', $numPhotos, 1);

		// If number of returned photo is less than num
		$numPhotos = min($numPhotos,$recent['photos']['total']);
		
		$flickr_photos = $recent['photos']['photo'];
		
		$variables = array();
		
		for ($i = 0; $i < $numPhotos; $i++) {
			
			// Retrieve the data for each photo
			$flickr_data = $flickr_photos[$i];
			
			$variable_row = array(
				'flickr_img' 			=> 'http://farm' . $flickr_data['farm'] . '.static.flickr.com/' . $flickr_data['server'] . '/' . $flickr_data['id'] . '_' . $flickr_data['secret'] . $sz . '.jpg',
				'flickr_url' 			=> $flickr_settings['option_photourl'] . $flickr_data['id'],
				'flickr_url_small' 		=> $flickr_data['url_s'],
				'flickr_url_medium' 	=> $flickr_data['url_m'],
				'flickr_url_orig' 		=> $flickr_data['url_o'],
				'flickr_title' 			=> $flickr_data['title'],
				'flickr_description' 	=> $flickr_data['description']
			);
			
			$variables[] = $variable_row;

		}
		
		$r = $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $variables);
		
		return $r;
	
  	}
	
	
	
	function favorites() {
		
		$template = $this->EE->TMPL->tagdata;
		
		$numPhotos = $this->EE->TMPL->fetch_param('limit');
		$numPhotos = $numPhotos != '' ?  $numPhotos :  10;
		
		// Load the flickr class
		$flickr = $this->_flickr();
		$f = $flickr[0];
		$flickr_settings = $flickr[1];
		
		// Get the desired size, or default to square
		$sz = $this->_size($this->EE->TMPL->fetch_param('size'));
		
		// Retrieve favorites from Flickr
		$favorites = $f->favorites_getPublicList($flickr_settings['option_nsid'], NULL, NULL, 'description, url_s, url_m, url_o', $numPhotos, 1);
		
		// If number of returned photo is less than num
		$numPhotos = min($numPhotos,$favorites['photos']['total']);
		
		
		$flickr_photos = $favorites['photos']['photo'];
		
		$variables = array();
		
		for ($i = 0; $i < $numPhotos; $i++) {
			
			// Retrieve the data for each photo
			$flickr_data = $flickr_photos[$i];

			$variable_row = array(
				'flickr_img' 			=> 'http://farm' . $flickr_data['farm'] . '.static.flickr.com/' . $flickr_data['server'] . '/' . $flickr_data['id'] . '_' . $flickr_data['secret'] . $sz . '.jpg',
				'flickr_url' 			=> 'http://www.flickr.com/photos/' . $flickr_data['owner'] . '/' . $flickr_data['id'],
				'flickr_url_small' 		=> $flickr_data['url_s'],
				'flickr_url_medium' 	=> $flickr_data['url_m'],
				'flickr_url_orig' 		=> isset($flickr_data['url_o']) ?  $flickr_data['url_o'] :  '',
				'flickr_title' 			=> $flickr_data['title'],
				'flickr_description' 	=> $flickr_data['description']
			);
			
			$variables[] = $variable_row;
		}
		
		$r = $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $variables);
		
		return $r;
	}
	
	
	
	function photosets() {
		
		$template = $this->EE->TMPL->tagdata;
		
		// Load the flickr class
		$flickr = $this->_flickr();
		$f = $flickr[0];
		$flickr_settings = $flickr[1];
		
		// Get the desired size, or default to square
		$sz = $this->_size($this->EE->TMPL->fetch_param('size'));
		
		// Retrieve sets from Flickr
		$set = $f->photosets_getList($flickr_settings['option_nsid']);
		//print_r($set);
		
		$variables = array();
		
		//for ($i = 0; $i < count($set); $i++) {
			
		for($i = 0; $i < count($set['photoset']); $i++) {
			
			// Retrieve the data for each photo
			$photoset = $set['photoset'][$i];
			
			$variable_row = array(
				'set_img' 			=> "http://farm" . $photoset['farm']. ".static.flickr.com/" . $photoset['server'] . "/" . $photoset['primary'] . "_" . $photoset['secret'] . $sz . '.jpg',
				'set_url' 			=> $flickr_settings['option_photourl'] . 'sets/' . $photoset['id'],
				'set_title' 		=> $photoset['title'],
				'set_count' 		=> $photoset['photos'],
				'set_id'			=> $photoset['id']
			);
			
			$variables[] = $variable_row;
			
		}
		
		$r = $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $variables);
		
		return $r;
	}
	
	
	
	function photoset() {
		
		$template = $this->EE->TMPL->tagdata;
		
		$setId = $this->EE->TMPL->fetch_param('set_id');
		
		$numPhotos = $this->EE->TMPL->fetch_param('limit');
		$numPhotos = $numPhotos != '' ?  $numPhotos :  10;
		
		// Load the flickr class
		$flickr = $this->_flickr();
		$f = $flickr[0];
		$flickr_settings = $flickr[1];
		
		// Get the desired size, or default to square
		$sz = $this->_size($this->EE->TMPL->fetch_param('size'));
		
		// Retrieve sets from Flickr
		$set_photos = $f->photosets_getPhotos($setId, 'description, url_s, url_m, url_o');
		
		// If number of returned photo is less than num
		$numPhotos = min($numPhotos,count($set_photos['photoset']['photo']));
		
		$flickr_photos = $set_photos['photoset']['photo'];
		
		$variables = array();
		
		for ($i = 0; $i < $numPhotos; $i++) {
			
			// Retrieve the data for each photo
			$flickr_data = $flickr_photos[$i];
			
			$variable_row = array(
				'flickr_img' 			=> 'http://farm' . $flickr_data['farm'] . '.static.flickr.com/' . $flickr_data['server'] . '/' . $flickr_data['id'] . '_' . $flickr_data['secret'] . $sz . '.jpg',
				'flickr_url' 			=> $flickr_settings['option_photourl'] . '/' . $flickr_data['id'],
				'flickr_url_small' 		=> $flickr_data['url_s'],
				'flickr_url_medium' 	=> $flickr_data['url_m'],
				'flickr_url_orig' 		=> isset($flickr_data['url_o']) ?  $flickr_data['url_o'] :  '',
				'flickr_title' 			=> $flickr_data['title'],
				'flickr_description' 	=> $flickr_data['description']
			);
			
			$variables[] = $variable_row;
		}
		
		$r = $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $variables);
		
		return $r;
	}
	
	
	function groups() {
		
		$template = $this->EE->TMPL->tagdata;
		
		// Load the flickr class
		$flickr = $this->_flickr();
		$f = $flickr[0];
		$flickr_settings = $flickr[1];
		
		// Retrieve sets from Flickr
		$groups = $f->people_getPublicGroups($flickr_settings['option_nsid']);
		
		$variables = array();
			
		foreach ($groups as $group) {
			
			// Retrieve the data for each photo
			$group_info = $f->groups_getInfo($group['nsid']);
			$group_info = $group_info['group'];
			
			$variable_row = array(
				'group_url' 		=> 'http://www.flickr.com/groups/' . $group['nsid'],
				'group_name' 		=> $group['name'],
				'group_id'			=> $group['nsid']
			);
			
			$variables[] = $variable_row;
			
		}
		
		$r = $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $variables);
		
		return $r;
	}
	
	
	
	function groupset() {
		
		$template = $this->EE->TMPL->tagdata;
		
		$groupId = $this->EE->TMPL->fetch_param('group_id');
		
		$numPhotos = $this->EE->TMPL->fetch_param('limit');
		$numPhotos = $numPhotos != '' ?  $numPhotos :  10;
		
		// Load the flickr class
		$flickr = $this->_flickr();
		$f = $flickr[0];
		$flickr_settings = $flickr[1];
		
		// Get the desired size, or default to square
		$sz = $this->_size($this->EE->TMPL->fetch_param('size'));
		
		// Retrieve sets from Flickr
		$group_photos = $f->groups_pools_getPhotos($groupId, NULL, NULL, 'description, url_s, url_m, url_o', $numPhotos, NULL);
		
		$variables = array();
		
		foreach ($group_photos['photo'] as $flickr_data) {
			
			$variable_row = array(
				'flickr_img' 			=> 'http://farm' . $flickr_data['farm'] . '.static.flickr.com/' . $flickr_data['server'] . '/' . $flickr_data['id'] . '_' . $flickr_data['secret'] . $sz . '.jpg',
				'flickr_url' 			=> 'http://www.flickr.com/photos/' .  $flickr_data['owner'] . '/' . $flickr_data['id'],
				'flickr_url_small' 		=> $flickr_data['url_s'],
				'flickr_url_medium' 	=> $flickr_data['url_m'],
				'flickr_url_orig' 		=> isset($flickr_data['url_o']) ?  $flickr_data['url_o'] :  '',
				'flickr_title' 			=> $flickr_data['title'],
				'flickr_description' 	=> $flickr_data['description'],
				'flickr_owner' 			=> $flickr_data['owner']
			);
			
			$variables[] = $variable_row;
		}
		
		$r = $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $variables);
		
		return $r;
	}
	
	
	
	function photo() {
		
		$template = $this->EE->TMPL->tagdata;
		
		$photoId = $this->EE->TMPL->fetch_param('photo_id');
		
		// Load the flickr class
		$flickr = $this->_flickr();
		$f = $flickr[0];
		$flickr_settings = $flickr[1];
		
		// Get the desired size, or default to square
		$sz = $this->_size($this->EE->TMPL->fetch_param('size'));
		
		// Retrieve the photo details from Flickr
		$flickr_data = $f->photos_getInfo($photoId);
		$flickr_comments = $f->photos_comments_getList($photoId);
		$flickr_geo = $f->photos_geo_getLocation($photoId);
		
		$variables = array();
		
		// Get the photo's tags
		$tags = array();
		foreach ($flickr_data['tags']['tag'] as $tag) {
			$tags[] = array(
					'tag_link' 	=> 'http://www.flickr.com/photos/' . $tag['author'] . '/tags/' . $tag['raw'],
					'tag_name'	=> $tag['_content']
			);
		}
	
		// Get the photo's notes
		$notes = array();
		foreach ($flickr_data['notes']['note'] as $note) {
			$notes[] = array(
					'note' 			=> $note['_content'],
					'note_x'		=> $note['x'],
					'note_y'		=> $note['y'],
					'note_width'	=> $note['w'],
					'note_height'	=> $note['h'],
					'note_author'	=> $note['authorname'],
			);
		}
		
		// Get the photo's comments
		$comments = array();
		if (isset($flickr_comments['comments']['comment'])) {
			foreach ($flickr_comments['comments']['comment'] as $comment) {
				$comments[] = array(
						'comment' 			=> $comment['_content'],
						'comment_author' 	=> $comment['authorname'],
						'comment_date' 		=> $comment['datecreate']
				);
			}
		}
		
		$variables[] = array(
				'flickr_img' 			=> 'http://farm' . $flickr_data['farm'] . '.static.flickr.com/' . $flickr_data['server'] . '/' . $flickr_data['id'] . '_' . $flickr_data['secret'] . $sz . '.jpg',
				'flickr_url' 			=> $flickr_settings['option_photourl'] . $flickr_data['id'],
				'flickr_url_small' 		=> 'http://farm' . $flickr_data['farm'] . '.static.flickr.com/' . $flickr_data['server'] . '/' . $flickr_data['id'] . '_' . $flickr_data['secret'] . '_s.jpg',
				'flickr_url_medium' 	=> 'http://farm' . $flickr_data['farm'] . '.static.flickr.com/' . $flickr_data['server'] . '/' . $flickr_data['id'] . '_' . $flickr_data['secret'] . '_m.jpg',
				'flickr_title' 			=> $flickr_data['title'],
				'flickr_description' 	=> $flickr_data['description'],
				'flickr_comment_total' 	=> $flickr_data['comments'],
				'flickr_dateposted'		=> $flickr_data['dates']['posted'],
				'flickr_datetaken'		=> $flickr_data['dates']['taken'],
				'flickr_latitude'		=> $flickr_geo['location']['latitude'],
				'flickr_longitude'		=> $flickr_geo['location']['longitude'],
				'flickr_locality'		=> $flickr_geo['location']['locality']['_content'],
				'tags'					=> $tags,
				'notes'					=> $notes,
				'comments'				=> $comments
			);
		
		$r = $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $variables);
		
		return $r;
	}
	
	
	
	function tagcloud() {
		
		$template = $this->EE->TMPL->tagdata;
		
		// Load the flickr class
		$flickr = $this->_flickr();
		$f = $flickr[0];
		$flickr_settings = $flickr[1];
		
		// Retrieve sets from Flickr
		$tags = $f->tags_getListUserPopular($flickr_settings['option_nsid'],1000);
		
		$fontMin = $this->EE->TMPL->fetch_param('font_min');
		$fontMax = $this->EE->TMPL->fetch_param('font_max');
		$fontMin = $fontMin != '' ?  $fontMin :  11;
		$fontMax = $fontMax != '' ?  $fontMax :  28;
		$size = $fontMin;
					
		$numTags = count($tags);
		sort($tags);
					
		$increment = intval($numTags/($fontMax-$fontMin));
					
		for ($i=0; $i < $numTags; $i++) {
			$output[$tags[$i]['_content']] = $size ;
			if ($increment == 0 || $i % $increment == 0 )  { 
			$size++;
			}
		}
					
		ksort($output);
		
		$variables = array();
		
		foreach ($output as $tg => $sz) {
			
			$variable_row = array(
				'tag_name' 			=> $tg,
				'tag_link' 			=> $flickr_settings['option_photourl'] . 'tags/' . $tg,
				'tag_urlname' 		=> $tg,
				'tag_size' 			=> $sz
			);
			
			$variables[] = $variable_row;
			
		}
		
		$r = $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $variables);
		
		return $r;
	}
	
	
	
	function tagset() {
		
		$template = $this->EE->TMPL->tagdata;
		
		$tag = $this->EE->TMPL->fetch_param('tag');
		
		$numPhotos = $this->EE->TMPL->fetch_param('limit');
		$numPhotos = $numPhotos != '' ?  $numPhotos :  10;
		
		// Load the flickr class
		$flickr = $this->_flickr();
		$f = $flickr[0];
		$flickr_settings = $flickr[1];
		
		// Get the desired size, or default to square
		$sz = $this->_size($this->EE->TMPL->fetch_param('size'));
		
		// Retrieve sets from Flickr
		$search_array = array('user_id' => $flickr_settings['option_nsid'], 'tags' => $tag, 'extras' => "description, url_s, url_m, url_o", 'per_page' => $numPhotos);
		$tag_photos = $f->photos_search($search_array);
		//print_r($tag_photos);
		
		$variables = array();
		
		foreach ($tag_photos['photo'] as $flickr_data) {
			
			$variable_row = array(
				'flickr_img' 			=> 'http://farm' . $flickr_data['farm'] . '.static.flickr.com/' . $flickr_data['server'] . '/' . $flickr_data['id'] . '_' . $flickr_data['secret'] . $sz . '.jpg',
				'flickr_url' 			=> $flickr_settings['option_photourl'] . $flickr_data['id'],
				'flickr_url_small' 		=> $flickr_data['url_s'],
				'flickr_url_medium' 	=> $flickr_data['url_m'],
				'flickr_url_orig' 		=> isset($flickr_data['url_o']) ?  $flickr_data['url_o'] :  '',
				'flickr_title' 			=> $flickr_data['title'],
				'flickr_description' 	=> $flickr_data['description']
			);
			
			$variables[] = $variable_row;
		}
		
		$r = $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $variables);
		
		return $r;
	}
	
	
	
	// HELPER FUNCTIONS

	function _flickr() {
		// need settings from DB to run
		$this->helper->get_settings();

		require_once PATH_THIRD . 'eehive_flickr/libraries/Phpflickr.php';
		
		$f = new Phpflickr($this->helper->cache['settings']['option_api'], $this->helper->cache['settings']['option_secret']);
		$f->setToken($this->helper->cache['settings']['option_auth']);
		
		return array($f, $this->helper->cache['settings']);
	}
	
	
	
	function _size($size) {
		// Default to square
		$size = $size != '' ?  $size :  'square';
		
		switch ($size) {
			case 'square':
				$sz = "_s";
				break;
			case 'thumb':
				$sz = "_t";
				break;
			case 'small':
				$sz = "_m";
				break;
			case 'medium':
				$sz = "";
				break;
			case 'large':
				$sz = "b";
				break;
			default:
      			$sz = "";
		}
		
		return $sz;
	}
	
	
	
	// ----------------------------------------
	//  Plugin Usage
	// ----------------------------------------
	
	// This function describes how the plugin is used.
	//  Make sure and use output buffering
	
	function usage() {
	  ob_start(); 
	  ?>
	The Flickr Plugin is provides several
	tags to incorporate Flickr into your
    website.
	
	{exp:flickr:photostream}
	
	Displays your Flickr photostream
	
	  <?php
	  $buffer = ob_get_contents();
		
	  ob_end_clean(); 
	
	  return $buffer;
	}
	// END


}

/* End of file pi.eehive_flickr.php */
/* Location: ./system/expressionengine/third_party/eehive_flickr/pi.eehive_flickr.php */