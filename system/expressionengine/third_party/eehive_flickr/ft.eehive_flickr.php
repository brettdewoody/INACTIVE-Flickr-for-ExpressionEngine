<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once PATH_THIRD . 'eehive_flickr/config.php';
require_once PATH_THIRD . 'eehive_flickr/helper.php';

class Eehive_flickr_ft extends EE_Fieldtype {
	
	var $info = array(
		'name'		=> EEHIVE_FLICKR_NAME,
		'version'	=> EEHIVE_FLICKR_VER,
		'pi_author' => EEHIVE_FLICKR_AUTHOR,
 	 	'pi_author_url' => EEHIVE_FLICKR_DOCS,
  		'pi_description' => EEHIVE_FLICKR_DESC
	);

	function __construct()
	{
		parent::__construct();

		$this->helper = new Eehive_flickr_helper();
	}
		
	
	/**
	 * Display field
	 *
	 * @access	public
	 * @param	existing data
	 * @return	field html
	 *
	 */
	function display_field($data)
	{
		return $this->_display_field($data, $this->field_name);
	}
	// END

	
	/**
	 * Display matrix cell
	 *
	 * @access	public
	 * @param	existing data
	 * @return	field html
	 *
	 */
	function display_cell($data)
	{
		return $this->_display_field($data, $this->cell_name);
	}
	// END

	
	/**
	 * Display Field on Publish
	 *
	 * @access	protected
	 * @param	existing data
	 * @param	field/cell name
	 * @return	field html
	 *
	 */
	function _display_field($data, $field_name) {
		
		// Load helpers
		$this->EE->load->helper('url');
		
		// Initialize vars
		$displayChooser = 'display:block; ';
		$displayImage = 'display:block; ';
		$pic = '';
		$picRaw = isset($data) ?  $data :  '';
		$picSquare = '';
		$picMedium = '';
		
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
		$this->helper->cp_js();
		
		// Add CSS
		$this->helper->cp_css();
		
		$r = '';
		$r .= '<div class="flickrContainer" id="flickrContainer_' . $field_name . '">';
		$r .= '<input type="hidden" class="flickrInput" id="' . $field_name . '" rel="' . $field_name . '" name="' . $field_name . '" value="' . $picRaw . '" />';
		$r .= '<div class="flickrChooser" id="' . $field_name . '_chooser" style="' . $displayChooser . '">
			<a  class="flickrInput" href="' . $this->helper->browser_url($field_name) . '" onClick="return false;"><button >Choose Photo</button></a> No photo chosen
		</div>';
		$r .= '<div class="flickrImage" id="' . $field_name . '_image" style="' . $displayImage . '">
			<a class="singleImage" href="' . $picMedium . '">
				<img src="' . $picSquare . '" align="left" />
				<span>' . $pic . '</span>
			</a> 
			<a class="flickrTrash" href="#" rel="' . $field_name . '" onClick="return false;"><img src="' . $this->helper->theme_url() . 'images/trash-icon.gif" /></a>
		</div>';
		$r .= '</div>';

		
		return $r;
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
		$settings = $this->helper->get_settings();
		
		// Unserialize the photo data
		$picArray = unserialize(urldecode($data));
		$id = $picArray[1];
		
		$link = '';
		
		if (isset($picArray[5])) {
			$userID = $picArray[5];
		} else {
			$userID = $this->helper->cache['settings']['option_nsid'];
		}
		
		$link .= 'http://www.flickr.com/photos/' . $userID . '/' . $id;
		
		return $link;
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
			if  ($size == "thumb" || $size == 'thumbnail') {
				$r .= "_t.jpg";
			}
			if  ($size == "small") {
				$r .= "_m.jpg";
			}
			if  ($size == "medium" || $size == 'medium_500') {
				$r .= ".jpg";
			}
			if  ($size == "medium_640") {
				$r .= "_z.jpg";
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
		$this->helper->get_settings();
		
		// Unserialize the photo data
		$picArray = unserialize(urldecode($data));
		$id = $picArray[1];
		
		$link = '';
		
		if (isset($picArray[5])) {
			$userID = $picArray[5];
		} else {
			$userID = $this->helper->cache['settings']['option_nsid'];
		}
		
		$link .= 'http://www.flickr.com/photos/' . $userID . '/' . $id;
		
		return $link;
	}

}

/* End of file ft.eehive_flickr.php */
/* Location: ./system/expressionengine/third_party/eehive_flickr/ft.eehive_flickr.php */