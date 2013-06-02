<?php

class fieldType_date extends SK_FormField
{
	protected $invite_messages = array();

	protected $range = array();

	protected $invitePrefix = '%forms._fields.date.';

	/**
	 * Constructor.
	 *
	 * @param string $name
	 */
	public function __construct( $name ) {
		parent::__construct($name);
	}


	public function setup(SK_Form $Form){
		$this->js_presentation["validate"] = 'function(value, required) {
			if ( !required ) {
				return;
			}
			if ( !$.trim(value.year) ) {
				throw new SK_FormFieldValidationException("");
			}

			if ( !$.trim(value.month) ) {
				throw new SK_FormFieldValidationException("");
			}

			if ( !$.trim(value.day) ) {
				throw new SK_FormFieldValidationException("");
			}

		}';

		parent::setup($Form);
	}

	public function setInviteMsg($item, $msg)
	{
		$this->invite_messages[$item] = $msg;
	}

    public function setInvitePrefix( $prefix )
    {
        $this->invitePrefix = $prefix;
    }

	public function setRange($range){
		if (is_array($range)) {
			$this->range = $range;
		}
		else {
			$range = explode('-',$range);
			$this->range['min'] = $range[0];
			$this->range['max'] = $range[1];
		}
	}

	public $order;
	public function order($order = "desc") {
		$this->order = in_array($order, array("desc", "asc")) ? $order : "desc";
	}

	/**
	 * Validate a field type of age_range.
	 *
	 * @param array $value
	 * @return array list($from, $to)
	 */
	public function validate( $value )
	{
		if (is_string($value)) {
			list($yy, $mm, $dd) = explode('-', $value);

			$value = array('day' => $dd, 'month' => $mm, 'year' => $yy);
		}

		return (array)$value;
	}


	public function render( array $params = null, SK_Form $form = null )
	{

		$class_decl = isset($params['class']) ? ' class="'.$params['class'].'"' : '';

		$invite_prefix = isset($params['invite_prefix']) ? '%' . trim($params['invite_prefix']) : $this->invitePrefix;

		$value = $this->getValue();

		$output = array();
		$output['y'] = '<select name="'.$this->getName().'[year]"'.$class_decl.'>
					<option value="">'.(isset($this->invite_messages['year']) ? $this->invite_messages['year'] : SK_Language::text($invite_prefix.'year')).'</option>';

		$min = $this->range['min'];
		$max = $this->range['max'];

		if ($this->order == "desc") {
			for ( $a = $max; $a >= $min; $a-- ) {
				$selected = ($value['year']==$a) ? 'selected="selected"' : '';
				$output['y'] .= '<option value="'.$a.'" '.$selected.'>'.$a.'</option>';
			}

		} else {
			for ( $a = $min; $a <= $max; $a++ ) {
				$selected = ($value['year']==$a) ? 'selected="selected"' : '';
				$output['y'] .= '<option value="'.$a.'" '.$selected.'>'.$a.'</option>';
			}
		}



		$output['y'] .= "</select>\n";

		$output['m'] = '<select name="'.$this->getName().'[month]"'.$class_decl.'>
						<option value="">'.(isset($this->invite_messages['month']) ? $this->invite_messages['month'] : SK_Language::text($invite_prefix.'month')).'</option>';

		for ( $a = 1; $a <= 12; $a++ ) {
			$selected = ($value['month']==$a) ? 'selected="selected"' : '';
			$output['m'] .= '<option value="'.$a.'" '.$selected.'>'.SK_Language::text('%i18n.date.month_full_'.$a).'</option>';
		}

		$output['m'] .= "</select>\n";

		$output['d'] = '<select name="'.$this->getName().'[day]"'.$class_decl.'>
						<option value="">'.(isset($this->invite_messages['day']) ? $this->invite_messages['day'] : SK_Language::text($invite_prefix.'day')).'</option>';

		for ( $a = 1; $a <= 31; $a++ ) {
			$selected = ($value['day']==$a) ? 'selected="selected"' : '';
			$output['d'] .= '<option value="'.$a.'" '.$selected.'>'.$a.'</option>';
		}

		$output['d'] .= '</select>';

		$dateFormatString = SK_Config::section('site.official')->date_format;
		$dateFormatString = !empty($dateFormatString) ? $dateFormatString : 'd-m-y';
		$dateFormat = explode('-', $dateFormatString);

		$result = array();

		foreach ( $dateFormat as $i )
        {
            array_push($result, $output[$i]);
        }

		return implode('', $result);
	}


}
