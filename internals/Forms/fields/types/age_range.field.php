<?php

class fieldType_age_range extends SK_FormField
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
		if (!isset($this->full_range)) {
			$this->full_range= array(18,100);
		}
		parent::setup($form);
	}
	
	public function setRange($range){
		if (is_array($range)) {
			$this->full_range = $range;			
		}
		else {
			$range = explode('-',$range);
			$this->full_range = $range;
			
		}
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
	
	//----------< backward compatibility >------
		
		if(!is_array($value)){
			$value = explode('-',$value);
		}
		
	//----------</ backward compatibility >------
	
	
		list($min, $max) = $this->full_range;
		
		$from = (int)@$value[0];
		$to = (int)@$value[1];
			
		if ( $from < $min || $from > $max
			|| $to < $min || $to > $max ) {
			throw new SK_FormFieldValidationException('full_range_overflow');
		}
		
		return array($from, $to);
	}
	
	public $order;
	public function order($order = "desc") {
		$this->order = in_array($order, array("desc", "asc")) ? $order : "desc";
	}
	
	
	public function render( array $params = null, SK_Form $form = null )
	{
		list($min, $max) = $this->full_range;
		list($value_from, $value_to) = $this->getValue();
		
		$class_decl = isset($params['class']) ? ' class="'.$params['class'].'"' : '';
		
		$output = '<label>'.SK_Language::text('%forms._fields.age_range.from').
			' <select name="'.$this->getName().'[0]"'.$class_decl.'>';
		
		for ( $a = $min; $a <= $max; $a++ ) {
			$selected = ($a == $value_from) ? 'selected="selected"' : '';
			$output .= '<option value="'.$a.'" '.$selected.' >'.$a.'</option>';
		}
		
		$output .= "</select></label>\n" .
			'<label>'.SK_Language::text('%forms._fields.age_range.to').
				' <select name="'.$this->getName().'[1]"'.$class_decl.'>';

		
		if ($this->order == "desc") {
			for ( $a = $max; $a >= $min; $a-- ) {
				$selected = ($a == $value_to) ? 'selected="selected"' : '';
				$output .= '<option value="'.$a.'" '.$selected.' >'.$a.'</option>';
			}
		} else {
			for ( $a = $min; $a <= $max; $a++ ) {
				$selected = ($a == $value_to) ? 'selected="selected"' : '';
				$output .= '<option value="'.$a.'" '.$selected.' >'.$a.'</option>';
			}
		}
		
		
		
		$output .= '</select></label> ';
		$output .= SK_Language::text('%forms._fields.age_range.years_old');
		return $output;
	}
	
	
	public function getValue()
	{
		$value = parent::getValue();
		
		return isset($value) ? $value : $this->full_range;
	}
	
}
