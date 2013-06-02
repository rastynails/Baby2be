<?php

class field_join_username extends fieldType_text
{


	/**
	 * Constructor.
	 *
	 * @param string $name
	 */
	public function __construct( $name = 'username' ) {
		parent::__construct($name);

		$profile_id = SK_HttpUser::profile_id();

		$this->js_presentation['changed'] = "true";

		$this->js_presentation['lastValue'] = $profile_id ? json_encode(app_Profile::getFieldValues($profile_id,'email')) : '""';

		$this->js_presentation['construct'] = '
					function($input) {
						var handler = this;

						if($input.val()!="" && $input.val()!=handler.lastValue){
							handler.check_username($input);
						}

						$input.change(function(){
							handler.changed = true;
						});

						$input.blur(function() {
							if($input.val()!="" && $input.val()!=handler.lastValue && handler.changed ){
								handler.check_username($input);
							}
							else{
								$input.parent().find(".success").remove();
							}
						});
					}';



		$this->js_presentation['check_username'] = '
					function($input){

					var handler = this;
						$.ajax({
								url: "'.URL_FIELD_RESPONDER.'",
								method: "post",
								dataType: "json",
								data: {action: "check_username_exists", username: $input.val()},
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

}
