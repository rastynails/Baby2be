<?php

abstract class fieldType_file extends SK_FormField
{
	private $owner_form_name;
	
	protected $default_allowed_extensions = array(
		'avi','mpeg','wmv','flv','mov','mp4',
		'jpg', 'jpeg', 'gif', 'png','mp3'
	);
	
	protected	$allowed_extensions = array(),
				$allowed_mime_types = array(),
				$max_file_size = 0;
	
	protected	$multifile = false,
				$max_files_num = 0;
	
	/**
	 * Constructor.
	 *
	 * @param string $name
	 */
	public function __construct( $name = 'file' ) {
		parent::__construct($name);
	}
	
	
	public function setup( SK_Form $form )
	{
		$this->owner_form_name = $form->getName();
		
		$this->js_presentation = array(
			'construct' =>
				'function($input, form_handler)
				{
					this.$input = $input;
					this.form_handler = form_handler;
					this.$iframe = $input.next();
					
					this.upload_error = false;
					
					'.($this->multifile
						? "if (this.free_file_slots === undefined) this.free_file_slots = $this->max_files_num;\n"
						: ''
					).'
					
                    this.onConstruct($input, form_handler);
                    					
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
					
			'onConstruct' =>
                'function( $input, form_handler ) {}',
				
			'displayLoading' => 
				'function( value ) {
					this.$input.before(\'<img src="'.URL_LAYOUT.'img/loading.gif" />\');
				}',
			
			'focus' =>
				'function() {
					this.$iframe.get(0).contentWindow.$userfile.focus();
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
				    if ( !$form )
				    {
				        return;
				    }
				
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
							    handler.form_handler.clearErrors("'.$this->getName().'");
								handler.form_handler.error(form_window.sk_upload_error, "'.$this->getName().'");
								handler.$iframe.show();
								handler.construct(handler.$input, handler.form_handler);
								handler.upload_error = true;
							}
							else {
								var $input_clone;
								
								$("#" + handler.form_handler.auto_id + "-' . $this->getName() . '-container").hide();
								
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
	
	
	public static function __set_state( array $params )
	{
		$owner_form_name =& $params['owner_form_name'];
		unset($params['owner_form_name']);
		
		$_this = parent::__set_state($params);
		
		$_this->owner_form_name = $owner_form_name;
		
		return $_this;
	}
	
	
	public function validate( $value )
	{
		if ( !$this->multifile ) {
			return (string)$value;
		}
		else {
			$value = (array)$value;
			if ( count($value) > $this->max_files_num ) {
				$value = array_slice($value, 0, $this->max_files_num);
			}
			return $value;
		}
	}
	
	
	public function validateUserFile( SK_TemporaryFile $tmp_file )
	{
		//Check file extension
        if (!count($this->allowed_extensions))
        {
            $this->allowed_extensions = $this->default_allowed_extensions;
        }

        if ( $this->allowed_extensions && !in_array($tmp_file->getExtension(), $this->allowed_extensions) ) {
            throw new SK_UserFileValidationException(
                SK_Language::text('%forms._errors.unallowed_file_extension').' "'.$tmp_file->getExtension().'"',
                SK_UserFileValidationException::UNALLOWABLE_EXTENSION
            );
        }

		if ( $this->allowed_mime_types && !in_array($tmp_file->getType(), $this->allowed_mime_types) ) {
			throw new SK_UserFileValidationException(
				SK_Language::text('%forms._errors.unallowed_file_mimetype').' "'.$tmp_file->getType().'"',
				SK_UserFileValidationException::UNALLOWABLE_MIME_TYPE
			);
		}
		
		if ( $tmp_file->getSize() > $this->max_file_size ) {
			throw new SK_UserFileValidationException(
				SK_Language::text('%forms._errors.max_filesize_exceeded'),
				SK_UserFileValidationException::MAX_FILE_SIZE_EXCEEDED
			);
		}
	}
	
	/**
	 * Get the field maximum file size.
	 *
	 * @return integer
	 */
	public function getMaxFileSize() {
		return $this->max_file_size;
	}
	
	
	abstract public function preview( SK_TemporaryFile $tmp_file );
	
	
	public function render( array $params = null, SK_Form $form = null )
	{
		$iframe_src = SITE_URL.'file_uploader.php'.
			'?SK-Field-Owner-Form='.$this->owner_form_name.
			'&SK-Field-Name='.$this->getName();
		
		$input_name = $this->multifile ? $this->getName().'[]' : $this->getName();
		
		$output = '<input type="hidden" name="'.$input_name.'" />'.
			'<iframe src="'.$iframe_src.'" width="200" height="22" frameborder="0" scrolling="no" allowtransparency="true"></iframe><div class="preview_cont"></div>';
		
		return $output;
	}
	
}


class SK_UserFileValidationException extends Exception
{
	const	UNALLOWABLE_EXTENSION	= 1,
			UNALLOWABLE_MIME_TYPE	= 2,
			MAX_FILE_SIZE_EXCEEDED	= 3,
			MAX_RESOLUTION_EXCEEDED = 4;
}
