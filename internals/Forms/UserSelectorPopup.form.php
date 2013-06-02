<?php

class form_UserSelectorPopup extends SK_Form
{

    public function __construct()
    {
        parent::__construct('user_selector_popup');
    }

    public function setup()
    {
        $field = new fieldType_textarea('message');
        parent::registerField($field);

        $field = new fieldType_hidden('emails');
        parent::registerField($field);

        parent::registerAction('form_UserSelectorForm_send');
    }
}


class form_UserSelectorForm_send extends SK_FormAction
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct('send');
    }


    public function setup( SK_Form $form )
    {
        parent::setup($form);
    }

    public function checkData(array $data, SK_FormResponse $response, SK_Form $form)
    {
        parent::checkData($data, $response, $form);

        if ( empty($data['emails']) )
        {
            $response->addError(SK_Language::text('components.user_selector_popup.error_no_emails'));
        }
    }


    /**
     * Update user status.
     *
     * @param array $data
     * @param SK_FormResponse $response
     * @param SK_Form $form
     */
    public function process( array $data, SK_FormResponse $response, SK_Form $form )
    {
        $message = $data['message'];
        $emails = explode(',', $data['emails']);

        $emailsSent = 0;

        foreach ( $emails as $email )
        {
            if ( app_Invitation::addRequestRegister($email, $message) )
            {
                $emailsSent++;
            }
        }

        $response->addMessage(SK_Language::text('components.user_selector_popup.success_message', array(
            'count' => $emailsSent
        )));
    }
}

