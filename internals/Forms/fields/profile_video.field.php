<?php

class field_profile_video extends fieldType_file 
{
	public function __construct($name)
	{
		parent::__construct($name);
	}
	
	public function setup(SK_Form $form)
	{
		//$this->allowed_mime_types = explode(',', SK_Config::section('video')->Section('other_settings')->get('upload_media_file_mime_types')); 
		$this->allowed_extensions = explode(',', SK_Config::section('video')->Section('other_settings')->get('upload_media_file_extension'));

		$this->max_files_num = 1;
		$this->max_file_size = intval(SK_Config::section('video')->Section('other_settings')->get('upload_media_file_size_limit')) * 1024 * 1024;
		
		parent::setup($form);
		
		$this->js_presentation['showLoading'] = 'function(){
			this.$input.siblings(".preloader").fadeIn("fast");
		}';
		
		$this->js_presentation["uploadComplete"] = 'function() {
			this.$input.siblings(".preloader").fadeOut("fast");
		}';
	}
	
	public function preview(SK_TemporaryFile $tmp_file)
	{
		$video_url = "<span>{$tmp_file->getFileName()}</span>";

		$output = $video_url.'<br /><a class="delete_file_btn" style="display: none" href="#">delete</a>';
		
		return $output;
	}
	
	public function render( array $params = null, SK_Form $form = null )
	{
		$output = parent::render($params);
		
		$output.='<div class="preloader"></div>';
		return $output;
	}
}
