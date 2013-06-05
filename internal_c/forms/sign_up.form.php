<?php
$form = form_SignUp::__set_state(array(
   'avalible_actions' => 
  array (
  ),
   'active_action' => 'action_sign_up',
   'class' => 'form_SignUp',
   'name' => 'sign_up',
   'fields' => 
  array (
    'email' => 
    fieldType_text::__set_state(array(
       'maxlength' => 128,
       'minlength' => 0,
       'regex' => 
      array (
        0 => '/^[a-zA-Z0-9_\\-\\.]+@([a-zA-Z0-9_\\-]+\\.)+?[a-zA-Z0-9_]{2,}(\\.\\w{2})?$/i',
        1 => NULL,
      ),
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
       'profile_field_id' => '1',
    )),
    're_email' => 
    fieldType_text::__set_state(array(
       'maxlength' => 255,
       'minlength' => 0,
       'regex' => NULL,
       'size' => NULL,
       'name' => 're_email',
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
    'username' => 
    fieldType_text::__set_state(array(
       'maxlength' => 32,
       'minlength' => 0,
       'regex' => 
      array (
        0 => '/^\\w+$/',
        1 => NULL,
      ),
       'size' => NULL,
       'name' => 'username',
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
       'profile_field_id' => '2',
    )),
    'password' => 
    fieldType_password::__set_state(array(
       'maxlength' => 40,
       'minlength' => 0,
       'regex' => 
      array (
        0 => '/^(.){4,30}$/i',
        1 => NULL,
      ),
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
       'profile_field_id' => '3',
    )),
    're_password' => 
    fieldType_password::__set_state(array(
       'maxlength' => 255,
       'minlength' => 0,
       'regex' => NULL,
       'size' => NULL,
       'name' => 're_password',
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
    'i_agree_with_tos' => 
    fieldType_checkbox::__set_state(array(
       'name' => 'i_agree_with_tos',
       'class' => 'fieldType_checkbox',
       'value' => NULL,
       'js_presentation' => 
      array (
        'construct' => 'function($input, form_handler) {}',
        'validate' => 'function(value, required) {}',
        'focus' => 'function() {}',
      ),
       'profile_field_id' => '188',
    )),
  ),
   'hidden_fields' => 
  array (
  ),
   'actions' => 
  array (
    'action_sign_up' => 
    formAction_SignUp::__set_state(array(
       'uniqid' => 'sign_up',
       'name' => 'action_sign_up',
       'class' => 'formAction_SignUp',
       'fields' => 
      array (
        'email' => true,
        're_email' => true,
        'username' => true,
        'password' => true,
        're_password' => true,
        'i_agree_with_tos' => true,
      ),
       'process_fields' => 
      array (
        0 => 'email',
        1 => 're_email',
        2 => 'username',
        3 => 'password',
        4 => 're_password',
        5 => 'i_agree_with_tos',
      ),
       'required_fields' => 
      array (
        0 => 'email',
        1 => 're_email',
        2 => 'username',
        3 => 'password',
        4 => 're_password',
        5 => 'i_agree_with_tos',
      ),
       'confirm_msg_lang_addr' => NULL,
    )),
  ),
   'default_action' => '',
   'frontend_data' => 
  array (
    'js_class' => 'form_SignUp',
    'js_file' => 'http://www.baby2be.dk/external_c/gh/%25%25DE/DE6/DE65EBD5%25%25form_SignUp.js',
  ),
   'frontend_handler' => NULL,
));
?>