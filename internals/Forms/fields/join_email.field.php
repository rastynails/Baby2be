<?php

class field_join_email extends fieldType_text
{
	
	
	/**
	 * Constructor.
	 *
	 * @param string $name
	 */
	public function __construct( $name = 'email' ) {
		
		parent::__construct($name);
		
		
		
		$this->js_presentation['lastValue'] = '""';
		
		$this->js_presentation['changed'] = "true";
		
		$this->js_presentation['construct'] = '
					function($input) {
						var handler = this;
						
						this.lastValue = $input.parent().find("input[name=last_value]").val();
						
						if($input.val()!="" && $input.val()!=this.lastValue){
							handler.check_email($input);
						}		
						
						$input.change(function(){
							handler.changed = true;
						});
										
						$input.blur(function() {
							if($input.val()!="" && $input.val()!=handler.lastValue && handler.changed ){
								handler.check_email($input);
							}
							else{
								$input.parent().find(".success").remove();
							}						
						});
					}';
		
		
		
		$this->js_presentation['check_email'] = '
					function($input){
					var handler = this;
					
						$.ajax({
								url: "'.URL_FIELD_RESPONDER.'",
								method: "post",
								dataType: "json",
								data: {action: "check_email_exists", email: $input.val()},
								success: function(result){
										$input.parent().find(".success").remove();
										handler.changed = false;
                                        if(result==1){
   											$input.after(\'<span class="success">&nbsp;</span>\');
   										}
                                        else{
                                            SK_drawError(result);											
   										}
  								}
							});
					}
					';
		
	}
	
	public function render( array $params = null, SK_Form $form = null ) {
		$profile_id = SK_HttpUser::profile_id();
		$last_val = $profile_id ? app_Profile::getFieldValues($profile_id,'email') : '';
		
		$out = '<input type="hidden" name="last_value" value="' . $last_val . '" />';
		return parent::render($params, $form) . $out;
	}
	
}

