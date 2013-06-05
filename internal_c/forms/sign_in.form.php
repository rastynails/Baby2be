<?php
$form = form_SignIn::__set_state(array(
   'class' => 'form_SignIn',
   'name' => 'sign_in',
   'fields' => 
  array (
    'login' => 
    fieldType_text::__set_state(array(
       'maxlength' => 255,
       'minlength' => 0,
       'regex' => NULL,
       'size' => NULL,
       'name' => 'login',
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
    'password' => 
    fieldType_password::__set_state(array(
       'maxlength' => 255,
       'minlength' => 0,
       'regex' => NULL,
       'size' => NULL,
       'name' => 'password',
       'class' => 'fieldType_password',
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
    'remember_me' => 
    fieldType_checkbox::__set_state(array(
       'name' => 'remember_me',
       'class' => 'fieldType_checkbox',
       'value' => NULL,
       'js_presentation' => 
      array (
        'construct' => 'function($input, form_handler) {}',
        'validate' => 'function(value, required) {}',
        'focus' => 'function() {}',
      ),
    )),
  ),
   'hidden_fields' => 
  array (
  ),
   'actions' => 
  array (
    'process' => 
    form_SignIn_Process::__set_state(array(
       'name' => 'process',
       'class' => 'form_SignIn_Process',
       'fields' => 
      array (
        'login' => true,
        'password' => true,
        'remember_me' => false,
      ),
       'process_fields' => 
      array (
        0 => 'login',
        1 => 'password',
        2 => 'remember_me',
      ),
       'required_fields' => 
      array (
        0 => 'login',
        1 => 'password',
      ),
       'confirm_msg_lang_addr' => NULL,
    )),
  ),
   'default_action' => '',
   'frontend_data' => 
  array (
    'js_class' => 'form_SignIn',
    'js_file' => 'http://www.baby2be.dk/external_c/gh/%25%25C2/C21/C21D8BEB%25%25form_SignIn.js',
  ),
   'frontend_handler' => NULL,
));
?>