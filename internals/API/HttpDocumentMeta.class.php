<?php

require_once DIR_INTERNALS . 'utf8.php';

class SK_HttpDocumentMeta
{
	public $title;

	public $content_header;

	public $keywords;

	public $description;
        
        private static $meta;

	/**
	 * Get document meta info object.
	 *
	 * @return SK_HttpDocumentMeta
	 */
	final public function get()
	{
		$document_meta = new stdClass();

		$section = SK_Language::section('nav_doc_item');

        $document_key == null;
        $document = SK_HttpRequest::getDocument();
        if ( !empty($document) )
        {
            $document_key = SK_HttpRequest::getDocument()->document_key;
        }

		try {
		$document_meta->title = isset($this->title)
			? $this->title
			: $section->section('titles')->text($document_key);
		}
		catch (SK_LanguageException $e){
			$document_meta->title = $section->section('titles')->text('_default');
		}

		try {
		$document_meta->content_header = isset($this->content_header)
			? $this->content_header
			: $section->section('headers')->text($document_key);
		}
		catch (SK_LanguageException $e){
			$document_meta->content_header = $section->section('headers')->text('_default');
		}

		try {
		$document_meta->keywords = isset($this->keywords)
			? $this->keywords
			: $section->section('keywords')->text($document_key);
		}
		catch (SK_LanguageException $e){
			$document_meta->keywords = $section->section('keywords')->text('_default');
		}

		try {
		$document_meta->description = isset($this->description)
			? SK_UTF8::substr($this->description, 0, 100)
			: $section->section('descriptions')->text($document_key);
		}
		catch (SK_LanguageException $e){
			$document_meta->description = $section->section('descriptions')->text('_default');
		}
                
                $document_meta->meta = self::$meta;
                
		return $document_meta;
	}
        
        public function  addMeta( $name, $content, $nameType = 'name' )
        {
            $content = SK_Language::htmlspecialchars($content);
            self::$meta .= 
<<<EOT
<meta $nameType="$name" content="$content" />\n
EOT;
        }
}
