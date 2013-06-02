<?php
require_once FBC_DIR_BOL . 'service_base.php';

class FBC_AdminService extends FBC_ServiceBase
{
    private static $classInstance;
    
    /**
     * Returns class instance
     *
     * @return FBC_AdminService
     */
    public static function getInstance()
    {
        if ( null === self::$classInstance )
        {
            self::$classInstance = new self();
        }
        
        return self::$classInstance;
    }
    
    public function registerApplication()
    {
        if (!$this->configureApplication())
        {
            return false;
        }
        
        $appInfo = $this->getAppPropertyList(array('app_id'));
        
        if (empty($appInfo['app_id']))
        {
            return false;
        }
        
        OW::getConfig()->saveConfig('fbconnect', 'app_id', $appInfo['app_id']);
        
        return true;
    }
    
    public function configureApplication()
    {
        $logoUrl = OW::getPluginManager()->getPlugin('fbconnect')->getStaticUrl() . 'img/logo.png';
        
        $props = array(
            'icon_url' => $logoUrl,
            'logo_url' => $logoUrl,
            'connect_url'  => OW_URL_HOME
        );
        
        try
        {
            $this->setAppPropertyList($props);    
        }
        catch (FacebookRestClientException $e) 
        {
            return false;
        }
        
        return true;
    }
    
    public function setAppPropertyList(array $properties)
    {
        $this->getFaceBook()->api_client->admin_setAppProperties($properties);
    }
    
    public function getAppPropertyList(array $properties)
    {
        $props = $this->getFaceBook()->api_client->admin_getAppProperties($properties);
        
        return $props;
    }
    
    public function getPublicAppInfo()
    {
        return $this->getFaceBook()->api_client->application_getPublicInfo();
    }
    
    public function getPossibleFbFieldList($questionName = null)
    {
        switch ($questionName)
        {
            case 'username':
                return array('name');
            case 'email':
                return array('email');
            case 'birthdate':
                return array('birthday_date');
        }
        
        return array('first_name', 'last_name', 'name', 'interests', 'music',
                     'tv', 'movies', 'books', 'quotes', 'about_me', 'website');        
    }
    
    public function assignQuestion($question, $fbField, $converter = 'FBC_FC_TextFieldConverter')
    {
        $fieldDto = $this->fieldDao->findByQuestion($question);
        if ($fieldDto === null)
        {
            $fieldDto = new FBC_Field();
        }
        
        $fieldDto->question = $question;
        $fieldDto->fbField = $fbField;
        $fieldDto->converter = $converter;
        
        $this->fieldDao->save($fieldDto);
    }
    
    public function removeAssignByQuestion($question)
    {
        $this->fieldDao->deleteByQuestion($question);
    }
    
    public function findAliasDtoList()
    {
        return $this->fieldDao->findAll();
    }
    
    public function findAliasList()
    {
        $out = array();
        $aliases = $this->findAliasDtoList();
        foreach($aliases as $alias)
        {
            $out[$alias->question] = $alias->fbField;     
        }
        
        return $out;
    }
}