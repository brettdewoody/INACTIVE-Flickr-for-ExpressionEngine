<?php

	echo form_open('C=addons_extensions'.AMP.'M=save_extension_settings'.AMP.'file=eehive_flickr');
	
	if(isset($message_error))
	{
		echo '<p class="notice failure">' . $message_error . '</p>';
	}

	$this->table->set_template($cp_pad_table_template);
	
	$this->table->set_heading(
	    array('data' => lang('preference'), 'style' => 'width:50%;'),
	    lang('setting')
	);

	foreach ($settings as $key => $val)
	{
		$left = (lang('note_' . $key)) ? lang($key, $key) . '<small style="display:block;font-weight:normal;margin-top:0.5em">' . lang($key . '_note') . '</small>' : lang($key, $key);
		$this->table->add_row($left, $val);
	}
	echo $this->table->generate();

	if(isset($callback_url))
	{
		printf(lang('callback_url'), $callback_url);
	}
	
	if(isset($activate_url))
	{
		echo '<p><a href="' . $activate_url . '" title="' . lang('activate_url') . '">' . lang('activate_url') . '</a></p>';
	}
	echo '<p>' . form_submit('submit', lang('save'), 'class="submit"') . '</p>';
	$this->table->clear();
	echo form_close();

/* End of file settings_setup.php */
/* Location: ./system/expressionengine/third_party/eehive_flickr/views/settings_setup.php */