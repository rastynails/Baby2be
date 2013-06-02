<?php

class component_MemberHome extends SK_Component 
{
	public function __construct( array $params = null )
	{
		parent::__construct('member_home');
	}

    public function prepareTpl()
    {
        $layout_theme = SK_Layout::getInstance()->theme();
        $this->tpl_file = 'db:' . ($layout_theme ? $layout_theme : 'default') . ':home_page_code';     
    }
    
    public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
    {
        $this->prepareTpl();

        parent::prepare($Layout, $Frontend);
    }
    
    public static function clearCompile( $tpl_file = null, $compile_id = null )
    {
        $self = new self;
        $self->prepareTpl();
        
        return $self->clear_compiled_tpl();
    }
}
