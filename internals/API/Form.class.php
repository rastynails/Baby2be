<?php

abstract class SK_Form
{
	/**
	 * The name of a form extension class.
	 *
	 * @var string
	 */
	private $class;
	
	/**
	 * The name of a form.
	 *
	 * @var string
	 */
	private $name;
	
	/**
	 * An array of fields objets.
	 *
	 * @var array
	 */
	public $fields = array();
	
	/**
	 * Keeps a keys of fields type of hidden.
	 *
	 * @var array
	 */
	private $hidden_fields = array();
	
	/**
	 * Form actions.
	 *
	 * @var array
	 */
	private $actions = array();
	
	/**
	 * If you have more than one actions in a form during $this->setup() set this value to the name
	 * of the action which must processed by default when the user presses "Enter" button.
	 *
	 * @var string
	 */
	protected $default_action = '';
	
	/**
	 * Form frontend data store.
	 * This data sets up and most used by a frontend handler.
	 *
	 * @var array
	 */
	public $frontend_data = array();
	
	/**
	 * Form javascript frontend handler object.
	 *
	 * @var SK_FormFrontendHandler
	 */
	public $frontend_handler;
	
	/**
	 * Constructor.
	 *
	 * @param string $form_name
	 */
	protected function __construct( $form_name )
	{
		$this->name = $form_name;
		$this->class = get_class($this);
	}
	
	/**
	 * Register a form fields and actions.
	 */
	abstract public function setup();
	
	/**
	 * Form JS prototype presentation.
	 *
	 * @return string scriptable object json
	 */
	public function js_prototype()
	{
		$field_list = array();
		foreach ( $this->fields as $field_key => $field ) {
			$field_list[] = "\n\t\t$field_key: " .
				str_replace("\n", "\n\t\t", $field->js_presentation());
		}
		
		$action_list = array();
		foreach ( $this->actions as $action_name => $action ) {
			$action_list[] = "\n\t\t$action_name: " .
				str_replace("\n", "\n\t\t", $action->js_presentation());
		}
		$default_action = (count($this->actions) == 1) ? $action_name : $this->default_action;
		
		return "{
	name: '$this->name',\n\t
	fields: {".implode(',', $field_list)."\n\t},
	actions: {".implode(',', $action_list)."\n\t},
	default_action: '$default_action'
}";
	}
	
	/**
	 * Register and setup a form field.
	 *
	 * @param string|SK_FormField $field
	 */
	protected function registerField( $field )
	{
		if ( is_string($field) ) {
			$field_class = "field_$field";
			$field = new $field_class;
		}
		
		if ( !is_a($field, 'SK_FormField') ) {
			throw new SK_FormException('$field is\'t an instance of SK_FormField', 0);
		}
		
		$field->setup($this);
		
		$field_key = $field->getName();
		
		if ( isset($this->fields[$field_key]) ) {
			throw new SK_FormException(
				'field with key name "'.$field_key.'" is already registered',
				SK_FormException::DUPLICATE_FIELD_KEY_ENTRY
			);
		}
		
		$this->fields[$field_key] = $field;
		
		if ( is_a($field, 'fieldType_hidden') ) {
			$this->hidden_fields[] = $field_key;
		}
	}
	
	/**
	 * Register an action for the form.
	 *
	 * @param string|SK_FormAction $action
	 */
	protected function registerAction( $action )
	{
		if ( is_string($action) ) {
			$action = new $action;
		}
		
		if ( !is_a($action, 'SK_FormAction') ) {
			throw new SK_FormException('$action is not an instance of SK_FormAction', 0);
		}
		
		$action->setup($this);
		
		$action_name = $action->getName();
		
		if ( isset($this->actions[$action_name]) ) {
			throw new SK_FormException(
				'"'.$action_name.'" action is already registered',
				SK_FormException::DUPLICATE_ACTION_ENTRY
			);
		}
		
		$this->actions[$action_name] = $action;
	}
	
	
	public static function __set_state( array $params )
	{
		$form_class = $params['class'];
		$form_name = $params['name'];
		unset($params['class'], $params['name']);
		
		$_this = new $form_class($form_name);
		
		foreach ( $params as $prop => $value )
			$_this->$prop = $value;
		
		// js frontend handler is always afresh
		$_this->frontend_handler = new SK_FormFrontendHandler($form_class);
		
		return $_this;
	}
	
	/**
	 * Get the name of a form.
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Render a form start html.
	 *
	 * @param array $params
	 * @return string html
	 */
	public function renderStart( array $params = null )
	{
		$output = '<form id="'.$this->frontend_data['auto_id'].'" method="post" onsubmit="return false;">';
		
		// rendering hidden fields
		foreach ( $this->hidden_fields as $field_key ) {
			$output .= "\n" . $this->fields[$field_key]->render();
		}
		
		foreach ( $this->actions as $action ) {
			// registering confirmations requiring
			$confirm_msg = $action->getConfirmation();
			if ( $confirm_msg ) {
				SK_Layout::frontend_handler()->registerLanguageValue($confirm_msg);
			}
			
			// registering required fileds messages
			$required_fields = $action->required_fields();
			foreach ( $required_fields as $field_key ) {
				SK_Layout::frontend_handler()->registerLanguageValue(
					$this->getErrorKey($field_key, 'required')
					, "\$forms.$this->name.fields.$field_key.errors.required"
				);
			}
		}
		
		return $output;
	}
	
	/**
	 * Render a form field label html.
	 *
	 * @param array $params
	 * @return string html
	 */
	public function renderFieldLabel( array $params )
	{
		if ( isset($params['text']) ) {
			$text = $params['text']; // text as attribute from template
			SK_Layout::frontend_handler()
				->assignLanguageValue($text
					, "\$forms.$this->name.fields.{$params['for']}"
				);
		}
		else { // auto label
			try {
				$section = 'forms.'.$this->name.'.fields.'.$params['for'];
				$text = SK_Language::section($section)->text('label');
			}
			catch ( SK_LanguageException $e ) {
				$section = 'forms._fields.'.$params['for'];
				$text = SK_Language::text("$section.label");
			}
			
			SK_Layout::frontend_handler()
				->registerLanguageValue("$section.label"
					, "\$forms.$this->name.fields.{$params['for']}"
				);
		}
		
		return '<label>'.$text.'</label>'; // $params['for']
	}
	
	/**
	 * Render a form field html.
	 *
	 * @param array $params
	 * @return string html
	 */
	public function renderField( array $params )
	{
		$name = $params['name'];
		
		if ( isset($params['label']) ) {
			SK_Layout::frontend_handler()
				->assignLanguageValue($params['label']
					, "\$forms.$this->name.fields.$name"
				);
		}
		
		if ( !isset($this->fields[$name]) ) {
			trigger_error(__CLASS__.'::'.__FUNCTION__.'() field "'.
				$name.'" is not defined in "'.$this->name.'" form'
				, E_USER_WARNING);
			return;
		}
		
		$html_output = $this->fields[$name]->render($params, $this);
		
		return
<<<EOT
$html_output
<div id="{$this->frontend_data['auto_id']}-$name-container"></div>
EOT;
	}
	
	/**
	 * Render a custom input item.
	 *
	 * @param array $params
	 */
	public function renderInputItem( array $params )
	{
		$name = $params['name'];
		
		if ( !isset($this->fields[$name]) ) {
			trigger_error(__CLASS__.'::'.__FUNCTION__.'() field "'.
				$name.'" is not defined in "'.$this->name.'" form'
				, E_USER_WARNING);
			return;
		}
		
		$_field = $this->fields[$name];
		
		if ( !is_a($_field, 'fieldType_custom_set') ) {
			// TODO: trigger a more understandable error
		}
		
		return $this->fields[$name]->renderItem($params, $this);
	}
	
	/**
	 * Render a form button html.
	 *
	 * @param array $params
	 * @return string html
	 */
	public function renderButton( array $params )
	{
		if ( !isset($this->actions[$params['action']]) ) {
			trigger_error(__CLASS__.'::'.__FUNCTION__.'() action "'.
				$params['action'].'" is not defined in "'.$this->form_name.'" form'
				, E_USER_WARNING);
			return;
		}
		
		$tabindex = '';
		if (isset($params['tabindex']) && $params['tabindex'] = intval($params['tabindex'])) {
			$tabindex = 'tabindex="'.$params['tabindex'].'"';
		}
		
		if (isset($params['label'])){
			$label = $params['label'];
		}
		else{
			try {
				$label_section = "forms.$this->name.actions";
				$label = SK_Language::section($label_section)->text($params['action']);
			}
			catch ( SK_LanguageException $e ) {
				$label_addr = 'forms._actions.'.$params['action'];
				$label = SK_Language::text($label_addr);
			}
		}
		$class = '';
		if (isset($params['class'])) {
			$class = 'class="' . trim($params['class']) . '"';
		}
		
		$id = $this->frontend_data['auto_id'].'-'.$params['action'].'-button';
		
		$type = (count($this->actions) == 1 || $params['action'] == $this->default_action) ? 'submit' : 'button';
		
		return '<input '.$class.' id="'.$id.'" type="'.$type.'" value="'.$label.'" '.$tabindex.' />';
	}
	
	/**
	 * Get the reference to a form action object.
	 *
	 * @param string $action_name
	 * @throws SK_FormException
	 * @return SK_FormAction
	 */
	protected function getAction( $action_name ) {
		if ( !isset($this->actions[$action_name]) ) {
			throw new SK_FormException(
				'unrecognized action "'.$action_name.'"',
				SK_FormException::UNRECOGNIZED_ACTION
			);
		}
		
		return $this->actions[$action_name];
	}
	
	/**
	 * Get the reference to a form field object.
	 *
	 * @param string $field_name
	 * @throws SK_FormException
	 * @return SK_FormField
	 */
	public function getField( $field_name )
	{
		if ( !isset($this->fields[$field_name]) ) {
			throw new SK_FormException(
				'unrecognized field "'.$field_name.'"',
				SK_FormException::UNRECOGNIZED_FIELD
			);
		}
		
		return $this->fields[$field_name];
	}
	
	
	/**
	 * Get auto id prefixed id value for a form html element.
	 *
	 * @param string $id_value
	 * @return string
	 */
	final public function getTagAutoId( $id_value ) {
		return $this->frontend_data['auto_id'].'-'.$id_value;
	}
	
	/* --- Form Processing --- */
	
	/**
	 * Process the form.
	 *
	 * @param array $post_data
	 * @param string $action_name
	 */
	public function process( array $post_data, $action_name, SK_FormResponse $response )
	{
		$action = $this->getAction($action_name);
		$proccess_fields = $action->getProcessFields();
		
		$form_data = array();
		foreach ( $proccess_fields as $field_name => $required )
		{
			$field = $this->getField($field_name);
			

			if ( !empty($post_data[$field_name]) && ( is_array($post_data[$field_name]) || trim($post_data[$field_name])))  
			{
				try {
					$field->setValue($post_data[$field_name]);
					$form_data[$field_name] = $field->getValue();
				}
				catch ( SK_FormFieldValidationException $e ) {
					$err_msg = $this->getErrorMessage($field_name, $e->getErrorKey(), @$field->profile_field_id);
					$response->addError($err_msg, $field_name);
				}
			}
			elseif ( $required ) {
				$err_msg = $this->getErrorMessage($field_name, 'required');
				$response->addError($err_msg, $field_name);
			}
			else {
				$form_data[$field_name] = null;
			}
		}
		
		$action->checkData($form_data, $response, $this);
		
		if ( !$response->hasErrors() ) {
			return $action->process($form_data, $response, $this);
		}
	}
	
	/**
	 * Get auto error message on current language.
	 *
	 * @param string $field_name
	 * @param string $error_key
	 * @param integer $profile_field_id
	 */
	private function getErrorMessage( $field_name, $error_key, $profile_field_id = null )
	{
		if ( isset($profile_field_id) ) {
			$profile_fields_section = 'profile_fields.error_msg';
			$message = SK_Language::section($profile_fields_section)->text($profile_field_id);
			return $message;
		}
		
		try {
			$preferred_section = 'forms.'.$this->name.'.fields.'.$field_name.'.errors';
			$message = SK_Language::section($preferred_section)->text($error_key);
		}
		catch ( SK_LanguageException $e ) {
			$default_section = 'forms._fields.'.$field_name.'.errors';
			try {
				$message = SK_Language::section($default_section)->text($error_key);
			} catch (SK_LanguageException $e) {
				$default_section = 'forms._errors';
				$message = SK_Language::section($default_section)->text($error_key);
			}
		}
		
		return $message;
	}
	
	
	private function getErrorKey( $field_name, $error_key )
	{
		try {
			$section = 'forms.'.$this->name.'.fields.'.$field_name.'.errors';
			SK_Language::section($section)->text($error_key);
		}
		catch ( SK_LanguageException $e ) {
			try {
				$section = 'forms._fields.'.$field_name.'.errors';
				SK_Language::section($section)->text($error_key);
			}
			catch ( SK_LanguageException $e ) {
				$section = 'forms._errors';
				SK_Language::section($section)->text($error_key);
			}
		}
		
		return "$section.$error_key";
	}
	
}


class SK_FormException extends Exception
{
	const	DUPLICATE_FIELD_KEY_ENTRY	= 1,
			DUPLICATE_ACTION_ENTRY		= 2,
			UNRECOGNIZED_FIELD			= 3,
			UNRECOGNIZED_ACTION			= 4;
	
}

