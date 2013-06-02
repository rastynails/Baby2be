<?php
require_once DIR_APPS.'appAux/BadwordDao.php';
require_once DIR_APPS.'appAux/SmileDao.php';
require_once DIR_APPS.'appAux/Inputfilter.php';

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Dec 07, 2008
 *
 */
final class app_TextService
{
	/**
	 * @var array
	 */
	private $configs;

	/**
	 * @var array
	 */
	private $badwords;

	/**
	 * @var array
	 */
	private $smiles;

	/**
	 * @var array
	 */
	private $smiles_arr;

	/**
	 * @var BadwordDao
	 */
	private $badword_dao;

	/**
	 * @var SmileDao
	 */
	private $smile_dao;

	/**
	 * @var array
	 */
	private $valid_tags;

	/**
	 * @var array
	 */
	private $valid_tags_array;

	/**
	 * @var array
	 */
	private $pair_valid_tags;

	/**
	 * @var array
	 */
	private $valid_attrs;

	/**
	 * @var array
	 */
	private $search_array;

	/**
	 * @var app_TextService
	 */
	private static $classInstance;

	/**
	 * Class constructor
	 */
	private function __construct ()
	{
		$this->badword_dao = new BadwordDao();
		$this->smile_dao = new SmileDao();

		$this->configs['on_page_count'] = 10;

		$section_conf = new SK_Config_Section( 'badwords' );

		$this->configs['feature'][FEATURE_BLOG] = $section_conf->get( FEATURE_BLOG );
		$this->configs['feature'][FEATURE_COMMENT] = $section_conf->get( FEATURE_COMMENT );
		$this->configs['feature'][FEATURE_EVENT] = $section_conf->get( FEATURE_EVENT );
		$this->configs['feature'][FEATURE_FORUM] = $section_conf->get( FEATURE_FORUM );
		$this->configs['feature'][FEATURE_PHOTO] = $section_conf->get( FEATURE_PHOTO );
		$this->configs['feature'][FEATURE_VIDEO] = $section_conf->get( FEATURE_VIDEO );
		$this->configs['feature'][FEATURE_GROUP] = $section_conf->get( FEATURE_GROUP );
        $this->configs['feature'][FEATURE_SHOUTBOX] = $section_conf->get( FEATURE_SHOUTBOX );
        $this->configs['feature'][FEATURE_CHAT] = $section_conf->get( FEATURE_CHAT );
        $this->configs['feature'][FEATURE_PROFILE] = $section_conf->get( FEATURE_PROFILE );
        $this->configs['feature'][FEATURE_CLASSIFIEDS] = $section_conf->get( FEATURE_CLASSIFIEDS );
        $this->configs['feature'][FEATURE_MAILBOX] = $section_conf->get( FEATURE_MAILBOX );
        $this->configs['feature'][FEATURE_TAGS] = $section_conf->get( FEATURE_TAGS );
        $this->configs['feature'][FEATURE_MUSIC] = $section_conf->get( FEATURE_MUSIC );
                
		$badwords = $this->badword_dao->findAll();

		$this->badwords = array();

		foreach ( $badwords as $value )
                {
                    $this->badwords[] = array( 'badword' => $value->getLabel(),
                                   'type' =>  $value->getType());
                }

		//gets smiles from BD and makes smiles array
		$smiles = $this->smile_dao->findAll();

		foreach ( $smiles as $key=>$value )
		{
			$code_arr = explode(',', $value->getCode());

			$this->smiles_arr[] = '<div class="smile float_left"><img  alt="'.$code_arr[0].'" title="'.$code_arr[0].'" src="'.URL_SMILE_IMG.$value->getUrl().'" /></div>';

			$str_code = '%%!'.$value->getId().'!%%';
			$replace_code = $code_arr[0];

			$this->smiles['str_code'][] = $str_code;
			$this->smiles['replace_code'][] = $replace_code;

			foreach ( $code_arr as $code )
			{
                            //for $string without htmlspecialchars
                            $this->smiles['search'][] = $code;
                            $this->smiles['replace'][] = '<img class="smile" alt="'.$str_code.'" title="'.$str_code.'" src="'.URL_SMILE_IMG.$value->getUrl().'" />';
                            //for $sring with htmlspecialchars
                            $this->smiles['search'][] = htmlspecialchars( $code );
                            $this->smiles['replace'][] = '<img class="smile" alt="'.$str_code.'" title="'.$str_code.'" src="'.URL_SMILE_IMG.$value->getUrl().'" />';
			}
		}

		// Preparing data for badwords replacement
	 	$this->search_array = array();

		foreach ( $this->badwords as $badword )
		{
            switch( $badword['type'] )
            {
                case 'word' :

                    $badword['badword'] = str_replace( '\*', '[\w~@\#$%^&*()_+|]*', preg_quote($badword['badword']) );

                    //$this->search_array[] = '#(^'.$badword.'(?=\s|$))|((?<=\s)'.$badword.'(?=\s|[.;:,?!]|$))#i';
                    $this->search_array[] = '#(\b'.$badword['badword'].'\b)#i';

                    break;
                case 'string' :

                    $badword['badword'] = str_replace( '\*', '[\w~@\#$%^&*()_+|]*', preg_quote($badword['badword']) );
                    $this->search_array[] = '#('.$badword['badword'].')#i';

                    break;

                case 'regexp' :

                    $this->search_array[] = '#('.$badword['badword'].')#i';

                    break;
            }
		}
		//printArr($this->search_array);
		$this->configs['title_replacement'] = SK_Language::text( 'txt.badword_replacement_title' );
		$this->configs['text_replacement'] = SK_Language::text( 'txt.badword_replacement_text' );

		// Valid tags for features
		$this->valid_tags = array(
			FEATURE_BLOG => '<u><i><b><a><span><img><center><ul><ol><li><style><div><style><input><strong><em><p><strike><sub><sup><table><thead><tbody><tfoot><tr><td><hr><h1><h2><h3><h4><h5><h6><blockquote><caption><pre><address>',
			FEATURE_MAILBOX => '<u><i><b><a><span><center><img><style>',
			FEATURE_FORUM => '<u><i><b><a><span><center><img><style><div>',
			FEATURE_GROUP => '<u><i><b><a><span><center><style>',
			FEATURE_COMMENT => '<u><i><b><a><span><center><style>',
			'profile_view' => '<u><i><b><a><span><img><center><ul><ol><li><object><embed><param><div><style><table><th><tr><td><tbody><thead><tfoot>'
		);

		$this->valid_tags_array = array(
			FEATURE_BLOG => array('u','i','b','a','span','img','center','ul','ol','li','div','input','strong','em','p','strike','sub','sup','table','thead','tbody','tfoot','tr','td','hr','h1','h2','h3','h4','h5','h6','blockquote','caption','pre','address','&nbsp;'),
			FEATURE_MAILBOX => array('u','i','b','a','span','center','img','style'),
			FEATURE_FORUM => array('u','i','b','a','span','center','img','style', 'div'),
			FEATURE_GROUP => array('u','i','b','a','span','center','style'),
			FEATURE_COMMENT => array('u','i','b','a','span','center','style'),
			'profile_view' => array('u','i','b','a','span','img','center','ul','ol','li','object','embed','param','div','style',
				'table', 'th', 'tr', 'td', 'thead', 'tbody', 'tfoot')
		);

		$this->valid_attrs = array('style', 'id', 'class', 'name', 'href', 'width', 'height', 'align', 'title', 'type', 'value', 'src',
		    'classid', 'codebase', 'align', 'height', 'width', 'pluginspage', 'quality', 'scale', 'salign', 'wmode' , 'bgcolor', 'FlashVars',
		    'allowScriptAccess', 'type', 'target', 'border', 'cellpadding', 'cellspacing', 'summary', 'alt', 'dir');

		$this->pair_valid_tags = array( 'u','i','b','a','span','img','center','ul','ol','li','style','div','input','strong','em','p','strike','sub','sup','table','thead','tbody','tfoot','tr','td','hr','h1','h2','h3','h4','h5','h6','blockquote','caption','pre','address' );
	}

	/**
	 * Returns the only instance of the class
	 *
	 * @return app_TextService
	 */
	public static function newInstance ()
	{
		if ( self::$classInstance === null )
			self::$classInstance = new self();
		return self::$classInstance;
	}

	/**
	 * Returns service configs
	 *
	 * @return array
	 */
	public function getConfigs ()
	{
		return $this->configs;
	}

	/* ------ !Class auxilary methods devider! ------- */



	/**
	 * Returns paged badword list
	 *
	 * @param integer $page
	 * @return array
	 */
	public function findBadwordList( $page, $type )
	{
		$first = ( $page - 1 ) * $this->configs['on_page_count'];
		$count = $this->configs['on_page_count'];

		return $this->badword_dao->findBadwordList( $first, $count, $type );
	}


	/**
	 * Deletes badword by id
	 *
	 * @param integer $id
	 */
	public function deleteBadwordById( $id )
	{
		$this->badword_dao->deleteById( $id );
	}


	/**
	 * Saves and updates badword entry
	 *
	 * @param Badword $badword
	 */
	public function saveOrUpdate( Badword $badword )
	{
		$this->badword_dao->saveOrUpdate( $badword );
	}


	/**
	 * Returns badword by label
	 *
	 * @param string $label
	 * @return Badword || null
	 */
	public function findBadwordByLabel( $label, $type )
	{
		return $this->badword_dao->findBadwordByLabel( trim( $label ), $type );
	}


	/**
	 * Returns badword by id
	 *
	 * @param integer $id
	 * @return Badword || null
	 */
	public function findBadwordById( $id )
	{
		return $this->badword_dao->findById( $id );
	}


	/**
	 * Returns badwords count
	 *
	 * @return integer
	 */
	public function getBadwordsCount($type)
	{
		return $this->badword_dao->getBadwordsCount($type);
	}


	/**
	 * Replaces badwords with #censored# string
	 *
	 * @param string $text
	 * @param string $feature
	 * @param string $replacement
	 * @return string
	 */
	public function censor( $text, $feature, $title = false )
	{
		if( !$this->configs['feature'][$feature] )
        {
			return $text;
        }

        $std_raplacement =  $title ? $this->configs['title_replacement'] : $this->configs['text_replacement'];

		return preg_replace( $this->search_array, $std_raplacement, $text );
	}


	/**
	 * Removes badwords from provided array
	 *
	 * @param array $tags
	 * @return array
	 */
	public function censorArray( array $array )
	{
		if( empty( $array ) )
			return array();

		//$result_array = explode( " , ", preg_replace( $this->search_array, "", implode( " , ", $array ) ) );

		$return_array = str_ireplace( $this->badwords, '', $array );

		foreach ( $return_array as $key => $value )
			if( trim( strlen( $value ) ) < 3 )
				unset( $return_array[$key] );

		return $return_array;
	}


	/**
	 * Replaces smiles codes with image url string
	 *
	 * @param $string string
	 * @return string
	 */
	public function handleSmiles( $string )
	{
		$string = str_replace( $this->smiles['search'], $this->smiles['replace'], $string );

		return str_replace( $this->smiles['str_code'], $this->smiles['replace_code'], $string );
	}

	/**
	 * Gets smiles from BD
	 *
	 * @return array
	 */
	public function getSmiles()
	{
		return $this->smiles_arr;
	}

	public function outputFormatter( $text, $feature = false, $nl2br = true )
	{
		$text = str_replace('<!--', '', $text);

                $text = str_replace("\n\n", "\n", $text);

        $return_text = str_replace('"javascript://', '"http://', $return_text);

        $text = preg_replace('/(?!<.*?href\s*=\s*[\'\"]\s*)(?!<.*?src\s*=\s*[\'\"]\s*)(http(s)?:\/\/)([^\s<>\"\']+?)(?=\s|$|<)/i', '<a href="$1$3" >$3</a> ', $text);

		$return_text = strip_tags( $text, ( $feature ? ( $this->valid_tags[$feature] ? $this->valid_tags[$feature] : "" ) : "" ) );

		$addTextFilter = new InputFilter($this->valid_tags_array[$feature], $this->valid_attrs);

		$return_text = $addTextFilter->process($return_text);

		foreach ( $this->pair_valid_tags as $value )
		{
			$count = substr_count( $return_text, "<".$value.">" );
            $count2 = substr_count( $return_text, "</".$value.">" );

			if( $count === 0 ) continue;

			$return_text .= str_repeat( '</'.$value.'>', ($count2-$count) );
		}

		return $nl2br ? nl2br($return_text) : $return_text;
	}


	/*--------------- Staic Interface ------------------*/


	/**
	 * Replaces badwords with #censored# string
	 *
	 * @param string $text
	 * @return string
	 */
	public static function stCensor( $text, $feature, $title = false )
	{
		$service = self::newInstance();

		return $service->censor( $text, $feature, $title );
	}


	/**
	 * Removes badwords from provided array
	 *
	 * @param array $tags
	 * @return array
	 */
	public static function stCensorArray( array $array )
	{
		$service = self::newInstance();

		return $service->censorArray( $array );
	}


	/**
	 * Enter description here...
	 *
	 * @param unknown_type $text
	 * @param unknown_type $feature
	 * @return unknown
	 */
	public static function stOutputFormatter( $text, $feature = false, $nl2br = true )
	{
		$service = self::newInstance();

		return $service->outputFormatter( $text, $feature, $nl2br );
	}

	/**
	 * Replaces smiles codes with image url string
	 *
	 * @param $string string
	 * @return string
	 */
	public static function stHandleSmiles( $string )
	{
		$service = self::newInstance();

		return $service->handleSmiles( $string );
	}

	public static function stGetSmiles()
	{
		$service = self::newInstance();

		return $service->getSmiles();
	}

}