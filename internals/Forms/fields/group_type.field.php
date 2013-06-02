<?php

class field_group_type extends SK_FormField  
{
	protected $values = array();
	
	public function __construct($name)
	{
		parent::__construct($name);
	}
	
	public function setValues($values = array())
	{
		$this->values = $values;
	}
	
	public function setup(SK_Form $form)
	{
		parent::setup($form);
		
		$this->js_presentation['construct'] = '
			function($input, form){
				var handler = this;
				this.input = $input;
				
				$input.change(function(){
					if (this.value == "closed") {
						$(".claim_config").fadeIn("fast");
					} else {
						$(".claim_config").fadeOut("fast");
					}
				});
				
				$input.change();
			}
		';
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
		return $value;
	}
	
	public function render( array $params = null, SK_Form $form = null )
	{		
		$output = '<select id="' . $form->getTagAutoId($this->getName()) . '" name="'.$this->getName().'[select]"';
		if ( isset($params['class']) ) {
			$output .= ' class="'.$params['class'].'"';
		}
		
		$invite_msg = isset($params['invite_msg']) ? $params['invite_msg'] : false;
		if ( isset($this->profile_field_id) ) {
			try {
				$invite_msg = SK_Language::section('profile_fields.select_invite_msg')->text($this->profile_field_id);
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
			
			if ( $value == $selected_value['select'] ) {
				$output .= ' selected="selected"';
			}
			
			$lang_key = $this->label_prefix ? $this->label_prefix.'_'.$value : $value;
			
			$label_text = SK_Language::text($labelsection.'.'.$lang_key);
			
			$output .= '>'.SK_Language::htmlspecialchars($label_text, ENT_NOQUOTES).'</option>';
		}
		$output .= '</select>';
		
		// checkbox		
		$output .= '<div class="claim_config" style="display: none;"><input id="' . $form->getTagAutoId($this->getName()) . '" name="'.$this->getName().'[checkbox]" type="checkbox" ';
			
		if ( isset($params['class']) ) {
			$output .= 'class="'.$params['class'].'" ';
		}
		
		if ( $selected_value['checkbox'] ) {
			$output .= 'checked="checked" ';
		}
		
		$output .= 'value="1" />';
		
		if ( isset($params['label']) ) {
			$output = '<label>' . $output . $params['label'] . '</label>';
		}
		$output .= "</div>";
		
		return $output;
	}
}
