<?php
require_once DIR_APPS.'appAux/ComponentDao.php';
require_once DIR_APPS.'appAux/ProfileComponentDao.php';
require_once DIR_APPS.'appAux/CustomHtmlDao.php';

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Dec 10, 2008
 * 
 */
final class app_ProfileComponentService
{
	/**
	 * @var array
	 */
	private $configs;
	
	/**
	 * @var ComponentDao
	 */
	private $component_dao;
	
	/**
	 * @var ProfileComponentDao
	 */
	private $profile_cmp_dao;
	
	/**
	 * @var CustomHtmlDao
	 */
	private $customHtmlDao;
	
	/**
	 * @var app_ProfileComponentService
	 */
	private static $classInstance;
	
	/**
	 * Class constructor
	 */
	private function __construct ()
	{
		$this->component_dao = ComponentDao::newInstance();
		$this->profile_cmp_dao = ProfileComponentDao::newInstance();
		$this->customHtmlDao = new CustomHtmlDao();
	}
	
	/**
	 * Returns the only instance of the class
	 *
	 * @return app_ProfileComponentService
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
	 * Returns components not in profile view list
	 *
	 * @param integer $profile_id
	 * @return array
	 */
	public function findNotInViewListCMP( $profile_id )
	{
		return $this->component_dao->findNotInViewListCMP( $profile_id );
	}
	
	/**
	 * Returns profile view CMPs
	 *
	 * @param integer $profile_id
	 * @return array
	 */
	public function findProfileViewCMP( $profile_id )
	{
		return $this->profile_cmp_dao->findProfileViewCMP( $profile_id );
	}
	
	
	/**
	 * Saves and Updates ProfileComponent items
	 *
	 * @param ProfileComponent $pc
	 */
	public function saveOrUpdateProfileComponent( ProfileComponent $pc )
	{
		$this->profile_cmp_dao->saveOrUpdate( $pc );
	}
	
	/**
	 * Finds ProfileComponent by id
	 *
	 * @param integer $id
	 * @return ProfileComponent
	 */
	public function findProfileComponentById( $id )
	{
		return $this->profile_cmp_dao->findById( $id );
	}
	
	/**
	 * @param integer $id
	 * @return Component
	 */
	public function findComponentById( $id )
	{
		return $this->component_dao->findById( $id );	
	}
	
	/**
	 * Deletes ProfileComponent item by id
	 *
	 * @param integer $id
	 */
	public function deleteById( $id )
	{
		$this->profile_cmp_dao->deleteById( $id );
	}
	
	
	/**
	 * Increments position index for one or severral cmps
	 *
	 * @param integer $profile_id
	 * @param array $array
	 */
	public function incrementPosition( $profile_id, $array )
	{
		$this->profile_cmp_dao->incrementPosition( $profile_id, $array );
	}
	
	
	/**
	 * Decrements position index for one or severral cmps
	 *
	 * @param integer $profile_id
	 * @param array $array
	 */
	public function decrementPosition( $profile_id, $array )
	{
		$this->profile_cmp_dao->decrementPosition( $profile_id, $array );
	}
	
	/**
	 * Inserts cmp in section with first position 
	 *
	 * @param integer $cmp_id
	 * @param integer $profile_id
	 * @param integet $section
	 */
	public function insertFirstInSection( $cmp_id, $profile_id, $section )
	{
		$cmp_entry = $this->profile_cmp_dao->findProfileCMPByProfileIdAndCMPId( $profile_id, $cmp_id );

		$cmp = $this->findComponentById($cmp_id);

        if( $cmp->getClass_name() == 'CustomHtml' )
        {
            $service_to_track = new SK_Service( 'custom_html', $profile_id );
            $service_to_track->checkPermissions();
            $service_to_track->trackServiceUse();
        }

		if( $cmp_entry !== null && $cmp->getMultiple() == 0 )
			return;
		
		$cmps_in_section = $this->profile_cmp_dao->findSectionCmps( $profile_id, $section );
		
		if( !empty( $cmps_in_section ) )
		{
			$incrIds = array();
			
			foreach ( $cmps_in_section as $value )
			{
				$incrIds[] = $value->getId();
			}
			
			$this->incrementPosition( $profile_id, $incrIds );
		}
		
		$pc = new ProfileComponent( $cmp_id, $profile_id, $section, 1 );
		$this->saveOrUpdateProfileComponent( $pc );	
	}
	
	
	/**
	 * Adds default cmps to new profiles
	 *
	 * @param integer $profile_id
	 */
	public function addDefaultCmps( $profile_id )
	{
		$cmp_ids = array(
            array( 'id' => 9, 'section' => 1, 'position' => 1), 
			array( 'id' => 1, 'section' => 1, 'position' => 2),
			array( 'id' => 4, 'section' => 1, 'position' => 3),
			array( 'id' => 2, 'section' => 2, 'position' => 1),
			array( 'id' => 3, 'section' => 2, 'position' => 2),
			array( 'id' => 5, 'section' => 1, 'position' => 4),
			array( 'id' => 13, 'section' => 1, 'position' => 5),
            array( 'id' => 18, 'section' => 1, 'position' => 1)
		);
		
		foreach ( $cmp_ids as $value )
			$this->profile_cmp_dao->saveOrUpdate( new ProfileComponent( $value['id'], $profile_id, $value['section'], $value['position'] ) );
	}
	
	
	/**
	 * Returns info on rendering component or not
	 *
	 * @param string $cmpClass
	 * @param integer $profile_id
	 * @return boolean
	 */
	public function renderCmp( $cmpClass, $profile_id )
	{
		switch ( $cmpClass )
		{
			case 'ProfileVideoAlbum':
				if( app_ProfileVideo::getProfileVideoCount($profile_id, 'active') > 0 )return true;
				break;
                        case 'ProfileMusicAlbum':
				if( app_ProfileMusic::getProfileMusicCount($profile_id, 'active') > 0 )return true;
	                        break;
				
			case 'BlogProfilePageList':
				if( app_BlogService::stFindLastProfileBlogPostsCount( $profile_id ) > 0 ) return true;
				break;
				
			case 'ForumProfilePosts':
				if( app_Forum::getProfilePostCount( $profile_id ) > 0 )return true;
				break;
				
			case 'ProfileEventList':
				if( app_EventService::stFindProfileEventsCount( $profile_id ) > 0 )return true;
				break;
				
			case 'SimpleFriendList':
				if( app_FriendNetwork::countFriends( $profile_id ) > 0 )return true;
				break;
				
			case 'ChuppoPlayer':
				if( SK_Config::section('chuppo')->get('enable_chuppo_recorder') ) return true;	
				break;
				
			default:
				return true;
		}
		
		return false;
	}
	
	
	/**
	 * Deletes profile view page info
	 *
	 * @param integer $profile_id
	 */
	public function deleteProfileViewInfo( $profile_id )
	{
		$this->profile_cmp_dao->deleteProfileEntries( $profile_id );
	}
	
	/**
	 * @param integer $cmp_id
	 * @return CustomHtml
	 */
	public function getCustomHtmlForCmp( $cmp_id )
	{
		return $this->customHtmlDao->findByCmpId($cmp_id);
	}
	
	public function saveCustomHtml( CustomHtml $ch )
	{
		$this->customHtmlDao->saveOrUpdate($ch);
	}
	
	/* Hardcode methods for bg customization */
	
	public function getBgInfo( $profile_id )
	{
		$query = SK_MySQL::placeholder("SELECT `bg_color`, `bg_image`, `bg_image_url`, `bg_image_mode` FROM `".TBL_PROFILE."` WHERE `profile_id`=?", $profile_id);
		
		return SK_MySQL::query($query)->fetch_assoc();
	}
	
	public function saveBgColor( $profile_id, $color )
	{
		$query = SK_MySQL::placeholder("UPDATE `".TBL_PROFILE."` SET `bg_color`='?' WHERE `profile_id`=?", $color, $profile_id);
		
		SK_MySQL::query($query);
	}
	
	public function saveBgImageUrl( $profile_id, $bg_image_url )
	{
		$query = SK_MySQL::placeholder("UPDATE `".TBL_PROFILE."` SET `bg_image_url`='?'  WHERE `profile_id`=?", $bg_image_url, $profile_id);
		
		SK_MySQL::query($query);
	}
	
	public function saveBgImage( $profile_id, $bg_image, $bg_image_url )
	{
		$query = SK_MySQL::placeholder("UPDATE `".TBL_PROFILE."` SET `bg_image`='?', `bg_image_url`='?'  WHERE `profile_id`=?", $bg_image, $bg_image_url, $profile_id);
		
		SK_MySQL::query($query);
	}
	
	public function saveBgImageMode( $profile_id, $mode )
	{
		if( $mode === null )
			$query = SK_MySQL::placeholder( "UPDATE `".TBL_PROFILE."` SET `bg_image_mode` = NULL  WHERE `profile_id`=?", $profile_id );
		else 
			$query = SK_MySQL::placeholder( "UPDATE `".TBL_PROFILE."` SET `bg_image_mode`=?  WHERE `profile_id`=?", $mode, $profile_id );
		
		SK_MySQL::query($query);
	}
	
	public function deleteBgImage( $profile_id )
	{
		$info = $this->getBgInfo($profile_id); 
		
		if( file_exists( DIR_USERFILES.$info['bg_image'] ) )
			unlink( DIR_USERFILES.$info['bg_image'] );
		
		$query = SK_MySQL::placeholder( "UPDATE `".TBL_PROFILE."` SET `bg_image` = NULL, `bg_image_status`='active' 
			WHERE `profile_id` = ?", $profile_id );
		SK_MySQL::query($query);
	}
	
	public function setBGImageStatus( $profile_id, $status = 'approval' )
	{
		$query = SK_MySQL::placeholder( "UPDATE `".TBL_PROFILE."` SET `bg_image_status`='?' WHERE `profile_id`=?", $status, $profile_id );
		
		SK_MySQL::query( $query );
	}
	
	
	public function getBGImageStatus ( $profile_id )
	{
		$query = SK_MySQL::placeholder( "SELECT `bg_image_status` FROM `".TBL_PROFILE."` WHERE `profile_id`=?", $profile_id );
		
		return SK_MySQL::query( $query )->fetch_cell();
	}
	/*--------------------------- static int -----------------------*/
	
	
	/**
	 * Adds default set of cmps for profile view page
	 *
	 * @param integer $profile_id
	 */
	public static function stAddDefaultCmps( $profile_id )
	{
		$service = self::newInstance();
		
		$service->addDefaultCmps( $profile_id );
	}
	
	
	/**
	 * Deletes profile view page info
	 *
	 * @param integer $profile_id
	 */
	public static function stDeleteProfileViewInfo( $profile_id )
	{
		$service = self::newInstance();
		
		$service->deleteProfileViewInfo( $profile_id );
	}
	
//	public function findCmpsToUpdate( $profile_id, $section, $position_start, $position_end )
//	{
//		return $this->profile_cmp_dao->findCmpsToUpdate( $profile_id, $section, $position_start, $position_end );
//	}
//	
//	public function findCmpsForAnotherSection( $profile_id, $section, $position, $id )
//	{
//		return $this->profile_cmp_dao->findCmpsForAnotherSection( $profile_id, $section, $position, $id );
//	}
//	
//	public function findCmpsForOldSection( $profile_id, $section, $position )
//	{
//		return $this->profile_cmp_dao->findCmpsForOldSection( $profile_id, $section, $position ); 
//	}
}