<?php

class SK_FormResponse
{
	/**
	 * Added errors.
	 *
	 * @var array
	 */
	private $errors = array();

	/**
	 * Added success messages.
	 *
	 * @var array
	 */
	private $messages = array();

	/**
	 * Debug variables.
	 *
	 * @var array
	 */
	private $debug_vars = array();

	/**
	 * A javascript code to execute.
	 *
	 * @var string
	 */
	private $js_code;

	/**
	 * Adds an error to response.
	 *
	 * @param string $err_msg
	 * @param string $field_name
	 */
	public function addError( $err_msg, $field_name = null )
	{
		if ( isset($field_name) ) {
			$this->errors[] = array($err_msg, $field_name);
		}
		else {
			$this->errors[] = $err_msg;
		}
	}

	/**
	 * Add a success message to response.
	 *
	 * @param string $msg_text
	 * @param string $field_name
	 */
	public function addMessage( $msg_text ) {
		$this->messages[] = $msg_text;
	}

	/**
	 * Check the response for having an errors.
	 *
	 * @return bool
	 */
	public function hasErrors() {
		return (bool)count($this->errors);
	}

	/**
	 * Add a javascript to execute.
	 *
	 * @param string $js_code
	 */
	public function exec( $js_code ) {
		SK_Frontend::concat_js($js_code, $this->js_code);
	}

	/**
	 * Add a redirect to the response.
	 *
	 * @param string $url
	 */
	public function redirect( $url ) {
		$this->exec('window.location.href = '.json_encode($url));
	}

    /**
     * Add a reload to the response.
     *
     */
    public function reload() {
        $this->exec('window.location.reload(true)');
    }

	/**
	 * Ajax debug.
	 *
	 * @param mixed $var
	 */
	public function debug( $var ) {
		$this->debug_vars[] = $var;
	}


	/**
	 * Process the response.
	 *
	 * @param mixed $return form.success(data)
	 */
	public function process( $return )
	{
		$response = array();


		if ( !empty($this->errors) ) {
                    $response['errors'] = $this->errors;
		}

		if ( DEV_MODE && !empty($this->debug_vars) ) {
                    $response['debug_vars'] = $this->debug_vars;
		}

		if ( $this->js_code ) {
                    $response['exec'] = $this->js_code;
		}

		if ( !empty($this->messages) ) {
                    $response['messages'] = $this->messages;
		}

                $response['data'] = $return;

		exit(json_encode($response));
	}

}
