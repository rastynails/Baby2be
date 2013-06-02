<?php

class field_email_list extends fieldType_textarea 
{
	public function validate($value)
	{
		$addr = preg_split('/\s+/', $value);
		
		if (!count($addr)) {
			throw new SK_FormFieldValidationException('empty_email');
		}
		
		
		return $value;
	}
}
