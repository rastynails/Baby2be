<?php

class field_join_photo extends fieldType_file
{
	public function __construct($name)
	{
		parent::__construct($name);
	}
	
	
	public function setup( SK_Form $form )
	{
		$this->allowed_extensions = array('jpg', 'jpeg', 'gif', 'png');
		//$this->allowed_mime_types = array('image/jpg', 'image/jpeg', 'image/gif', 'image/png', 'image/x-png');
		$this->max_file_size = SK_Config::section("photo")->Section("general")->max_filesize * 1024 * 1024;
		
		$this->multifile = false;
			
		parent::setup($form);
	}
	
	public function validateUserFile(SK_TemporaryFile $tmp_file) {
		parent::validateUserFile($tmp_file);
		
		list($width, $height, $type) = getimagesize($tmp_file->getPath());
		$config = SK_Config::section("photo")->Section("general");
		
		if ($width > $config->max_width || $height > $config->max_height) {
			throw new SK_UserFileValidationException(
				SK_Language::text("%forms._errors.max_resolution_exceeded"),
				SK_UserFileValidationException::MAX_RESOLUTION_EXCEEDED
			);
		}
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

