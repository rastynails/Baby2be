<?php

class SK_AjaxResponse extends SK_Frontend
{
	/**
	 * Request components object model node.
	 *
	 * @var SK_FrontendCOMNode
	 */
	private $COM_node;

	/**
	 * Applied function.
	 *
	 * @var callback
	 */
	private $request_function;

	/**
	 * Applied function params.
	 *
	 * @var SK_HttpRequestParams
	 */
	private $func_params;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$node = json_decode(urldecode($_POST['COM_node']));
		$this->COM_node = new SK_FrontendCOMNode($node);

		$this->request_function = array("component_{$this->COM_node()->cmp_class()}", $_POST['apply_func']);

		$_params = json_decode(urldecode($_POST['params']), true);
		$this->func_params = new SK_HttpRequestParams($_params);

		$this->component_headers_defined = true;

		$this->forms_counter = -1;
	}

	/**
	 * Returns a reference to request components object model node.
	 *
	 * @return SK_FrontendCOMNode
	 */
	public function COM_node() {
		return $this->COM_node;
	}

	/**
	 * Create a new component instance.
	 *
	 * @param string $component_class
	 * @param array $params
	 * @return SK_Component
	 */
	private function initComponent( $component_class, array $params = null )
	{
		$class = "component_$component_class";
		$component = new $class($params);

		return $component;
	}

	/**
	 * Process the response.
	 */
	public function process()
	{
		$component_handler = new SK_ComponentFrontendHandler($this->COM_node->cmp_class());

                $response = array();
                $response['data'] = call_user_func($this->request_function, $this->func_params, $component_handler, $this);

		$exec = $component_handler->compile_js('this');
		self::concat_js($this->header_js, $exec);
		self::concat_js($this->onload_js, $exec);

		if ( strlen($exec) ) {
			$response['exec'] = $exec;
		}

		exit(json_encode($response));
	}

	/**
	 * Reload a frontend components by an object model.
	 *
	 * @param SK_FrontendCOMNode $COM_node
	 */
	public function reloadComponent( SK_FrontendCOMNode $COM_node, array $params = null )
	{
		$layout = SK_Layout::getInstance();

		$component = $this->initComponent($COM_node->cmp_class(), $params);

		$html_output = $layout->renderComponent($component, $COM_node->auto_id());

		$this->header_js(
			"\$(this.container_node).empty().before(".json_encode($html_output).").remove();\n"
		);

		$this->annulCOMNode($COM_node);
	}

	/**
	 * Unlink component object model node freeing used memory.
	 *
	 * @param SK_FrontendCOMNode $COM_node
	 */
	public function annulCOMNode( SK_FrontendCOMNode $COM_node )
	{
		$parent_id = $COM_node->parent()->auto_id;

		$js_output = isset($parent_id)
? <<<EOT
var children = window.sk_components['$parent_id'].children;

window.sk_components['$parent_id'].children = [];

for (var i = 0, child; child = children[i]; i++) {
	if (child.auto_id != '{$COM_node->auto_id()}') {
		window.sk_components['$parent_id'].children.push(child);
	}
}\n
EOT
: '';

		while ( $child_node = $COM_node->nextChild() ) {
			$js_output .= "delete window.sk_components['{$child_node->auto_id()}']\n";
			self::annulCOMNode($child_node);
		}

		while ( $form = $COM_node->nextForm() ) {
			$form_index = substr($form->auto_id, strlen('form_'));
			$js_output .= "delete window.sk_forms[$form_index];\n";
		}

		$this->header_js($js_output);
	}

}


class SK_FrontendCOMNode
{
	/**
	 * Component class name.
	 *
	 * @var string
	 */
	private $cmp_class;

	/**
	 * Component frontend auto_id.
	 *
	 * @var string
	 */
	private $auto_id;

	/**
	 * Children components.
	 *
	 * @var array
	 */
	private $children = array();

	/**
	 * Component forms.
	 *
	 * @var array
	 */
	private $forms = array();

	/**
	 * Parent component.
	 *
	 * @var SK_FrontendCOMNode
	 */
	private $parent;

	/**
	 * Children iterator index pointer.
	 *
	 * @var integer
	 */
	private $child_pointer = 0;

	/**
	 * Forms iterator index pointer.
	 *
	 * @var integer
	 */
	private $form_pointer = 0;

	/**
	 * Constructor.
	 *
	 * @param stdClass $COM_node
	 */
	public function __construct( stdClass $COM_node )
	{
		$this->cmp_class = $COM_node->cmp_class;
		$this->auto_id = $COM_node->auto_id;
		$this->forms = $COM_node->forms;
		$this->parent = @$COM_node->parent;

		foreach ( (array)@$COM_node->children as $key => $node ) {
			$this->children[$key] = new self($node);
		}
	}

	/**
	 * A component class getter.
	 *
	 * @return string
	 */
	public function cmp_class() {
		return $this->cmp_class;
	}

	/**
	 * Get a component frontend auto_id.
	 *
	 * @return string
	 */
	public function auto_id() {
		return $this->auto_id;
	}

	/**
	 * Child nodes iterator.
	 *
	 * @return SK_FrontendCOMNode
	 */
	public function nextChild()
	{
		if ( isset($this->children[$this->child_pointer]) ) {
			return $this->children[$this->child_pointer++];
		}

		$this->resetChildPointer();
		return false;
	}

	/**
	 * Reset a children iterator index pointer.
	 */
	public function resetChildPointer() {
		$this->child_pointer = 0;
	}

	/**
	 * Component forms iterator.
	 *
	 * @return stdClass
	 */
	public function nextForm()
	{
		if ( isset($this->forms[$this->form_pointer]) ) {
			return $this->forms[$this->form_pointer++];
		}

		$this->resetFormPointer();
		return false;
	}

	/**
	 * Reset a forms iterator index pointer.
	 */
	public function resetFormPointer() {
		$this->form_pointer = 0;
	}

	/**
	 * Parent component getter.
	 *
	 * @return SK_FrontendCOMNode
	 */
	public function parent() {
		return $this->parent;
	}

}
