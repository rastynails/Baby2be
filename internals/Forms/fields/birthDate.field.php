<?php

class field_birthDate extends fieldType_date
{
    public function validate($value)
    {
        if ( empty($value['month']) && empty($value['day']) && empty($value['year']) )
        {
            return array();
        }

        $value = parent::validate($value);

        $now = getdate();
        $minTime = mktime(null, null, null, $now['mon'], $now['mday'], $now['year'] - ($now['year'] - $this->range['min']) );
        $maxTime = mktime(null, null, null, $now['mon'], $now['mday'], $now['year'] - ($now['year'] - $this->range['max']) );
        $birthTime = mktime(0, 0, 0, $value['month'], $value['day'], $value['year']);

        if ( ($birthTime > $maxTime) || ($minTime > $birthTime) )
        {

            throw new SK_FormFieldValidationException('birthdate_overflow');
        }

        return $value;
    }
}