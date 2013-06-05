<?php
$form = form_UserStatus::__set_state(array(
   'class' => 'form_UserStatus',
   'name' => 'user_status',
   'fields' => 
  array (
    'status' => 
    fieldType_text::__set_state(array(
       'maxlength' => 40,
       'minlength' => 0,
       'regex' => NULL,
       'size' => NULL,
       'name' => 'status',
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
    'update' => 
    form_UserStatus_update::__set_state(array(
       'name' => 'update',
       'class' => 'form_UserStatus_update',
       'fields' => 
      array (
        'status' => false,
      ),
       'process_fields' => 
      array (
        0 => 'status',
      ),
       'required_fields' => 
      array (
      ),
       'confirm_msg_lang_addr' => NULL,
    )),
  ),
   'default_action' => '',
   'frontend_data' => 
  array (
    'js_class' => 'form_UserStatus',
    'js_file' => 'http://www.baby2be.dk/external_c/gh/%25%2552/52C/52CC38AA%25%25form_UserStatus.js',
  ),
   'frontend_handler' => NULL,
));
?>