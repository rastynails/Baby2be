<?php

class component_ClsSearch extends SK_Component
{	
	
	public function __construct()
	{
	    if ( !app_Features::isAvailable(45) )
        {
            SK_HttpRequest::showFalsePage();
        }
        
		parent::__construct('cls_search');
	}
	
	/**
	 * @see SK_Component::prepare()
	 *
	 * @param SK_Layout $Layout
	 * @param SK_Frontend $Frontend
	 */
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{		
        $script = '$("#cls-search-input").focus(function(){
           var label = '.json_encode(SK_Language::text('%label.cls_search')).';
           if ( $(this).val() == label )
           {
               $(this).removeClass("invitation").val("");
           }
       }).blur(function(){
           if ( $(this).val() == "" )
           {
               var label = '.json_encode(SK_Language::text('%label.cls_search')).';
               $(this).addClass("invitation").val(label);
           }
       });
       ';

        $Frontend->onload_js($script);

		return parent::prepare( $Layout, $Frontend );
	}
	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 * @return unknown
	 */
	public function render( SK_Layout $Layout )
	{
        $search = !empty($_GET['search']) ? strip_tags($_GET['search']) : null;
        $Layout->assign('search', $search);

		return parent::render($Layout);
	}
}
