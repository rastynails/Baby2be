<?php

class fieldType_select extends SK_FormField
{
	/**
	 * Possible values list.
	 *
	 * @var array
	 */
	protected $values = array();
	
	protected $type;
	
	public $label_prefix;
	
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
	
	/**
	 * Validator for field type of select.
	 * Note: validation works only if $this->values is sets up
	 * during $this->setup() and compiled with the form state as well.
	 *
	 * @param string $value
	 * @return string
	 */
	public function validate( $value )
	{
		$value = (string)$value;
		
		if ( $this->values && !in_array($value, $this->values) ) {
			throw new SK_FormFieldValidationException('illegal_value');
		}
		
		return $value;
	}
	
	
	public function setValues($values = array())
	{
		$this->values = $values;
	}
	
	
	public function setColumnSize($size)
	{
		$this->column_size = $size;
	}
	
	
	public function setType($type)
	{
		$this->type = in_array($type, array('fradio', 'radio', 'fselect', 'select')) ? $type : 'radio';
	}
	
	/**
	 * Render a field.
	 *
	 * @param string $name
	 * @param string $type 'select'|'radio' default 'select'
	 * @param string $class an html class name
	 * @return string html
	 */
	public function render( array $params = null, SK_Form $form = null )
	{	
		$type = (isset($params['type'])) ? $params['type'] : $this->type;
			
		return ( !isset($type) || ($type != 'radio' && $type != 'fradio'))
			? $this->renderSelect( $params, $form )
			: $this->renderRadios( $params, $form );
	}
	
	
	private function renderSelect( array $params = null, SK_Form $form = null )
	{
		$output = '<select id="' . $form->getTagAutoId($this->getName()) . '" name="'.$this->getName().'"';
		if ( isset($params['class']) ) {
			$output .= ' class="'.$params['class'].'"';
		}
		
		$invite_msg = isset($params['invite_msg']) ? $params['invite_msg'] : false;
		if ( isset($this->profile_field_id) ) {
			try {
				$invite_msg = SK_Language::text('profile_fields.select_invite_msg.' . $this->profile_field_id);
			}
			catch (SK_LanguageException $e){
				$invite_msg = ' ';
			}
		}
		
		$output .= '>';
		
		$labelsection = isset($params['labelsection'])
			? $params['labelsection'] : '%forms._fields.'.$this->getName().'.values';
		
		$selected_value = $this->getValue();
		
		$output .= $invite_msg ? '<option value="">' . $invite_msg . '</option>' : '';
		
		foreach ( $this->values as $value )
		{
			$output .= '<option value="'.SK_Language::htmlspecialchars($value).'"';
			
			if ( $value == $selected_value ) {
				$output .= ' selected="selected"';
			}
			
			$lang_key = $this->label_prefix ? $this->label_prefix.'_'.$value : $value;
			
			$label_text = SK_Language::text($labelsection.'.'.$lang_key);
			
			$output .= '>'.SK_Language::htmlspecialchars($label_text, ENT_NOQUOTES).'</option>';
		}
		
		$output .= '</select>';
		
		return $output;
	}
	
	
	private function renderRadios( $params = null, SK_Form $form )
	{
		$output = '<ul id="' . $form->getTagAutoId($this->getName()) . '" ';
		if ( isset($params['class']) ) {
			$output .= ' class="'.$params['class'].'"';
		}
		$output .= '>';
		
		$labelsection = isset($params['labelsection'])
			? $params['labelsection'] : '%forms._fields.'.$this->getName().'.values';
		
		$selected_value = $this->getValue();
		
		$col_size = isset($params['col_size']) ? $params['col_size'] : $this->column_size;
	
		$style = isset($col_size) ? 'style="width:'.$col_size.'"':'';
		
		foreach ( $this->values as $value )
		{
			$output .= '<li '.$style.' class="list_label '.$this->getName().'_value_'.$value.'" ><label><input name="'.$this->getName() .
				'" type="radio" value="'.SK_Language::htmlspecialchars($value).'"';
			
			if ( $value == $selected_value ) {
				$output .= ' checked="checked"';
			}
			
			$lang_key = $this->label_prefix ? $this->label_prefix.'_'.$value : $value;
			
			$label_text = SK_Language::text($labelsection.'.'.$lang_key);
			
			$output .= ' /><span class="'.$this->getName().'_label_'.$value.'">'.SK_Language::htmlspecialchars($label_text, ENT_NOQUOTES).'</span></label></li>';
		}
		
		$output .= '</ul><br clear="all" />';
		
		return $output;
	}
	
}
