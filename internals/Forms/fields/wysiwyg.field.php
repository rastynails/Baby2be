<?php

class field_wysiwyg extends fieldType_textarea
{
    public function __construct( $name )
    {
        parent::__construct($name);
    }

    public function render( array $params = null, SK_Form $form = null )
    {
        SK_Layout::frontend_handler()->include_js_file( URL_STATIC . 'ckeditor/ckeditor.js' );
        SK_Layout::frontend_handler()->onload_js( '
            
        var editor = CKEDITOR.replace(document.getElementById("' . $form->getTagAutoId($this->getName()) . '"));

        editor.on("change", function()
        {
            $( document.getElementById("' . $form->getTagAutoId($this->getName()) . '") ).val( editor.getData() );
        });' );

        return parent::render( $params, $form );
    }
}