<?php

class component_UserSelectorPopup extends SK_Component
{
    private $error = null;
    private $data = array();

    public function __construct()
    {
        parent::__construct('user_selector_popup');
    }

    public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
    {
        if ( $this->error )
        {
            $Frontend->onload_js('window.close();');

            return parent::prepare($Layout, $Frontend);
        }

        $this->frontend_handler = new SK_ComponentFrontendHandler('UserSelectorPopup');
        $this->frontend_handler->construct();

        return parent::prepare($Layout, $Frontend);
    }

    public function render( SK_Layout $Layout )
    {
        $Layout->assign('list', $this->data);

        return parent::render($Layout);
    }

    public function setError( $error = true )
    {
        $this->error = $error;
    }

    public function setData( $data )
    {
        $this->data = $data;
    }
}