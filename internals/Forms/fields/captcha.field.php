<?php

class field_captcha extends SK_FormField 
{
	
	/**
	 * Constructor.
	 *
	 * @param string $name
	 */
	public function __construct( $name = 'captcha' ) {
		
		parent::__construct($name);
		
		$this->js_presentation['$input'] = '{}';
		
		$this->js_presentation['request_result'] = '""';
		
		$this->js_presentation['construct'] = 'function($input, form_handler) {
			var handler = this;
			
			
			$input.parents(".captcha_container:eq(0)").find("a").click(function(){
				handler.refresh_image($input); 
			});
			
			form_handler.bind("error", function(){
				handler.refresh_image($input); 
			});
		}';
		
		$this->js_presentation['refresh_image'] = 'function($input){
				$.ajax({
							url: "'.URL_FIELD_RESPONDER.'",
							method: "post",
							dataType: "json",
							data: {action: "change_captcha_image"},
							success: function(result){
							 	if (result){
									$input.parents(".captcha_container:eq(0)").find("img").attr("src","'.URL_CAPTCHA .'image.php?img_id="+result);
									$input.val("");								
								}
							 }
						});
		}';
		
		
		
	}
	
	public function validate($value)
	{
		$result = self::checkNumber($value['value']);
		
		if (!$result) {
			throw new SK_FormFieldValidationException('incorrect_value');
		}
			
		return $value;
	}
	
	public static function checkNumber($code)
	{
        require_once DIR_CAPTCHA . 'securimage.php';

        $img = new securimage();
		return $img->check($code);
	}

    public static function generateNumber()
	{
		return uniqid();
	}
	
	public function render( array $params = null, SK_Form $form = null )
	{
		$output = '<div class="captcha_container" align="center">
						<div>
							<img style="width:150px" src="'.URL_CAPTCHA .'image.php?img_id=' .self::generateNumber().'">
							<input type="text" maxlength="6" name="'.$this->getName().'[value]" style="width:70px">
						</div>
						<a class="refresh" title="Refresh" href="javascript://"></a>
					</div>';
		return $output;
	}

}
