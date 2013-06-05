<?php
$form = form_ForgotPassword::__set_state(array(
   'class' => 'form_ForgotPassword',
   'name' => 'forgot_password',
   'fields' => 
  array (
    'email' => 
    fieldType_text::__set_state(array(
       'maxlength' => 255,
       'minlength' => 0,
       'regex' => NULL,
       'size' => NULL,
       'name' => 'email',
       'class' => 'fieldType_text',
       'value' => NULL,
       'js_presentation' => 
      array (
        'construct' => 'function($input, form_handler) {}',
        'validate' => 'function(value, required, autoId) {
	       if ( required && $("#" + autoId).hasClass("input_invitation") )
	       {
	           throw new Exception();
	       }
	    }',
        'focus' => 'function() {}',
      ),
    )),
  ),
   'hidden_fields' => 
  array (
  ),
   'actions' => 
  array (
    'send' => 
    form_ForgotPassword_Process::__set_state(array(
       'name' => 'send',
       'class' => 'form_ForgotPassword_Process',
       'fields' => 
      array (
        'email' => true,
      ),
       'process_fields' => 
      array (
        0 => 'email',
      ),
       'required_fields' => 
      array (
        0 => 'email',
      ),
       'confirm_msg_lang_addr' => NULL,
    )),
  ),
   'default_action' => '',
   'frontend_data' => 
  array (
    'js_class' => 'form_ForgotPassword',
    'js_file' => 'http://www.baby2be.dk/external_c/gh/%25%2562/622/622D9B8F%25%25form_ForgotPassword.js',
  ),
   'frontend_handler' => NULL,
));
?>