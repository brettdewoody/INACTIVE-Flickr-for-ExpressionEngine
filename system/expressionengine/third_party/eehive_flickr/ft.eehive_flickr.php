<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Eehive_flickr_ft extends EE_Fieldtype {
	
	var $info = array(
		'name'		=> 'Flickr',
		'version'	=> '2.1.0',
		'pi_author' =>'EE Hive - Brett DeWoody',
 	 	'pi_author_url' => 'http://www.ee-hive.com/expressionengine-2/flickr',
  		'pi_description' => 'Provides tags for integrating Flickr into your website'
	);
	
	function Eehive_flickr_ft() {
		parent::EE_Fieldtype();
	}
	
	/**
	 * Display Global Settings
	 *
	 * @access	public
	 * @return	form contents
	 *
	 */
	function display_global_settings() {
		$val = array_merge($this->settings, $_POST);
		
		// Add script tags
		$this->_cp_js();
		
		$value_api = 	isset($val['option_api']) ?  $val['option_api'] :  '';
		$value_secret = isset($val['option_secret']) ?  $val['option_secret'] :  '';
		
		// If the user is receiving the FROB then retrieve the AUTH token
		if (isset($_GET['frob'])) {
			
			$frob = $_GET['frob'];
	
			require_once('libraries/Phpflickr.php');
		
			$f = new phpFlickr($value_api, $value_secret);
			
			$auth_array = $f->auth_getToken($frob);
			
			$person_array = $f->people_getInfo($auth_array['user']['nsid']);
			
			// If there was an error save it 
			if ($f->getErrorMsg() != '') {
				$error = $f->getErrorMsg();
			}
			
			$val['option_auth'] = $auth_array['token'];
			$val['option_nsid'] = $auth_array['user']['nsid'];
			$val['option_photourl'] = $person_array['photosurl'];
			$val['option_profileurl'] = $person_array['profileurl'];
			
		}
		
		$value_auth = 	isset($val['option_auth']) ?  $val['option_auth'] :  '';
		$value_nsid = 	isset($val['option_nsid']) ?  $val['option_nsid'] :  '';
		$value_photourl = 	isset($val['option_photourl']) ?  $val['option_photourl'] :  '';
		$value_profileurl = 	isset($val['option_profileurl']) ?  $val['option_profileurl'] :  '';
		
		$r = '';
		
		$r .= form_label('Flickr API', 'option_api').NBS.form_input('option_api', $value_api);
		$r .= form_label('Flickr Secret', 'option_secret').NBS.form_input('option_secret', $value_secret); 
		
		if ($value_api != '' && $value_secret != '' && $value_auth != '') {
		$r .= form_label('Flickr User ID', 'option_nsid').NBS.form_input('option_nsid', $value_nsid);
			
		$r .= form_label('Flickr Token', 'option_auth').NBS.form_input('option_auth', $value_auth);
		
		$r .= form_label('Flickr Photo URL', 'option_photourl').NBS.form_input('option_photourl', $value_photourl);
			
		$r .= form_label('Flickr Profile URL', 'option_profileurl').NBS.form_input('option_profileurl', $value_profileurl);
		}
		
		// Generate the Callback URL for Flickr
		if ($value_auth == '') {
			$callbackURL =  'http://' . $_SERVER['HTTP_HOST']. $_SERVER['PHP_SELF'] . '?D=cp&C=addons_fieldtypes&M=global_settings&ft=eehive_flickr<br/>';
			
			$r .= '<br/><br/>Your callback URL is: <span style="font-weight:bold; color:#999;">' . $callbackURL . '</span>';
		}
		
		// If the user has saved their API Key and Secret
		if ($value_api != '' && $value_secret != '' && $value_auth == '') {
			
			$sig_str = md5($value_secret . 'api_key' . $value_api . 'permsread');
			
			$r .= '<br/><a style="font-weight:bold; color:#86c950;" href="http://flickr.com/services/auth/?api_key=' . $value_api . '&perms=read&api_sig=' . $sig_str . '">Click to activate Flickr</a><br/><br/>';
		
		}
		

		return $r;
	}
	// --------------------------------------------------------------------
	
	
	
	
	/**
	 * Save Global Settings
	 *
	 * @access	public
	 * @return	global settings
	 *
	 */
	function save_global_settings() {
		return array_merge($this->settings, $_POST);
	}
	
	// --------------------------------------------------------------------
	
	
	
	
	/**
	 * Display Field on Publish
	 *
	 * @access	public
	 * @param	existing data
	 * @return	field html
	 *
	 */
	function display_field($data) {
		
		// Load helpers
		$this->EE->load->helper('url');
		
		// Initialize vars
		$displayChooser = 'display:block; ';
		$displayImage = 'display:block; ';
		$pic = '';
		$picRaw = isset($data) ?  $data :  '';
		$picSquare = '';
		$picMedium = '';
		
		// Flickr Vars
		$flickrField = $this->field_name;
		$flickrAPI = $this->settings['option_api'];
		$flickrSecret = $this->settings['option_secret'];
		$flickrToken = $this->settings['option_auth'];
		$flickrNSID = $this->settings['option_nsid'];
		$flickrPhotoURL = $this->settings['option_photourl'];
		$flickrURL = uri_string();
		
		
		// If a pic has already been selected read it in and unserialize it
		if ($picRaw != '') {
			$picArray = unserialize(urldecode($picRaw));
			$pic = $picArray[0];
			$picSquare = $pic . '_s.jpg';
			$picMedium = $pic . '.jpg';
			
			// Set the Image Chooser to display:none
			$displayChooser = 'display:none; ';
		} else {
			
			// Set the Image to display:none;
			$displayImage = 'display:none; ';
		}
		
		// Add scripts
		$this->_cp_js();
		
		// Add CSS
		$this->_cp_css();
		
		$r = '';
		$r .= '<div class="flickrContainer" id="flickrContainer_' . $this->escapejQuery($flickrField) . '">';
		$r .= '<input type="hidden" class="flickrInput" id="' . $flickrField . '" rel="' . $this->escapejQuery($flickrField) . '" name="' . $flickrField . '" value="' . $picRaw . '" />';
		$r .= '<div class="flickrChooser" id="' . $this->escapejQuery($flickrField) . '_chooser" style="' . $displayChooser . '">
			<a  class="flickrInput" href="' . $this->_theme_url() . 'views/browser.php?v=Main&apppath=' . APPPATH . '&themeurl=' . $this->_theme_url() . '&fn=' . $flickrField . '&photourl=' . $flickrPhotoURL . '&api=' . $flickrAPI . '&secret=' . $flickrSecret . '&token=' . $flickrToken . '&nsid=' . $flickrNSID . '" onClick="return false;"><button >Choose Photo</button></a> No photo chosen
		</div>';
		$r .= '<div class="flickrImage" id="' . $this->escapejQuery($flickrField) . '_image" style="' . $displayImage . '">
			<a class="singleImage" href="' . $picMedium . '">
				<img src="' . $picSquare . '" align="left" />
				<span>' . $pic . '</span>
			</a> 
			<a class="flickrTrash" href="#" rel="' . $this->escapejQuery($flickrField) . '" onClick="return false;"><img src="' . $this->_theme_url() . 'images/trash-icon.gif" /></a>
		</div>';
		$r .= '</div>';

		
		return $r;
	}
	
	
	
	function display_cell($data) {
		
		return $this->display_field($data);
		
	}
	
	
	
	// TAG: Display the photo, in the appropriate size
	function replace_tag($data, $params = array(), $tagdata = FALSE) {
		
		// Unserialize the photo data
		$picArray = unserialize(urldecode($data));
		$pic = $picArray[0];
		
		$r = '';
		
		$r .= $pic;
		
		if (isset($params['size'])) {
			$size = $params['size'];
			if($size == 'square') {
				$r .= "_s.jpg";
			}
			if  ($size == "thumb") {
				$r .= "_t.jpg";
			}
			if  ($size == "small") {
				$r .= "_m.jpg";
			}
			if  ($size == "medium") {
				$r .= ".jpg";
			}
			if  ($size == "large") {
				$r .= "_b.jpg";
			}
		} else {
			// Else display the medium size
			$r .= ".jpg";
		}
		
		return $r;
		
	}
	
	
	// TAG: Display the photo's title
	function replace_title($data, $params = array(), $tagdata = FALSE) {
		// Unserialize the photo data
		$picArray = unserialize(urldecode($data));
		$title = $picArray[3];
		
		return $title;
	}
	
	
	// TAG: Display the photo's description
	function replace_description($data, $params = array(), $tagdata = FALSE) {
		// Unserialize the photo data
		$picArray = unserialize(urldecode($data));
		$description = $picArray[4];
		
		return $description;
	}
	
	// TAG: Display the photo's owner
	function replace_owner($data, $params = array(), $tagdata = FALSE) {
		// Unserialize the photo data
		$picArray = unserialize(urldecode($data));
		if (isset($picArray[5])) {
			$owner = $picArray[5];
		} else {
			$owner = NULL;
		}
		
		return $owner;
	}
	
	
	// TAG: Display the photo's page URL
	function replace_link($data, $params = array(), $tagdata = FALSE) {
		
		// Pull in the site settings
		$settings = $this->settings;
		
		// Unserialize the photo data
		$picArray = unserialize(urldecode($data));
		$id = $picArray[1];
		
		$link = '';
		
		if (isset($picArray[5])) {
			$userID = $picArray[5];
		} else {
			$userID = $settings['option_nsid'];
		}
		
		$link .= 'http://www.flickr.com/photos/' . $userID . '/' . $id;
		
		return $link;
	}
	

	
	function _cp_js() {	
		$this->EE->cp->add_to_head('<script type="text/javascript" src="' . $this->_theme_url() . 'javascript/fancybox/jquery.fancybox-1.3.1.pack.js"></script>');
		$this->EE->cp->add_to_head('<script type="text/javascript" src="' . $this->_theme_url() . 'javascript/jquery.flickr.js"></script>');
		$this->EE->cp->add_to_head('<script type="text/javascript" src="' . $this->_theme_url() . 'javascript/jquery.jscrollpane.js"></script>');
	}
	
	
	
	
	function _cp_css() {
		$this->EE->cp->add_to_head('<link rel="stylesheet" type="text/css" href="' . $this->_theme_url() . 'css/jquery.fancybox-1.3.1.css" />');
		$this->EE->cp->add_to_head('<link rel="stylesheet" type="text/css" href="' . $this->_theme_url() . 'css/flickr.css" />');
	}
	
	
	
	
	function _cp_url() {
		if (! isset($this->cache['cp_url'])) {
			$cp_folder_url = $this->EE->config->item('cp_url');
			if (substr($cp_folder_url, -9) == 'index.php') $cp_folder_url = substr($cp_folder_url,0,-9);
			if (substr($cp_folder_url, -1) != '/') $cp_folder_url .= '/';
			$this->cache['cp_url'] = $cp_folder_url;
		}

		return $this->cache['cp_url'];
	}
	
	
	
	function _theme_url() {
		if (! isset($this->cache['theme_url'])) {
			$theme_folder_url = $this->EE->config->item('theme_folder_url');
			if (substr($theme_folder_url, -1) != '/') $theme_folder_url .= '/';
			$this->cache['theme_url'] = $theme_folder_url.'eehive_flickr/';
		}

		return $this->cache['theme_url'];
	}
	
	
	
	// Function to escape necessary jQuery characters
	// Bug in jQuery prevents us from merely escaping special characters. So instead
	// we'll replace them with zeros.
	function escapejQuery($str) {
		$str = str_replace("[","0",$str);
		$str = str_replace("]","0",$str);
		return $str;
	}
	
	
}

/* End of file ft.google_maps.php */
/* Location: ./system/expressionengine/third_party/google_maps/ft.google_maps.php */