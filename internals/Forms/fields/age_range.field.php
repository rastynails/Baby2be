<?php

class field_age_range extends SK_FormField
{
	/**
	 * Full age range points.
	 *
	 * @var array
	 */
	private $full_range;
	
	/**
	 * Constructor.
	 *
	 * @param string $name
	 */
	public function __construct( $name = 'age_range' ) {
		parent::__construct($name);
	}
	
	
	public function setup( SK_Form $form )
	{
		// TODO: getting this values from configs
		$this->full_range = array(18, 100);
		
		parent::setup($form);
	}
	
	
	public static function __set_state( array $params )
	{
		$full_range = &$params['full_range'];
		unset($params['full_range']);
		
		$_this = parent::__set_state($params);
		
		$_this->full_range = &$full_range;
		
		return $_this;
	}
	
	/**
	 * Validate a field type of age_range.
	 *
	 * @param array $value
	 * @return array list($from, $to)
	 */
	public function validate( $value )
	{
		list($min, $max) = $this->full_range;
		
		$from = (int)@$value[0];
		$to = (int)@$value[1];
		
		if ( $from < $min || $from > $max
			|| $to < $min || $to > $max ) {
			throw new SK_FormFieldValidationException('full_range_overflow');
		}
		
		return array($from, $to);
	}
	
	
	public function render( array $params = null, SK_Form $form = null )
	{
		list($min, $max) = $this->full_range;
		list($value_from, $value_to) = $this->getValue();
		
		$class_decl = isset($params['class']) ? ' class="'.$params['class'].'"' : '';
		
		$output = '<select name="'.$this->getName().'[0]"'.$class_decl.'>';
		
		for ( $a = $min; $a <= $max; $a++ ) {
			$output .= '<option value="'.$a.'">'.$a.'</option>';
		}
		
		$output .= "</select>\n" .
			'<select name="'.$this->getName().'[1]"'.$class_decl.'>';
		
		for ( $a = $max; $a >= $min; $a-- ) {
			$output .= '<option value="'.$a.'">'.$a.'</option>';
		}
		
		$output .= '</select>';
		
		return $output;
	}
	
	
	public function getValue()
	{
		$value = parent::getValue();
		
		return isset($value) ? $value : $this->full_range;
	}
	
}
