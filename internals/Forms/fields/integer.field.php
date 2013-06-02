<?php

class field_integer extends fieldType_text
{
    const INT_PATTERN = '/^[-+]?[0-9]+$/';
    
    public function __construct($name)
    {
        parent::__construct($name);

        $this->setRegExPatterns(self::INT_PATTERN, self::INT_PATTERN);
    }
}