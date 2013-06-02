<?php

class field_item_file extends fieldType_file
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
		$this->max_files_num = 10;
		
		$this->owner_form_name = $form->getName();
		
		$this->js_presentation = array(
			'construct' =>
				'function($input, form_handler)
				{
					this.$input = $input;
					this.form_handler = form_handler;
					this.$iframe = $input.siblings("iframe");
					this.upload_error = false;
					
					'.($this->multifile
						? "if (this.free_file_slots === undefined) this.free_file_slots = $this->max_files_num;\n"
						: ''
					).'
					
					var form_window = this.$iframe.get(0).contentWindow;
					
					var handler = this;
					
					var onload_ping =
						window.setInterval(function() {
							if (form_window.$userfile) {
								window.clearInterval(onload_ping);
								form_window.$userfile.change(function() {
									handler.uploadStart(form_window.$upload_form);
								});
							}
						}, 500);
				}',
			
			'validate' =>
				'function( value ) {}',
				
			'displayLoading' => 
				'function( value ) {
					this.$input.before(\'<img src="'.URL_LAYOUT.'img/loading.gif" />\');
				}',
			
			'focus' =>
				'function() {
                                     this.$input.focus();
				}',
			
			'showLoading' =>
				'function() {
					this.$input.before(\'<img src="'.URL_LAYOUT.'img/loading.gif" />\');
				}',
			
			'hideLoading' =>
				'function() {
					this.$input.prev().remove();
				}',
			
			'uploadStart' =>
				'function($form)
				{
					this.form_handler.file_upload_in_process = true;	
					
					this.$iframe.hide();				
					this.showLoading();

					var form_window = this.$iframe.get(0).contentWindow;
					form_window.$upload_form = null;
					form_window.$userfile = null;
					form_window = null;
					
					$form.submit();
					
					var handler = this;
					
					var ping = window.setInterval(function() {
						var form_window = handler.$iframe.get(0).contentWindow;
						if (form_window && form_window.$userfile)
						{
							window.clearInterval(ping);
							
							handler.hideLoading();
							
							if (form_window.sk_upload_error) {
								handler.form_handler.error(form_window.sk_upload_error, "'.$this->getName().'");
								handler.$iframe.show();
								handler.construct(handler.$input, handler.form_handler);
								handler.upload_error = true;
							}
							else {
								var $input_clone;
								
								if (!handler.$input.val()) {
									handler.$input.val(form_window.sk_userfile_uniqid);
								}
								else {
									$input_clone = handler.$input.before(
										handler.$input.clone()
											.val(form_window.sk_userfile_uniqid)
									);
								}
								
								var $preview = $(form_window.sk_userfile_preview);
								handler.$input.parent().find(".preview_cont").append($preview);
								
								$(".delete_file_btn", $preview)
									.one("click", function()
									{
										if (!$input_clone) {
											handler.$input.removeAttr("value");
										}
										else {
											$input_clone.remove();
										}
										
										if (handler.$iframe.css("display") == "none") {
											handler.$iframe.show();
											handler.construct(handler.$input, handler.form_handler);
										}
										
										if (typeof handler.free_file_slots != "undefined") {
											handler.free_file_slots++;
										}
										
										$preview.remove();
									});
							}
							
							handler.form_handler.file_upload_in_process = false;
							
							handler.uploadComplete();
						}
					}, 500);
				}',
				
				'uploadComplete' => 'function() {}'
		);
		
		if ( $this->multifile ) {
			$this->js_presentation['uploadComplete'] =
				'function() {
					if (--this.free_file_slots) {
						this.$iframe.show();
						this.construct(this.$input, this.form_handler);
					}
				}';
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
