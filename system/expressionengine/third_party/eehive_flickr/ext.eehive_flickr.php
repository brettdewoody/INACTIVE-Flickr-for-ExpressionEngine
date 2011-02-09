<?php if ( ! defined('APP_VER')) exit('No direct script access allowed');

require_once PATH_THIRD . 'eehive_flickr/config.php';
require_once PATH_THIRD . 'eehive_flickr/helper.php';

class Eehive_flickr_ext {

	var $name           = EEHIVE_FLICKR_NAME;
	var $version        = EEHIVE_FLICKR_VER;
	var $description    = EEHIVE_FLICKR_DESC;
	var $settings_exist = 'y';
	var $docs_url       = EEHIVE_FLICKR_DOCS;
	
	var $settings = array();
	var $EE;
	var $helper;

	/**
	 * Class Constructor
	 */
	function __construct()
	{
		// Make a local reference to the ExpressionEngine super object
		$this->EE = get_instance();
		
		$this->helper = new Eehive_flickr_helper();
	}
	// END


	/**
	 * Activate Extension
	 */
	function activate_extension()
	{
		// add the row to exp_extensions
		$this->EE->db->insert('extensions', array(
			'class'    => __CLASS__,
			'hook'     => 'wygwam_config',
			'method'   => 'wygwam_config',
			'priority' => 10,
			'version'  => $this->version,
			'settings' => serialize($this->_default_settings()),
			'enabled'  => 'y'
		));
	}
	// END


	/**
	 * Update Extension
	 */
	function update_extension($current = '')
	{
		if($current == '' OR $current == $this->version) {
			return FALSE;
		}

		$this->EE->db->where('class', __CLASS__);
		$this->EE->db->update('extensions', array('version' => $this->version));
	}
	// END


	/**
	 * Disable Extension
	 */
	function disable_extension()
	{
		$this->EE->db->where('class', __CLASS__);
		$this->EE->db->delete('extensions');
	}
	// END


	/**
	 * Retrieves settings from db
	 *
	 * @return void
	 */
	function get_settings()
	{
		// if settings are already in session cache, use those
		if (isset($this->helper->cache['settings'])) return;

		$this->EE->db
					->select('settings')
					->from('extensions')
					->where(array('enabled' => 'y', 'class' => __CLASS__ ))
					->limit(1);
		$query = $this->EE->db->get();
		
		if ($query->num_rows() > 0)
		{
			$this->settings = unserialize($query->row()->settings);
			log_message('debug', 'EEHive Flickr has retrieved settings from DB.');
		}

		$this->helper->cache['settings'] = array_merge($this->_default_settings(), $this->settings);
	}
	// END


	/**
	 * Save settings
	 *
	 * @return 	void
	 */
	function save_settings()
	{
		if (empty($_POST))
		{
			show_error($this->EE->lang->line('unauthorized_access'));
		}

		$settings = '';
		foreach($this->_default_settings() as $key => $val)
		{
			$settings[$key] = $this->EE->input->post($key);
		}

		$this->EE->db->where('class', __CLASS__);
		$this->EE->db->update('extensions', array('settings' => serialize($settings)));
		
		$this->EE->session->set_flashdata(
			'message_success',
		 	$this->EE->lang->line('preferences_updated')
		);
	}
	// END


	/**
	 * Settings Form
	 *
	 * @param	Array	Current settings from DB
	 * @return 	void
	 */
	function settings_form($current)
	{
		$this->EE->load->helper('form');
		$this->EE->load->library('table');

		$this->settings = array_merge($this->_default_settings(), $current);
		
		// init view vars
		$vars = array();
		
		// if we're authenticating Flickr, do so now
		if(isset($_GET['frob']))
		{
			$frob = $_GET['frob'];
	
			require_once(PATH_THIRD . 'eehive_flickr/libraries/Phpflickr.php');
		
			$f = new phpFlickr($this->settings['option_api'], $this->settings['option_secret']);
			$auth_array = $f->auth_getToken($frob);
			$person_array = $f->people_getInfo($auth_array['user']['nsid']);
			
			// If there was an error save it 
			if ($f->getErrorMsg() != '') {
				$vars['message_error'] = $f->getErrorMsg();
			} else {
				$vars['message_success'] = $this->EE->lang->line('activation_success');

				$this->settings['option_auth'] = $auth_array['token'];
				$this->settings['option_nsid'] = $auth_array['user']['nsid'];
				$this->settings['option_photourl'] = $person_array['photosurl'];
				$this->settings['option_profileurl'] = $person_array['profileurl'];
			}
		}
		
		// determine what install stage we are at
		switch(($this->settings['option_auth'] != '') ? 'ok' : 'setup') :

			// before Flickr authentication
			case('setup') :
				$vars['settings'] = array(
					'option_api'	=> form_input(array('name' => 'option_api', 'id' => 'option_api', 'value' => $this->settings['option_api'])),
					'option_secret'	=> form_input(array('name' => 'option_secret', 'id' => 'option_secret', 'value' => $this->settings['option_secret'])),
				);
				
				// If the user has saved their API Key and Secret
				if ($this->settings['option_api'] != '' && $this->settings['option_secret'] != '')
				{
					$sig_str = md5($this->settings['option_secret'] . 'api_key' . $this->settings['option_api'] . 'permsread');
					$vars['activate_url'] = 'http://flickr.com/services/auth/?api_key=' . $this->settings['option_api'] . '&perms=read&api_sig=' . $sig_str;
				}

				// config callback_url
				$vars['callback_url'] = 'http://' . $_SERVER['HTTP_HOST']. $_SERVER['PHP_SELF'] . '?D=cp&C=addons_extensions&M=extension_settings&file=eehive_flickr';
				
				return $this->EE->load->view('settings_setup', $vars, TRUE);			

			break;
			
			// after successfull Flickr authentication
			case('ok') :
				$vars['settings'] = array();
				foreach($this->settings as $key => $val)
				{
					$vars['settings'][$key] = form_input(array('name' => $key, 'id' => $key, 'value' => $val));
				}

				return $this->EE->load->view('settings_ok', $vars, TRUE);			
			break;
			
		endswitch;

	}
	// END


	/**
	 * wygwam_config hook
	 */
	function wygwam_config($config, $settings)
	{
		// If another extension shares the same hook,
		// we need to get the latest and greatest config
		if ($this->EE->extensions->last_call !== FALSE)
		{
			$config = $this->EE->extensions->last_call;
		}

		if(isset($config['toolbar'])) {
			foreach($config['toolbar'] as $i => $toolbar)
			{
				if(is_array($toolbar) && in_array('Image', $toolbar))
				{
					array_unshift($config['toolbar'][$i], 'Flickr');
					break;
				}
			}
		}
		
		// retrieve settings
		$this->get_settings();
		
		// Add CSS
		$this->helper->cp_css();

		// Add scripts
		$this->helper->cp_js();
		$this->EE->cp->add_to_head('<script type="text/javascript">Eehive_flickr = {}; Eehive_flickr.browserUrl = "' . $this->helper->browser_url('wygwam') . '";</script>');
		
		// Return the (modified) config
		return $config;
	}
	// END


	/**
	 * Returns a default array of settings
	 *
	 * @return array default settings & values
	 */
	function _default_settings()
	{
		return array(
			'option_api'		=> '',
			'option_secret'		=> '',
			'option_auth'		=> '',
			'option_nsid'		=> '',
			'option_photourl'	=> '',
			'option_profileurl'	=> ''
		);
	}


}
// END CLASS

	
/* End of file ext.eehive_flickr.php */ 
/* Location: ./system/expressionengine/third_party/eehive_flickr/ext.eehive_flickr.php */