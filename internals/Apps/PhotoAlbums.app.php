<?php

class app_PhotoAlbums
{
	public static function isFeatureActive() {
		return app_Features::isAvailable(39);
	}
	
	/**
	 * @return dto_PhotoAlbum
	 */
	public static function getAlbums($profile_id = null, $limit = null, $has_photo = false) {
		if ( !($profile_id = intval($profile_id)) ) {
			$profile_id = SK_HttpUser::profile_id();
		}
		
		$limit_str = '';
		if (isset($limit)) {
			$limit_str = 'LIMIT ' . intval($limit['offset']) . ',' . intval($limit['count']);
		}
		
		$query = SK_MySQL::placeholder("
			SELECT `albums`.* FROM `" . TBL_PHOTO_ALBUMS . "` AS `albums`
			".($has_photo 
				? 'INNER JOIN `' . TBL_PHOTO_ALBUM_ITEMS . '` AS `items` ON `albums`.`id` = `items`.`album_id`'  
				: ''
			)."
			WHERE `profile_id`=? 
			GROUP BY `albums`.`id`
			ORDER BY `create_stamp` DESC
			$limit_str
		", $profile_id);
		
		$result = SK_MySQL::query($query);
		$out = array();
		while ($item = $result->fetch_assoc()) {
			$out[$item['id']] = self::createAlbum($item);
		}
		return $out;
	}
	
	public static function IsFull($album_id = null)
	{
		$config = SK_Config::section('photo')->Section('general');
		$max_count = intval($album_id) ? $config->max_photos_in_album : $config->max_count;
		
		app_ProfilePhoto::setProcessAlbum($album_id);
		$count = app_ProfilePhoto::getPhotoCount();
		app_ProfilePhoto::unsetProcessAlbum();
		
		return $count >= $max_count;
	}
	
	public static function getPhotoAlbum($photo_id)
    {
		if ( !($photo_id = intval($photo_id)) ) {
			return null;
		}
		
		$query = "SELECT `album_id` FROM `" . TBL_PHOTO_ALBUM_ITEMS . "`
			WHERE `photo_id`=$photo_id";
		return (int) SK_MySQL::query($query)->fetch_cell();		
	}
	
	public static function getAlbumMinNumber($album_id = null) {
		app_ProfilePhoto::setProcessAlbum($album_id);
		$uploaded = app_ProfilePhoto::getUploadedPhotos(); 
		app_ProfilePhoto::unsetProcessAlbum();
		$config = SK_Config::section('photo')->Section('general');
		$max_count = intval($album_id) ? $config->max_photos_in_album : $config->max_count;
		 
		for($i=1; $i <= $max_count; $i++) {
			if (!isset($uploaded[$i])) {
				return $i;
			}
		}
		return 1;
	}
	
	public static function moveTo($photo_id, $album_id=null) {
		if (
			!($photo_id = intval($photo_id)) ||
			!($profile_id = SK_HttpUser::profile_id())
		) {
			return false;
		}
		
		$number = self::getAlbumMinNumber($album_id);
		
		$query = "UPDATE `" . TBL_PROFILE_PHOTO . "`
			SET `number`=$number
			WHERE `photo_id`=$photo_id
		";
		SK_MySQL::query($query);
		
		$query = "DELETE FROM `" . TBL_PHOTO_ALBUM_ITEMS . "` 
			WHERE `photo_id`=$photo_id";
		SK_MySQL::query($query);
		
		if ($album_id = intval($album_id)) 
		{
			$query = "INSERT INTO `" . TBL_PHOTO_ALBUM_ITEMS . "` SET
				`photo_id`=$photo_id,
				`album_id`=$album_id,
				`add_stamp`=" . time()
			;
			
			SK_MySQL::query($query);
		}
		return (bool) SK_MySQL::affected_rows();
	}
	
	public static function getAlbumsCount($profile_id = null, $has_photo = false) {
		if ( !($profile_id = intval($profile_id)) ) {
			$profile_id = SK_HttpUser::profile_id();
		}
		
		$query = SK_MySQL::placeholder("
			SELECT COUNT(`albums`.`id`) FROM `" . TBL_PHOTO_ALBUMS . "` AS `albums`
			".($has_photo 
				? 'INNER JOIN `' . TBL_PHOTO_ALBUM_ITEMS . '` AS `items` ON `albums`.`id` = `items`.`album_id`'  
				: ''
			)."
			WHERE `profile_id`=?
		", $profile_id);
		
		return (int) SK_MySQL::query($query)->fetch_cell();
	}
	
	public static function getDefaultAlbumThumb($mini = false) 
	{
		$size = $mini ? '_mini' : '';
		
		return URL_LAYOUT . 'img/empty_photo_album' . $size . '.png';
	}
	
	public static function getPrivateAlbumThumb( $privacy, $mini = false )
	{
	    $privacy = trim($privacy);
	    $size = $mini ? '_mini' : '';
        
        return URL_LAYOUT . 'img/' . $privacy . $size . '.png';
	}
	
	public static function createAlbum(array $state = array()) {
		return new dto_PhotoAlbum($state);
	}
	
	/**
	 *Returns album object
	 * @param int $album_id
	 * @return dto_PhotoAlbum
	 */
	public static function getAlbum($album_id) {
		if ( !($album_id = intval($album_id)) ) {
			throw new app_PhotoAlbumSystem_Exception('Undefined album id');
		}
		
		$query = SK_MySQL::placeholder("
			SELECT * FROM `" . TBL_PHOTO_ALBUMS . "`
			WHERE `id`=? 
		", $album_id);
		
		$info = SK_MySQL::query($query)->fetch_assoc();
		return self::createAlbum($info);
	}
	
	public static function SaveAlbum(dto_PhotoAlbum $album) {
		$props = array();
		
		$props['id'] = $album->getId();
		$props['label'] = SK_MySQL::realEscapeString($album->getLabel());
		$props['privacy'] = $album->getPrivacy();
		$props['password'] = SK_MySQL::realEscapeString($album->getPassword());
		$props['profile_id'] = ($pr_id = $album->getProfile_id()) ? $pr_id : SK_HttpUser::profile_id(); 
		$props['create_stamp'] = ($cr_stamp = $album->getCreate_stamp()) ? $cr_stamp : time();
		$set_str = '';
		
		if ( !($props['label'] = trim($props['label'])) ) {
			 throw new app_PhotoAlbumCreation_Exception('emty_label');
		}
		
		foreach ($props as $item => $value) {
			if (isset($value)) {
				$set_str .= ", $item='$value'";
			}
		}
		$set_str = substr($set_str, 2);
		$query = "
			INSERT INTO `" . TBL_PHOTO_ALBUMS . "`
			SET $set_str
			ON DUPLICATE KEY 
			UPDATE $set_str 
		";

		SK_MySQL::query($query);
	}
	
	public static function getAlbumPhotoIds($album_id) {
		if (!($album_id = intval($album_id))) {
			return false;
		}
		
		$query = "SELECT `photo_id` FROM `" . TBL_PHOTO_ALBUM_ITEMS . "`
			WHERE `album_id`=$album_id";
		$result = SK_MySQL::query($query);
		$out = array();
		while ($item = $result->fetch_cell()) {
			$out[] = $item;
		}
		return $out;
	}
	
	public static function deleteAlbum($album_id) {
		if (
			!($album_id = intval($album_id)) ||
			!($profile_id = SK_HttpUser::profile_id())
		) {
			return false;
		}
		
		$photos = self::getAlbumPhotoIds($album_id);
		foreach ($photos as $photo) {
			app_ProfilePhoto::delete($photo);
		}
		$query = "DELETE FROM `" . TBL_PHOTO_ALBUMS . "`
			WHERE `id`=$album_id";
		SK_MySQL::query($query);
		return (bool) SK_MySQL::affected_rows();
	}
	
	public static function storeAlbumPassword( $albumId, $password )
	{
	    SK_HttpUser::set_cookie('photo_album_password_' . $albumId, $password);
	}
	
    public static function getStoredAlbumPassword( $albumId )
    {
        return empty($_COOKIE['photo_album_password_' . $albumId]) ? null : $_COOKIE['photo_album_password_' . $albumId];
    }
}



class dto_PhotoAlbum
{
	/**
	 * @var string
	 */
	private $label;
	
	/**
	 * @var int
	 */
	private $id;
	
	/**
	 * @var int
	 */
	private $create_stamp;
	
	/**
	 * @var int
	 */
	private $profile_id;
	
	private $privacy;
	private $password;
	
	private $unlocked;
	
	private $first_photo_id;
	
	public function __construct(array $state) {
		if (isset($state['id'])) {
			$this->setId($state['id']);
		}
		if (isset($state['label'])) {
			$this->setLabel($state['label']);
		}
		if (isset($state['create_stamp'])) {
			$this->setCreate_stamp($state['create_stamp']);
		}
		if (isset($state['profile_id'])) {
			$this->setProfile_id($state['profile_id']);
		}
		
        if (isset($state['privacy'])) {
            $this->setPrivacy($state['privacy']);
        }
        
        if (isset($state['password'])) {
            $this->setPassword($state['password']);
        }
	}
	
	
	public function getFirst_photo_id($avaliable_statuses = array()) {
		if (isset($this->first_photo_id)) {
			return (int) $this->first_photo_id;
		}
		$add_sql = '';
		if ( !in_array('password_protected', $avaliable_statuses) && !empty($_SESSION["%unlocked_photos%"]) )
		{
			$add_sql = 'OR ( `pp`.`publishing_status`="password_protected" AND `pp`.`photo_id` IN (' . implode(',', $_SESSION["%unlocked_photos%"]) . ') )';
		}
		
		$status_sql = !empty($avaliable_statuses) ? '`pp`.`publishing_status` IN(' . implode(',', array_map('json_encode', $avaliable_statuses)) . ')' : 1;
		
		$query = "
			SELECT `pp`.`photo_id` FROM `" . TBL_PHOTO_ALBUM_ITEMS . "` AS `pai`
			INNER JOIN `" . TBL_PROFILE_PHOTO . "` AS `pp` USING(`photo_id`)
			WHERE `pai`.`album_id`={$this->getId()} AND ( $status_sql $add_sql )
			ORDER BY `pai`.`add_stamp` 
			LIMIT 1
		";
		
		return (int)SK_MySQL::query($query)->fetch_cell();
	}
	
	public function getFirst_photo_url($photo_type = app_ProfilePhoto::PHOTOTYPE_THUMB) {
		$perm = $this->getPhotoPermission();
		if ( !($url = app_ProfilePhoto::getUrl($this->getFirst_photo_id($perm), $photo_type)) ) {
			return null;
		}
		return $url;
	}
	
	public function getPhotoPermission()
	{
		if ( !($viewer_id = SK_HttpUser::profile_id()) )
		{
			return array('public');
		}
		
		if ($this->profile_id == $viewer_id)
		{
			return array('public', 'password_protected', 'friends_only');
		}
        
		$perm = array('public');
		if ( app_FriendNetwork::isProfileFriend($this->profile_id, $viewer_id) )
		{
			$perm[] = 'friends_only';
		}
		return $perm;
	}
	
	public function enterPassword( $password )
	{
	    if ( $this->getPassword() != $password )
	    {
	        return false;
	    }
	    
	    $this->unloked = true;
	    
	    app_PhotoAlbums::storeAlbumPassword($this->id, $password);
	    
	    return true;
	}
	
	public function isAccessable( $profileId )
	{
	   switch ($this->getPrivacy())
	   {
	       case 'public':
	           return true;
	           
	       case 'friends_only':
	           return app_FriendNetwork::isProfileFriend($this->profile_id, $profileId);

	       case 'password_protected':
	           if ( !$this->unlocked )
	           {
	               $this->unlocked = app_PhotoAlbums::getStoredAlbumPassword($this->id) == $this->getPassword();
	           }
	           
	           return $this->unlocked;
	   }
	   
	}
	
	public function getThumb_url($type = 'normal') 
	{
		if ( !$this->isAccessable(SK_HttpUser::profile_id()) )
		{
		    return app_PhotoAlbums::getPrivateAlbumThumb($this->getPrivacy(), $type == 'mini');
		}		
	    
		if ( $url = $this->getFirst_photo_url() ) {
			return $url;
		}
		
		return $this->getDefault_thumb_url($type);
	}
	
	public function getDefault_thumb_url($type = 'normal') {
		return app_PhotoAlbums::getDefaultAlbumThumb($type == 'mini');
	}
	
	/**
	 * @return int
	 */
	public function getCreate_stamp () { 
		return $this->create_stamp; 
	}

	/**
	 * @return int
	 */
	public function getProfile_id () {
		return $this->profile_id; 
	}

	/**
	 * @param int $create_stamp
	 */
	private function setCreate_stamp ($create_stamp) { 
		$this->create_stamp = intval($create_stamp); 
	}

	/**
	 * @param int $profile_id
	 */
	private function setProfile_id ($profile_id) { 
		$this->profile_id = intval($profile_id); 
	}

	/**
	 * @return int
	 */
	public function getId () {
		return $this->id; 
	}

	/**
	 * @return string
	 */
	public function getLabel () { 
		return $this->label; 
	}

	/**
	 * @param int $id
	 */
	private function setId ($id) { 
		$this->id = intval($id); 
	}

	/**
	 * @param string $label
	 */
	public function setLabel ($label) { 
		$this->label = trim($label); 
	}

	public function getView_label() {
		return SK_Language::htmlspecialchars($this->getLabel());
	}
	
    public function setPrivacy ($privacy) { 
        $this->privacy = trim($privacy); 
    }
    
    public function getPrivacy () { 
        return empty($this->privacy) ? 'public' : $this->privacy; 
    }
    
    public function setPassword ($password) { 
        $this->password = trim($password); 
    }
    
    public function getPassword () { 
        return $this->password; 
    }
	
	public function getUrl($for_edit = true) {
		if ($for_edit && $this->getProfile_id() == SK_HttpUser::profile_id()) {
			return SK_Navigation::href('profile_photo_upload', array(
				'album'	=> $this->getId(),
				'tab'	=> 'albums'
			));
		}
		
		return SK_Navigation::href('profile_photo', array('album'=>$this->getId()));
	}
}

class app_PhotoAlbumCreation_Exception extends Exception {}

class app_PhotoAlbumSystem_Exception extends Exception {}