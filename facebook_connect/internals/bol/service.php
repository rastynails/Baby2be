<?php

class FBC_Service extends FBC_ServiceBase
{
    private static $classInstance;

    /**
     * Returns class instance
     *
     * @return FBC_Service
     */
    public static function getInstance()
    {
        if ( null === self::$classInstance )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    public function getScope()
    {
        return '';
    }

    public function fbRequireUser($requiredPermissions = array())
    {
        $user = $this->getFaceBook()->getUser();
        if ( empty($user) )
        {
            $loginUrl = $this->getFaceBook()->getLoginUrl(array(
                'scope' => 'email,user_about_me,user_birthday'
            ));

             SK_HttpRequest::redirect($loginUrl);
        }

        return $user;
    }

    public function fbGetFieldValueList($fbUserId, array $fields)
    {
        if (!$fbUserId)
        {
            throw new InvalidArgumentException('Invalid Argument $fbUserId');
        }

        if (empty($fields))
        {
            return array();
        }

        $fieldStr = implode(', ', $fields);

        $query = "SELECT $fieldStr FROM user WHERE uid=$fbUserId";
        $param  =   array(
            'method'    => 'fql.query',
            'query'     => $query,
            'callback'  => ''
        );

        $out = $this->getFaceBook()->api($param);

        return $out[0];
    }

    public function requestQuestionValueList($fbUserId, $questionNameList = null)
    {
        $fieldDtoList = empty($questionNameList)
            ? $this->fieldDao->findAll()
            : $this->fieldDao->findListByQuestionList($questionNameList);

        $converterList = array();

        $fbFields = array();
        foreach ($fieldDtoList as $fieldDto)
        {
            /* @var $fieldDto FBC_Field */
            $fbFields[$fieldDto->fbField] = $fieldDto->fbField;
        }
        $fbFields = array_values($fbFields);

        $fbFieldValues = $this->fbGetFieldValueList($fbUserId, $fbFields);

        $out = array();
        foreach ($fieldDtoList as $fieldDto)
        {
            /* @var $fieldDto FBC_Field */
            $class = $fieldDto->converter;
            if (empty($converterList[$class]))
            {
                $converter = new $class();
            }
            $out[$fieldDto->question] = $converter->convert($fieldDto->question, $fieldDto->fbField, $fbFieldValues[$fieldDto->fbField]);
        }

        return $out;
    }

    public function loginCompleteRedirect($profileId, $backUrl)
    {

    }

    public function removeInviteRequests( $requestList )
    {
        foreach ( $requestList as $id )
        {
            try
            {
                $this->getFaceBook()->api('/' . $id, 'DELETE');
            }
            catch ( Exception $e )
            {}
        }
    }
}
