<?php

class component_Slideshow extends SK_Component
{
    private $slides = array();

	public function __construct( array $params = null )
	{
		parent::__construct('slideshow');

        $this->slides = app_Slideshow::getSlideList();

        if ( !$this->slides )
        {
            $this->annul();
        }
	}

    function prepare ( SK_Layout $Layout, SK_Frontend $Frontend )
    {
        $Frontend->include_js_file(URL_STATIC . 'slides.min.jquery.js');
        $Frontend->include_js_file(URL_STATIC . 'slideshow.js');

        $id = uniqid();

        $settings = SK_Config::section('slideshow')->getConfigsList();

        $params = array(
            'sizes' => app_Slideshow::getSizes($this->slides),
            'pagination' => $settings['navigation']->value ? "true" : "false",
            'interval' => $settings['interval']->value,
            'uniqname' => $id,
            'effect' => $settings['effect']->value,
            'preloadImage' => URL_LAYOUT . 'img/image_progress.gif'
        );

        $script = 'var slideshow' . $id . ' = new slideshow('.json_encode($params).'); slideshow' . $id . '.init();';

        $Frontend->onload_js($script);

        $Layout->assign('uniqName', $id);
    }
	
	public function render( SK_Layout $Layout )
	{
        $Layout->assign('slides', $this->slides);

		return parent::render($Layout);
	}
}