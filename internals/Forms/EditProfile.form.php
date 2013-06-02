<?php

class form_EditProfile extends form_FieldForm
{
    public function __construct()
    {
        parent::__construct('profile_edit');
    }

    public function fields()
    {
        return app_FieldForm::formFields(app_FieldForm::FORM_EDIT);
    }

    public function addField( $field_name )    {
        if ( $field_name == 'email' )        {
            $field = new field_join_email();
            parent::registerField($field);
        }
        parent::addField($field_name);
    }

    protected function actionsPrepare()
    {
        $steps = app_FieldForm::formSteps(app_FieldForm::FORM_EDIT);

        $sexes = SK_ProfileFields::get("sex")->values;

        foreach ( $sexes as $sex )        {
            foreach ( $steps as $step )            {
                $this->addAction($step . "___" . $sex, "formAction_EditProfile");
            }
        }
    }
}




class formAction_EditProfile extends formAction_FieldForm
{

    public function process_fields( $uniqid )
    {
        list($step, $sex) = explode('___', $uniqid);
        $all_fields = app_FieldForm::formStepFields(app_FieldForm::FORM_EDIT, $sex);

        return $all_fields[$step];
    }
    /*
      protected function no_requared_fields() {
      return self::no_process_fields();
      }

      protected static function no_process_fields() {
      $reliant_field_value = app_Profile::getFieldValues(SK_HttpUser::profile_id());
      $no_process_filds = app_FieldForm::hidedDependedFields("sex", $reliant_field_value);
      return $no_process_filds ? $no_process_filds : array();
      }

      public static function __set_state(array $params) {

      $no_process_fields = self::no_process_fields();

      foreach ($params["process_fields"] as $key => $field) {
      if (in_array($field, $no_process_fields)) {
      unset($params["process_fields"][$key]);
      $params["fields"][$field] = false;
      } else {
      try {
      if (SK_ProfileFields::get($field)->required_field && !in_array($field, $params["required_fields"])) {
      $params["required_fields"][] = $field;
      $params["fields"][$field] = true;
      }
      }catch (Exception $e){}
      }
      }

      foreach ($params["required_fields"] as $key => $field) {
      if (in_array($field, $no_process_fields)) {
      unset($params["required_fields"][$key]);
      }
      }

      return parent::__set_state($params);
      }

      public function setup(SK_Form $form) {
      parent::setup($form);

      $no_process_fields = self::no_process_fields();
      foreach ($this->required_fields as $key => $field) {
      if (in_array($field, $no_process_fields)) {
      unset($this->required_fields[$key]);
      }
      }

      }
     */
    public function checkData( array $data, SK_FormResponse $response, SK_Form $form )
    {

        if ( isset($data['username']) && $data['username'] != app_Profile::getFieldValues(SK_HttpUser::profile_id(), 'username') )
        {
            if ( app_Username::isUsernameInRestrictedList($data['username']) )            {
                $error_msg = SK_Language::text('%forms._fields.username.errors.is_restricted');
                $response->addError($error_msg, 'username');
            }            else            {

                $query = SK_MySQL::placeholder("SELECT `profile_id` FROM `" . TBL_PROFILE . "` WHERE `username`='?'", $data['username']);
                if ( SK_MySQL::query($query)->fetch_cell() )                {
                    $error_msg = SK_Language::text('%forms._fields.username.errors.already_exists');
                    $response->addError($error_msg, 'username');
                }
            }
        }

        if ( isset($data['email']) && $data['email'] != app_Profile::getFieldValues(SK_HttpUser::profile_id(), 'email') )
        {
            $query = SK_MySQL::placeholder("SELECT COUNT(`profile_id`) FROM `" . TBL_PROFILE . "` WHERE `email`='?'", $data['email']);
            if ( SK_MySQL::query($query)->fetch_cell() )            {
                $error_msg = SK_Language::text('%forms._fields.email.errors.already_exists');
                $response->addError($error_msg, 'email');
            }
        }

        parent::checkData($data, $response, $form);
    }


    public function process( array $post_data, SK_FormResponse $response, SK_Form $form )
    {

        if ( !$response->hasErrors() )        {

            $fields = array();

            $query = 'SELECT `field`.`name`,`field`.`presentation`, `field`.`base_field` FROM `' . TBL_PROF_FIELD_PAGE_LINK . '` AS `page`
						LEFT JOIN `' . TBL_PROF_FIELD . '` AS `field` USING(`profile_field_id`)
						WHERE `page`.`profile_field_page_type`="edit"';
            $result = SK_MySQL::query($query);

            while ( $field = $result->fetch_object() )
            {
                if ( !array_key_exists($field->name, $post_data) )
                {
                    continue;
                }

                switch ( $field->presentation )
                {
                    case 'location':
                        $location = &$post_data[$field->name];

                        $location_fields = array('country_id', 'state_id', 'city_id', 'zip', 'custom_location');
                        foreach ( $location_fields as $item )
                        {
                            $fields[$item] = isset($location[$item]) ? $location[$item] : null;
                        }
                        break;
                    case 'age_range':
                        $fields[$field->name] = isset($post_data[$field->name][0]) && isset($post_data[$field->name][1]) ? $post_data[$field->name][0] . '-' . $post_data[$field->name][1] : null;
                        break;

                    case 'birthdate':
                    case 'date':
                        $fields[$field->name] = (strlen($post_data[$field->name]['year']) ? $post_data[$field->name]['year'] : '0000') . '-' .
                            (strlen($post_data[$field->name]['month']) ? (strlen($post_data[$field->name]['month'])==1 ? '0'.$post_data[$field->name]['month'] : $post_data[$field->name]['month']) : '00') . '-' .
                            (strlen($post_data[$field->name]['day']) ? (strlen($post_data[$field->name]['day'])==1 ? '0'.$post_data[$field->name]['day'] : $post_data[$field->name]['day']) : '00');
                        break;

                    case 'multiselect':
                    case 'multicheckbox':

                        if ( count($post_data[$field->name]) )
                        {
                            $fields[$field->name] = array_sum($post_data[$field->name]);
                        }
                        else
                        {
                            $fields[$field->name] = 0;
                        }

                        //$response->debug($field->name.'='.$fields[$field->name]);
                        break;

                    default:
                        $fields[$field->name] = @$post_data[$field->name];

                }
            }

            $profile_id = SK_HttpUser::profile_id();

            $base = array();

            $extend = array();
            foreach ( $fields as $name => $value )
            {
                try
                {
                    $pr_field = SK_ProfileFields::get($name);
                }
                catch ( Exception $e )
                {
                    continue;
                }

                if ( $pr_field->base_field )
                {
                    $base[] = "`$name`=" . (isset($value) ? SK_MySQL::placeholder("'?'", $value) : "DEFAULT(`$name`)");
                }
                else
                {
                    $extend[] = "`$name`=" . (isset($value) ? SK_MySQL::placeholder("'?'", $value) : "DEFAULT(`$name`)");
                }

                if (isset($value))
                {
                    if (app_ProfileField::profileFieldChanged($profile_id, $pr_field, $value))
                    {
                        app_ProfileField::markProfileFieldForReview($profile_id, $pr_field->profile_field_id);
                    }
                }
            }

            $last_email = app_Profile::getFieldValues($profile_id, 'email', false);

            $records_affected = 0;
            if ( count($base) )            {
                $query = "UPDATE `" . TBL_PROFILE . "` SET " . implode(',', $base) . " WHERE `profile_id`=" . $profile_id;
                SK_MySQL::query($query);
            }
            $records_affected += SK_MySQL::affected_rows();

            if ( count($extend) )            {
                $query = "UPDATE `" . TBL_PROFILE_EXTEND . "` SET " . implode(',', $extend) . " WHERE `profile_id`=" . $profile_id;
                SK_MySQL::query($query);
            }
            $records_affected += SK_MySQL::affected_rows();

            if ( $records_affected > 0 )            {
                $new_email = app_Profile::getFieldValues($profile_id, 'email', false);

                if ( $new_email != $last_email )                {
                    app_Profile::setFieldValue($profile_id, 'email_verified', 'undefined');
                    app_EmailVerification::addRequestEmailVerification($profile_id, $new_email);
                }

                if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
                {
                    $newsfeedAction = app_Newsfeed::newInstance()->getAction('profile_edit', $profile_id, $profile_id);
                    if ( !empty($newsfeedAction) )
                    {
                        app_Newsfeed::newInstance()->removeActionById($newsfeedAction->getId());
                    }

                    $newsfeedDataParams = array(
                        'params' => array(
                            'feature' => FEATURE_NEWSFEED,
                            'entityType' => 'profile_edit',
                            'entityId' => $profile_id,
                            'userId' => $profile_id,
                            'status' => 'approval',
                            'replace' => true
                        )
                    );
                    app_Newsfeed::newInstance()->action($newsfeedDataParams);
                }

                if (SK_Config::section('site')->Section('additional')->Section('profile')->profile_review_enabled)
                {
                    app_Profile::setFieldValue(SK_HttpUser::profile_id(), 'reviewed', "n");
                }
                $response->addMessage(SK_Language::text('%forms.edit_profile.msg.saved'));
            }
            else            {
                $response->addMessage(SK_Language::text('%forms.edit_profile.msg.not_saved'));
            }

            //$response->exec('window.location.reload()');
        }

    }

}
