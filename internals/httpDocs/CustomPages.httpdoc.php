<?php

class httpdoc_CustomPages extends SK_HttpDocument
{
    private $documentKey;

    public function __construct( array $params = null )
    {
        parent::__construct('custom_pages');

        $this->documentKey = SK_HttpRequest::getDocument()->document_key;

        if ( $this->documentKey == 'not_found' )
        {
            header("HTTP/1.1 404 Not Found", true, 404);
        }
    }

    public function render( SK_Layout $Layout )
    {
        $code = SK_Language::section('nav_doc_item.custom_code')->cdata($this->documentKey);
        $Layout->assign('code',	$code);

        return parent::render($Layout);
    }
}
