<?php

class fieldType_set extends SK_FormField
{
	/**
	 * Possible values list.
	 *
	 * @var array
	 */
	protected $values = array();

	protected $type;

	protected $column_size;

	/**
	 * Label prefix (SkaDate6 profile fields compatibility).
	 *
	 * @var string
	 */
	public $label_prefix;

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

	public function setType($type)
	{
		$this->type = in_array($type, array('multicheckbox','select')) ? $type : 'radio';
	}

	public function setup( SK_Form $form )
	{
		if ( !$this->getValue() ) {
			$this->setValue(array());
		}

        if ($this->type == 'multicheckbox' || $this->type == 'radio' )
        {
            $this->js_presentation['$prototype_node']='{}';

            $this->js_presentation['construct']='
			function($input, formHandler, auto_id){
				var handler = this;

                var $node = $("input[name=\''.$this->getName().'_select_all\']");
                this.lastStatus = $node.prop("checked");

                $("input[name=\''.$this->getName().'\[\]\']").click(function(){
                    if ($node.prop("checked"))
                    {
                        $node.removeAttr("checked");
                        handler.lastStatus = false;
                    }
                });

				$node.click(function(){

                        if (handler.lastStatus)
                        {
                            $.each($("#"+auto_id+" li"), function(){
                                $(this).find("input").removeAttr("checked");
                            });
                            handler.lastStatus = false;
                        }
                        else
                        {
                            $.each($("#"+auto_id+" li"), function(){
                                $(this).find("input").prop("checked", "checked");
                            });
                            handler.lastStatus = true;
                        }
				});
			}
		';
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
	 * @param string $type 'check'|'select' default 'check'
	 * @param string $class an html class name
	 * @param integer $size a fixed size for a select
	 * @return string html
	 */
	public function render( array $params = null, SK_Form $form = null )
	{
		$type = (isset($params['type'])) ? $params['type'] : $this->type;

		switch ($type)
		{
		    case 'multiselect':
		        $params['multiple'] = 'multiple';
		        return $this->renderSelect( $params, $form );

		    case 'select':
		        $params['multiple'] = '';
		        return $this->renderSelect( $params, $form );

            default:
                return $this->renderMulticheck( $params, $form );
		}
	}


	private function renderMulticheck( array $params, SK_Form $form = null )
	{
		$output = '<ul id="' . $form->getTagAutoId($this->getName()) . '" ';
		if ( isset($params['class']) ) {
			$output .= ' class="'.$params['class'].'"';
		}
		$output .= '>';

		$labelsection = isset($params['labelsection'])
			? $params['labelsection'] : '%forms._fields.'.$this->getName().'.values';

		$selected_values = $this->getValue();

		$col_size = isset($params['col_size']) ? $params['col_size'] : $this->column_size;

		$style = isset($col_size) ? 'style="width:'.$col_size.'"':'';

        $select_all_checked = ($selected_values === $this->values) ? ' checked="checked" ' : '';

		foreach ( $this->values as $value )
		{
			$output .= '<li '.$style.' class="list_label '.$this->getName().'_value_'.$value.'" ><label><input name="'.$this->getName().'[]"' .
				'type="checkbox" value="'.SK_Language::htmlspecialchars($value).'"';

			if ( in_array($value, $selected_values) ) {
				$output .= ' checked="checked"';
			}

			$lang_key = $this->label_prefix ? $this->label_prefix.'_'.$value : $value;

			$label_text = SK_Language::text($labelsection.'.'.$lang_key);

			$output .= ' /><span class="'.$this->getName().'_label_'.$value.'">'.SK_Language::htmlspecialchars($label_text, ENT_NOQUOTES).'</span></label></li>';
		}

        $output .= '<li '.$style.' class="list_label" ><label><input name="'.$this->getName().'_select_all"' . 'type="checkbox" ';
        $output .= ' class="checkbox_'.$this->getName().'" '.$select_all_checked;
        $output .= ' />'.'<span class="'.$this->getName().'_label_select_all">'.SK_Language::text('forms._fields.set.select_all').'</span></label></li>';

		$output .= '</ul><br clear="all" />';

		return $output;
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

}