<?php

class fieldType_text extends SK_FormField
{
	/**
	 * The maximum length of value.
	 *
	 * @var integer
	 */
	public $maxlength = 255;
	
	/**
	 * The minimum length of value.
	 *
	 * @var integer
	 */
	public $minlength = 0;
	
	/**
	 * PHP and Javascript regular expression patterns for a value.
	 *
	 * @var array list($php_pattern, $js_pattern)
	 */
	private $regex;
	
	/**
	 * Size of input tag.
	 *
	 * @var integer
	 */
	protected $size;
	
	/**
	 * Constructor.
	 *
	 * @param string $name
	 */
	public function __construct( $name ) {
		parent::__construct($name);
	}
	
	public static function __set_state( array $params )
	{
		$regex =& $params['regex'];
		unset($params['regex']);
		
		$_this = parent::__set_state($params);
		
		$_this->regex =& $regex;
		
		return $_this;
	}
	
	/**
	 * Set a both PHP and Javascript regular expression patterns for
	 * checking a field value on both client and server sides.
	 *
	 * @param string $php_pattern
	 * @param string $js_pattern
	 */
	public function setRegExPatterns( $php_pattern, $js_pattern=null ) {
		$this->regex = array($php_pattern, $js_pattern);
	}
	
	/**
	 * @return integer
	 */
	public function getSize()
	{
		return $this->size;
	}
	
	/**
	 * @param integer $size
	 */
	public function setSize( $size )
	{
		$this->size = $size;
	}

	
	/**
	 * Validate a text field.
	 *
	 * @param string $value
	 */
	public function validate( $value )
	{
		$strlen = strlen($value);
		
		if ( $this->minlength && $strlen < $this->minlength ) {
			throw new SK_FormFieldValidationException('fail_in_minlength');
		}

		if ( $this->maxlength && $strlen > $this->maxlength ) {
			throw new SK_FormFieldValidationException('maxlength_exceeded');
		}

		if ( !empty($this->regex) && !preg_match($this->regex[0], $value) ) {
			throw new SK_FormFieldValidationException('regex_match_fail');
		}
		
		return $value;
	}
	
	public function setup( SK_Form $form )
	{
	    $this->js_presentation['validate'] = 'function(value, required, autoId) {
	       if ( required && $("#" + autoId).hasClass("input_invitation") )
	       {
	           throw new Exception();
	       }
	    }';
	}
	
	/**
	 * Render a text input.
	 *
	 * @param string $name
	 * @param string $class an html class name
	 * @return string html
	 */
	public function render( array $params = null, SK_Form $form = null )
	{
		$id = isset($form) ? 'id="' . $form->getTagAutoId($this->getName()) . '"' : '';
		$output = '<input ' . $id . ' name="'.$this->getName().'" type="text"';
		
		if (isset($params['tabindex']) && $params['tabindex'] = intval($params['tabindex'])) {
			$output .= ' tabindex="'.$params['tabindex'].'"';
		}
		
		if ( isset($params['class']) ) {
			$output .= ' class="'.$params['class'].'"';
		}
		
	    if ( isset($params['autocomplete']) ) {
            $output .= ' autocomplete="'.$params['autocomplete'].'"';
        }
		
		if ( $size = $this->getSize() ) {
			$output .= ' size="'.$size.'"';
		}
		
		if ( $this->maxlength > 0 ) {
			$output .= ' maxlength="'.$this->maxlength.'"';
		}
		
		$value = $this->getValue();
		
		if ( $value ) {
			$output .= ' value="'.SK_Language::htmlspecialchars($value).'"';
		}
		
		$output .=  ' />';
		
		if ( !empty($params['hasInvitation']) )
		{
		    $labelEmbed = 'SK_Language.text(' . json_encode('$forms.' . $form->getName() . '.fields.' . $this->getName()) . ')';
		    $output .= '<script language="javascript">SK_SetFieldInvitation(' . json_encode($form->getTagAutoId($this->getName())) . ', ' . $labelEmbed . ' );</script>';
		}
		
		return $output;
	}
	
}
