<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov 17, 2008
 * 
 */

class fieldType_time extends SK_FormField
{
	protected $invite_messages = array();
	
	/**
	 * @var boolean
	 */
	protected $day_part;
	
	/**
	 * Class Constructor
	 *
	 * @param string $name
	 */
	public function __construct( $name = 'time' ) 
	{
		parent::__construct( $name );
	}
	
	
	public function setInviteMsg($item, $msg)
	{
		$this->invite_messages[$item] = $msg;
	}
	
	
	/**
	 *
	 * @param array $value
	 * @return array
	 */
	public function validate( $value )
	{
        $this->day_part = !SK_Config::section('site.official')->get('military_time');

		if( is_array( $value ) )
		{
			if( isset($value['minute']) && !isset($value['hour'] ) )
			{
				throw new SK_FormFieldValidationException('hour');
			}
			
			if( isset($value['hour'] ) && !isset($value['minute'] ) )
			{
				throw new SK_FormFieldValidationException('minute');
			}
		}
		else 
		{
			$intval = (int)$value;
			
			$value = ( $this->day_part ) ?
				array( 'hour' => date('g', $intval), 'minute' => date('i', $intval), 'day_part' => date('a', $intval) ) :
				array( 'hour' => date('G', $intval), 'minute' => date('i', $intval) );
		}
		
		return array( 'hour' => ( isset($value['hour']) ? $value['hour'] : false ), 
			'minute' => (isset( $value['minute'] ) ? $value['minute'] : false ),
			'day_part' => (isset( $value['day_part'] ) ? $value['day_part'] : false ) );
	}
	
	
	public function render( array $params = null, SK_Form $form = null )
	{
        $this->day_part = !SK_Config::section('site.official')->get('military_time');

		$input_devider = '&nbsp;';
		
		$value = $this->getValue();
		
		$invite_prefix = isset($params['invite_prefix']) ? '%' . trim($params['invite_prefix']) : '%forms._fields.time';
		
		$class_decl = isset($params['class']) ? ' class="'.$params['class'].'"' : '';
		
		$output = '<select name="'. $this->getName().'[hour]"'. $class_decl.'>
			<option value="">'. ( isset( $this->invite_messages['hour'] ) ? $this->invite_messages['hour'] : SK_Language::text( $invite_prefix.'.hour' )). '</option>';
		
		
		$hours = $this->day_part ? 12 : 24;
			 
		for ( $i = 1; $i <= $hours; $i++ )
		{
			$selected = $value['hour'] == $i ? 'selected="selected"' : '';
			$output .= '<option value="'. $i.'" '. $selected.'>'. $i. '</option>';
		}
			
		$output .= '</select>'.$input_devider.'<select name="'. $this->getName().'[minute]"'. $class_decl.'>
			<option value="">'. ( isset( $this->invite_messages['minute'] ) ? $this->invite_messages['minute'] : SK_Language::text( $invite_prefix.'.minute' )). '</option>';
		
		for ( $i = 0; $i<=59; $i++ )
		{
			$selected = ( isset($value['minute']) && $value['minute'] == $i ) ? 'selected="selected"' : '';
			$output .= '<option value="'. $i.'" '. $selected.'>'. ( $i < 10 ? '0'.$i : $i ). '</option>';
		}
		
		$output .= '</select>';
		
		if( $this->day_part )
		{
			$output .= $input_devider.'<select name="'. $this->getName().'[day_part]"'. $class_decl.'>
				<option value="am"'. ( isset( $value['day_part'] ) && $value['day_part'] == 'am' ? ' selected="selected"' : '' ).' >am</option> 
				<option value="pm"'. ( isset( $value['day_part'] ) && $value['day_part'] == 'pm' ? ' selected="selected"' : '' ).'>pm</option></select>';
		}
		
		return $output;
	}
	
	/**
	 * @see SK_FormField::setup()
	 *
	 * @param SK_Form $form
	 */
	public function setup(  SK_Form $form)
	{
		$this->js_presentation['validate'] = "function(value, required){
		if (!required) {
			return;
		}
		
		if( !value.hour || !value.minute )
		{
			throw new SK_FormFieldValidationException();	
		}
		}";
	}

}
