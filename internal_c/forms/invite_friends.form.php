<?php
$form = form_InviteFriends::__set_state(array(
   'class' => 'form_InviteFriends',
   'name' => 'invite_friends',
   'fields' => 
  array (
    'email_addr' => 
    field_email_list::__set_state(array(
       'maxlength' => 2000,
       'minlength' => 0,
       'regex' => NULL,
       'size' => NULL,
       'name' => 'email_addr',
       'class' => 'field_email_list',
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
    'message' => 
    fieldType_textarea::__set_state(array(
       'maxlength' => 2000,
       'minlength' => 0,
       'regex' => NULL,
       'size' => NULL,
       'name' => 'message',
       'class' => 'fieldType_textarea',
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
    'process' => 
    form_InviteFriends_Process::__set_state(array(
       'name' => 'process',
       'class' => 'form_InviteFriends_Process',
       'fields' => 
      array (
        'email_addr' => true,
        'message' => false,
      ),
       'process_fields' => 
      array (
        0 => 'email_addr',
        1 => 'message',
      ),
       'required_fields' => 
      array (
        0 => 'email_addr',
      ),
       'confirm_msg_lang_addr' => NULL,
    )),
  ),
   'default_action' => '',
   'frontend_data' => 
  array (
    'js_class' => 'form_InviteFriends',
    'js_file' => 'http://www.matingdating.dk/external_c/gh/%25%252A/2AC/2AC7386F%25%25form_InviteFriends.js',
  ),
   'frontend_handler' => NULL,
));
?>