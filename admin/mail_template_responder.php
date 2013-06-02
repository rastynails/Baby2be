<?php

require_once '../internals/Header.inc.php';

// Admin authentication
require_once DIR_ADMIN_INC.'fnc.auth.php';

//require_once( DIR_ADMIN_INC.'fnc.mass_mailing.php' );
require_once( DIR_ADMIN_INC.'fnc.mail_templates.php' );

class rsp_Mail_template
{
	private static $response = array();

	public static function construct()
	{
		$apply_function = (string)$_POST["function_"];
		if (!strlen($apply_function)) {
			return;
		}
		if ( !isAdminAuthed( false ) ) {
			self::exec("alert('No changes made. Demo mode.')");
		} else {
			$result = call_user_func(array(__CLASS__, $apply_function), $_POST);

			self::$response["result"] = $result;
		}

		echo json_encode(self::$response);
	}

    function loadTemplateList( array $params )
    {
        $list = getMailTemplateList();
        return array('template_list' => $list);
    }

    function loadTemplate( array $params )
    {
        $template = getTemplate( $params['template_id'] );
        return array('template' => $template);
    }

    function editTemplate( array $params )
    {
        $template_id = intval(trim($params['template_id']));
        $subject = trim($params['subject']);
        $text = trim($params['text']);
        //return $params;
        return updateTemplate( $template_id, $subject, $text );
    }

    function deleteTemplate( array $params )
    {
        return deleteTemplate( $params['template_id'] );
    }
}

rsp_Mail_template::construct();
