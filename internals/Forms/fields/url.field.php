<?php

class field_url extends fieldType_text
{
    const URL_PATTERN = '/^http(s)?:\/\/((\d+\.\d+\.\d+\.\d+)|(([\w-]+\.)+([a-z,A-Z][\w-]*)))(:[1-9][0-9]*)?(\/([\w-.\/:%+@&=]+[\w- .\/?:%+@&=]*)?)?(#(.*))?$/';
    
    public function __construct($name = 'url')
    {
        parent::__construct($name);
        
        $this->setRegExPatterns(self::URL_PATTERN, self::URL_PATTERN);
    }
}