<?php

class field_checkbox_set extends SK_FormField
{
	/**
	 * Possible values list.
	 *
	 * @var array
	 */
	protected $values = array();
	
	protected $labels = array();
	
	protected $column_size;
	
	/**
	 * Constructor.
	 *
	 * @param string $name
	 */
	public function __construct( $name )
	{
		parent::__construct($name);
	}
	
	public function setValues($values = array())
	{
		$this->values = $values;
	}
	
	public function setColumnSize($size)
	{
		$this->column_size = $size;
	}
	
	public function setLabels($labels = array())
	{
		$this->labels = $labels;
	}
	
	public function setup( SK_Form $form )
	{
		if ( !$this->getValue() ) {
			$this->setValue(array());
		}
	
	}
	
	/**
	 * Validator for field type of set.
	 * Note: validation works only if $this->values is sets up
	 * during $this->setup() and compiled with the form state as well.
	 *
	 * @param array $value
	 * @return array
	 */
	public function validate( $value )
	{
	
	//----------< backward compatibility  >------

		if(!is_array($value))
		{
			if(!intval($value))
				return array();
			$val_array = array();
			foreach ($this->values as $field_value)
			{
				
				if((int)$field_value & (int)$value){
					$val_array[] = $field_value;
				}
			}
			$value = $val_array;
						
		}
	
		
	//----------</ backward compatibility >------
		
		$checked_values = (array)$value;
				
		if ( $this->values ) {
			foreach ( $value as $_value ) {
				if ( !in_array($_value, $this->values) ) {
					throw new SK_FormFieldValidationException('illegal_value');
				}
			}
		}
		
		return $value;
	}
	
	/**
	 * Render a field.
	 *
	 * @param string $name	 
	 * @param string $class an html class name
	 * @param integer $size a fixed size for a select
	 * @return string html
	 */
	public function render( array $params = null, SK_Form $form = null )
	{
		$output = '<ul id="' . $form->getTagAutoId($this->getName()) . '" ';
		if ( isset($params['class']) ) {
			$output .= ' class="'.$params['class'].'"';
		}
		$output .= '>';
		
		$selected_values = $this->getValue();
		
		$col_size = isset($params['col_size']) ? $params['col_size'] : $this->column_size;
	
		$style = isset($col_size) ? 'style="width:'.$col_size.'"':'';
		
		foreach ( $this->values as $key=>$value )
		{
			$output .= '<li '.$style.' class="list_label" ><label><input name="'.$this->getName().'['.$value.']"' .
				'type="checkbox" value="'.SK_Language::htmlspecialchars($value).'"';
			
			if ( in_array($value, $selected_values) ) {
				$output .= ' checked="checked"';
			}
			
			$output .= ' class="checkbox_'.$this->getName().'" ';
			
			$label_text = $this->labels[$key];
			
			$output .= ' />'.$label_text.'</label></li>';
			//$output .= ' />'.SK_Language::htmlspecialchars($label_text, ENT_NOQUOTES).'</label></li>';
		}
		
		$output .= '</ul><br clear="all" />';
		
		return $output;
	}

	
}
