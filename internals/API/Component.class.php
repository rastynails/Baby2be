<?php

abstract class SK_Component
{
	/**
	 * Component work directories namespace.
	 *
	 * @var string
	 */
	protected $namespace;

	/**
	 * The name of a component template file.
	 *
	 * @var string
	 */
	protected $tpl_file = 'default.tpl';

	/**
	 * Component compile id.
	 *
	 * @var string
	 */
	protected $compile_id;

	/**
	 * Component cache id.
	 *
	 * @var string
	 */
	protected $cache_id;

	/**
	 * Length of time in seconds that a component cache is valid.
	 *
	 * @var integer
	 */
	protected $cache_lifetime = 0;

	/**
	 * Says whether the component displayng is disabled.
	 *
	 * @var boolean
	 */
	private $annuled = false;

	/**
	 * Component layout auto id.
	 * Sets up by a SK_Layout when rendering.
	 *
	 * @var string
	 */
	public $auto_id;

	/**
	 * Document component meta data object.
	 * This works only for components which used as an http document.
	 *
	 * @var SK_HttpDocumentMeta
	 */
	public $document_meta;

	/**
	 * Component frontend javascript handler object.
	 *
	 * @var SK_ComponentFrontendHandler
	 */
	public $frontend_handler;

	/**
	 * Component forms.
	 *
	 * @var array
	 */
	public $forms = array();

	public $vars = array();

	/**
	 * Component constructor.
	 *
	 * @param string $namespace
	 * @return SK_Component
	 */
	protected function __construct( $namespace ) {
		$this->namespace = $namespace;
	}

        /**
         * @return SK_HttpDocumentMeta
         */
        public function getDocumentMeta()
        {
            if ( empty($this->document_meta) )
            {
                $this->document_meta = new SK_HttpDocumentMeta;
            }

            return $this->document_meta;
        }

	/**
	 * Returns the namespace of a component.
	 *
	 * @return string
	 */
	public function getNamespace() {
		return $this->namespace;
	}

	/**
	 * Annul the component to disable it displaying.
	 */
	public function annul() {
		$this->annuled = true;
	}

	/**
	 * Check wether the component is annuled.
	 *
	 * @return boolean
	 */
	public function annuled() {
		return $this->annuled;
	}

	/**
	 * This method calls just before calling a SK_Component::render()
	 * and aims to prepare SK_Layout object to render a current component.
	 * Register any template functions here to avoid an errors when
	 * the component caching is enabled.
	 *
	 * @param SK_Layout $Layout
	 * @param SK_Frontend $Frontend
	 */
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend ) {
        if(empty($this->vars)) return;

        foreach($this->vars as $name => $value)
            $Layout->assign($name, $value);

	}

	/**
	 * Component render event-method.
	 * You can assign any cacheable template variables overwriting this method in the child class.
	 *
	 * @param SK_Layout $Layout
	 * @return boolean
	 */
	public function render( SK_Layout $Layout ) {
		return !$this->annuled;
	}

	/**
	 * An optional abstract method for handling forms.
	 *
	 * @param SK_Form $form
	 */
	public function handleForm( SK_Form $form ) {}

	/**
	 * Returns the params for display component.
	 *
	 * @return array list($tpl_file, $cache_id, $compile_id, $cache_lifetime)
	 */
	final public function getDisplayParams()
	{
		if ( $this->annuled ) {
			return null;
		}

		if ( strpos($this->tpl_file, 'db:') !== 0 ) {
			$tpl_file_src = ($this instanceof SK_HttpDocument ? 'httpdocs' : 'components') . DIRECTORY_SEPARATOR.
				$this->namespace . DIRECTORY_SEPARATOR . $this->tpl_file;
		}
		else {
			$tpl_file_src = $this->tpl_file;
		}

		return array($tpl_file_src, $this->cache_id, $this->compile_id, $this->cache_lifetime);
	}

	/**
	 * Get auto id prefixed id value for an html element.
	 *
	 * @param string $id_value
	 * @return string
	 */
	final public function getTagAutoId( $id_value ) {
		return $this->auto_id.'-'.$id_value;
	}

	/**
	 * Clear compiled php files.
	 *
	 * @param string $tpl_file
	 * @param string $compile_id
	 * @return boolean
	 */
	public function clear_compiled_tpl( $tpl_file = null, $compile_id = null )
	{
		if ( isset($tpl_file) ) {
			$this->tpl_file = $tpl_file;
		}

		if ( isset($compile_id) ) {
			$this->compile_id = $compile_id;
		}

		return SK_Layout::getInstance()->clear_compiled_tpl1($this);
	}

	/**
	 * Ajax component reload backend implementation.
	 *
	 * @param array|object $params
	 * @param SK_ComponentFrontendHandler $handler
	 * @param SK_AjaxResponse $response
	 */
	public static function reload(
		$params, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response
	) {
		$_params = array();

		if ($params instanceof SK_HttpRequestParams) {
			$params = $params->params;
		}

		if ( $params ) {
			foreach ( $params as $key => $value ) {
				$_params[$key] = $value;
			}
		}

		$response->reloadComponent($response->COM_node(), $_params);
	}

}
