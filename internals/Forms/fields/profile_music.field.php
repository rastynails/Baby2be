<?php

class field_profile_music extends fieldType_file
{
	public function __construct($name)
	{
		parent::__construct($name);
	}
	
	public function setup(SK_Form $form)
	{
		$this->allowed_extensions =array('mp3');
           
		$this->max_files_num = 1;
		$this->max_file_size = intval(SK_Config::section('music')->get('upload_music_file_size_limit')) * 1024 * 1024;
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
		$music_url = "<span>{$tmp_file->getFileName()}</span>";
                
                $output = $music_url.'<br /><a class="delete_file_btn" style="display: none" href="#">delete</a>';
		
		return $output;
	}
	
	public function render( array $params = null, SK_Form $form = null )
	{
		$output = parent::render($params);
		
		$output.='<div class="preloader"></div>';
		return $output;
	}
}
