<?php if ( ! defined('APP_VER')) exit('No direct script access allowed');

class Eehive_flickr_helper {

	public $EE;
	public $cache;

	function __construct()
	{
		$this->EE = get_instance();

		if( ! isset($this->EE->session->cache['eehive_flickr']))
		{
			$this->EE->session->cache['eehive_flickr'] = array();
		}
		$this->cache =& $this->EE->session->cache['eehive_flickr'];
	}
	// END


	function browser_url($fn)
	{
		// need settings from DB to run
		$this->get_settings();

		return $this->theme_url()
			. 'views/browser.php?v=Main&apppath=' . APPPATH
			. '&themeurl=' . $this->theme_url()
			. '&fn=' . $fn
			. '&photourl=' . $this->cache['settings']['option_photourl']
			. '&api=' . $this->cache['settings']['option_api']
			. '&secret=' . $this->cache['settings']['option_secret']
			. '&token=' . $this->cache['settings']['option_auth']
			. '&nsid=' . $this->cache['settings']['option_nsid'];
	}
	// END


	function cp_css()
	{
		// if our cache key exists, we've already loaded js once so don't do it again
		if( ! isset($this->cache['cp_css']))
		{
			$this->EE->cp->add_to_head('<link rel="stylesheet" type="text/css" href="' . $this->theme_url() . 'css/jquery.fancybox-1.3.1.css" />');
			$this->EE->cp->add_to_head('<link rel="stylesheet" type="text/css" href="' . $this->theme_url() . 'css/flickr.css" />');
			$this->cache['cp_css'] = TRUE;
		}
	}
	// END
	

	function cp_js()
	{	
		// if our cache key exists, we've already loaded js once so don't do it again
		if( ! isset($this->cache['cp_js']))
		{
			$this->EE->cp->add_to_head('<script type="text/javascript" src="' . $this->theme_url() . 'javascript/fancybox/jquery.fancybox-1.3.1.pack.js"></script>');
			$this->EE->cp->add_to_head('<script type="text/javascript" src="' . $this->theme_url() . 'javascript/jquery.flickr.js"></script>');
			$this->EE->cp->add_to_head('<script type="text/javascript" src="' . $this->theme_url() . 'javascript/jquery.jscrollpane.js"></script>');
			$this->EE->cp->add_to_head('<script type="text/javascript">Eehive_flickr = {}; Eehive_flickr.browserUrl = "' . $this->browser_url('wygwam') . '";</script>');
		
			$this->cache['cp_js'] = TRUE;
		}
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

		// Get settings with help of our extension
		if ( ! class_exists('Eehive_flickr_ext'))
		{
			require_once(PATH_THIRD . 'eehive_flickr/ext.eehive_flickr.php');
		}

		$this->ext = new Eehive_flickr_ext();
		$this->cache['settings'] = $this->ext->get_settings();
	}
	// END


	function theme_url()
	{
		if( ! isset($this->cache['theme_url']))
		{
			$theme_folder_url = $this->EE->config->item('theme_folder_url');
			$this->cache['theme_url'] = $this->EE->functions->remove_double_slashes($theme_folder_url . '/third_party/eehive_flickr/');
		}

		return $this->cache['theme_url'];
	}
	// END
}

/* End of file helper.php */
/* Location: ./system/expressionengine/third_party/eehive_flickr/helper.php */