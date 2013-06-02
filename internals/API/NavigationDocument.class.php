<?php

class SK_NavigationDocument
{
    public $document_key;
    public $url;
    public $access_guest;
    public $access_member;
    public $base_document;
    public $status;
    public $custom;
    public $path;

    public function __construct( $document_key )
    {
        $document_key = trim($document_key);

        SK_Navigation::init();

        if ( !isset(SK_Navigation::$documents[$document_key]) )
        {
            $result = SK_MySQL::query(SK_MySQL::placeholder('SELECT * FROM `' . TBL_DOCUMENT . '` where `document_key`="?"', $document_key));
            SK_Navigation::$documents[$document_key] = $result->fetch_assoc();
        }

        if ( SK_Navigation::$documents[$document_key] )
        {
            foreach ( SK_Navigation::$documents[$document_key] as $field => $value )
            {
                $this->$field = $value;
            }
            if ( $this->custom )
            {
                $this->path = DIR_SITE_ROOT . 'custom_pages.php';
            }
            else
            {
                $url_info = parse_url($this->url);
                $this->path = DIR_SITE_ROOT . str_replace('/', DIRECTORY_SEPARATOR, $url_info["path"]);
            }

            $this->url = SITE_URL . $this->url;
        }
    }
}