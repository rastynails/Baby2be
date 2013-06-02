<?php

class field_upload_photo extends fieldType_file
{
	public function __construct($name = "upload_photo")
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
		
		$this->js_presentation['showLoading'] = 'function(){
			$(".preloader").show();
		}';
		
		$this->js_presentation['focus'] = 'function(){
			
		}';
		
		$this->js_presentation['hideLoading'] = 'function(){
			
		}';
				
		$this->js_presentation["uploadComplete"] = 'function() {
			
			var $component_container = $(this.form_handler.ownerComponent.container_node);
			var $preloader = $component_container.find(".preloader");
			if (this.upload_error) {
				$preloader.hide();
				this.upload_error = false;
				return ;
			} 
			
			var field = this;		
		
			var $cloase_btn = this.$input.parent().find(".delete_file_btn");
			
			this.form_handler.bind("success", function(){
				$preloader.hide();
				$cloase_btn.click();
			});
			
			this.form_handler.bind("error", function(){
				$preloader.hide();
				$cloase_btn.click();
			});
			
			this.form_handler.$form.submit();
		}';
	}
	
	
	public function preview( SK_TemporaryFile $tmp_file )
	{
		$output = '<div style="display:none"><a class="delete_file_btn" href="#">[delete]</a></div>';
		
		return $output;
	}
	
	public function validateUserFile(SK_TemporaryFile $tmp_file) {
		parent::validateUserFile($tmp_file);
		
		list($width, $height, $type) = getimagesize($tmp_file->getPath());
		$config = SK_Config::section("photo")->Section("general");
		
		if ($width > $config->max_width || $height > $config->max_height) {
			throw new SK_UserFileValidationException(
				SK_Language::text("%forms._fields.".$this->getName().".errors.max_resolution_exceeded"),
				SK_UserFileValidationException::MAX_RESOLUTION_EXCEEDED
			);
		}
	}
	
	public function render( array $params = null, SK_Form $form = null )
	{
		$output = parent::render($params);
		$height = isset($params["height"]) ? 'height: '.trim($params["height"]) : 'height: 100px';
		
		$output.='<div class="preloader" style="'.$height.';width: 300px; display:none"></div>';
		return $output;
	}
	
}
