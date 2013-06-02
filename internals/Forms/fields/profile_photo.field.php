<?php

class field_profile_photo extends fieldType_file
{
	public function __construct($name)
	{
		parent::__construct($name);
	}
	
	
	public function setup( SK_Form $form )
	{
		$this->allowed_extensions = array('jpg', 'jpeg', 'gif', 'png');
		//$this->allowed_mime_types = array('image/jpg', 'image/jpeg', 'image/gif', 'image/png', 'image/x-png');
		$this->max_file_size = 2*1024*1024;
		
		$this->multifile = true;
		$this->max_files_num = 5;
		
		parent::setup($form);
	}
	
	
	public function preview( SK_TemporaryFile $tmp_file )
	{
		$img_src = $tmp_file->getURL();
		
		$output = <<<EOT
<div style="float: left; margin: 2px; text-align: center">
	<img src="$img_src" height="100" /><br />
	<a class="delete_file_btn" href="#">[delete]</a>
</div>
EOT;
		return $output;
	}
	
}
