<?php
require_once DIR_APPS.'appAux/BlogPost.php';

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Oct 15, 2008
 * 
 * Desc: data access object Class for blog_post Table
 */

final class BlogDao extends SK_BaseDao
{
	//private static $classInstance;
	
	/**
	 * Class constructor
	 *
	 */	
	public function __construct (){}
	
	protected function getDtoClassName (){return "BlogPost";}
	
	protected function getTableName (){return '`'.TBL_BLOG_POST.'`';}
	
	/**
	 * Returns the only object of BlogDao class
	 *
	 * @return BlogDao
	 */
//	public static function newInstance ()
//	{
//		if ( self::$classInstance === null )
//			self::$classInstance = new self;
//			
//		return self::$classInstance;
//	}
//	
	/* !!! AUXILARY PART DEVIDER !!! */
	
	/**
	 * Gets last common Blog Posts marked as Main ( !WARN Partial DTO )
	 *
	 * @param integer $count
	 * 
	 * @return array
	 */
	public function findLastCommonMainBlogPosts( $count )
	{
		$query = SK_MySQL::placeholder("SELECT ". $this->getTotalReducedFieldList()." FROM ". $this->getTableName(). "
			WHERE `profile_status` = 1 AND `admin_status` = 1 AND `is_news` = 0  ORDER BY `create_time_stamp` DESC LIMIT ?", $count);

		return SK_MySQL::query($query)->mapObjectArray($this->getDtoClassName());
	}
	
	
	/**
	 * Gets last user Blog Posts ( !WARN Partial DTO )
	 *
	 * @param integer $profile_id
	 * @param integer $count
	 * 
	 * @return array
	 */
	public function findLastUserBlogPosts( $profile_id, $count )
	{
		$query = SK_MySQL::placeholder("SELECT ". $this->getTotalReducedFieldList()."
			FROM ". $this->getTableName(). " WHERE `profile_id` = ? AND `profile_status` = 1 AND `admin_status` = 1 AND `is_news` = 0 ORDER BY `create_time_stamp` DESC LIMIT ?", $profile_id, $count);
		
		return SK_MySQL::query($query)->mapObjectArray($this->getDtoClassName());
	}
	
	
/**
	 * Gets last user Blog Posts count
	 *
	 * @param integer $profile_id
	 * @return integer
	 */
	public function findLastUserBlogPostsCount( $profile_id )
	{
		$query = SK_MySQL::placeholder("SELECT COUNT(*) as `items_count`
			FROM ". $this->getTableName(). " WHERE `profile_id` = ? AND `profile_status` = 1 AND `admin_status` = 1 AND `is_news` = 0", $profile_id);
		
		return (int)SK_MySQL::query($query)->fetch_cell();
	}
	
	
	/**
	 * Gets user Blog Posts ( !WARN Partial DTO )
	 *
	 * @param integer $profile_id
	 * @param integer $first
	 * @param integer $count
	 * @param boolean $viewMode
	 * @param string $order
	 * 
	 * @return array
	 */
	public function findUserBlogPosts( $profile_id, $first, $count, $view_mode = 'user', $order = 'date' )
	{	
		$query = SK_MySQL::placeholder("SELECT ". $this->getReducedFieldList()." 
			FROM ". $this->getTableName(). " WHERE `profile_id` = ?". $this->getViewModeQueryIns($view_mode). $this->getOrderQueryIns($order). " LIMIT ?, ?", $profile_id, $first, $count);

		return SK_MySQL::query($query)->mapObjectArray($this->getDtoClassName());
	}
	
	
	/**
	 * Gets user Blog Posts Count
	 *
	 * @param integer $profile_id
	 * @param string $viewMode
	 * 
	 * @return integer
	 */
	public function findUserBlogPostsCount( $profile_id, $viewMode = 'user' )
	{
		$query = SK_MySQL::placeholder("SELECT COUNT(*) AS `items_count` FROM ". $this->getTableName(). " WHERE `profile_id` = ?". $this->getViewModeQueryIns($viewMode), $profile_id);
		
		return (int)SK_MySQL::query($query)->fetch_cell();
	}
	
	/**
	 * Gets user Blog Posts ( !WARN Partial DTO )
	 *
	 * @param integer $profile_id
	 * @param integer $first
	 * @param integer $count
	 * @param boolean $viewMode
	 * @param string $order
	 * 
	 * @return array
	 */
	public function findUserBlogPostsWithCC( $profile_id, $first, $count, $view_mode = 'user', $order = 'date' )
	{	
		$query = SK_MySQL::placeholder("SELECT ". $this->getReducedFieldList(true).", IF ( `c`.`items_count` is null, 0, `c`.`items_count` ) AS `items_count`
			FROM ". $this->getTableName(). " AS `b` 
			LEFT JOIN ( SELECT COUNT(*) AS `items_count`, `entity_id` FROM `".TBL_BLOG_POST_COMMENT."` GROUP BY `entity_id` ) AS `c` ON (`b`.`id`=`c`.`entity_id`)
			WHERE `profile_id` = ?". $this->getViewModeQueryIns( $view_mode, true ). $this->getOrderQueryIns( $order, true ). " LIMIT ?, ?", $profile_id, $first, $count);

		return SK_MySQL::query($query)->mapObjectArray($this->getDtoClassName());
	}
	
	
	/**
	 * Gets common Blog Posts
	 *
	 * @param integer $first
	 * @param integer $count
	 * @param string $order
	 * 
	 * @return array
	 */
	public function findCommonBlogPosts( $first, $count, $order = 'date' )
	{
		$query = SK_MySQL::placeholder("SELECT ". $this->getReducedFieldList(true).",`p`.`username`
			FROM ". $this->getTableName(). " AS `b` LEFT JOIN `". TBL_PROFILE."` AS `p` ON (`b`.`profile_id` = `p`.`profile_id`) 
			WHERE `b`.`profile_status` = 1 AND `b`.`admin_status` = 1 AND `b`.`is_news` = 0". $this->getOrderQueryIns($order, true)." LIMIT ?, ?", $first, $count);

		return SK_MySQL::query($query)->mapObjectArray($this->getDtoClassName());
	}
	
	
	/**
	 * Gets common Blog Posts count
	 *
	 * @return integer
	 */
	public function findCommonBlogPostsCount( $news = 0 )
	{
		$query = SK_MySQL::placeholder("SELECT COUNT(*) AS `items_count` FROM ". $this->getTableName(). " WHERE `profile_status` = 1 AND `admin_status` = 1 AND `is_news` = ?", $news);

		return (int)SK_MySQL::query($query)->fetch_cell();
	}
	
	
	/**
	 * Gets common Blog Posts by tag_id
	 *
	 * @param integer $tag_id
	 * @param integer $first
	 * @param integer $count
	 * @param integer $order
	 */
	public function findBlogPostsByTagId( $tag_id, $first, $count, $order = 'date', $is_news = 0 )
	{
		$query = SK_MySQL::placeholder("SELECT ". $this->getReducedFieldList(true).",`p`.`username`
			FROM ". $this->getTableName(). " AS `b` 
			LEFT JOIN `". TBL_PROFILE."` AS `p` ON (`b`.`profile_id` = `p`.`profile_id`)
			LEFT JOIN `". TBL_BLOG_POST_TAG."` AS `t` ON (`b`.`id` = `t`.`entity_id`) 
			WHERE `b`.`profile_status` = 1 AND `b`.`admin_status` = 1 AND `b`.`is_news` = ? AND `t`.`tag_id` = ? ". $this->getOrderQueryIns($order, true)." LIMIT ?, ?", $is_news, $tag_id, $first, $count);

		return SK_MySQL::query($query)->mapObjectArray($this->getDtoClassName());
	}
	
	
	/**
	 * Gets common Blog Posts count by tag_id
	 *
	 * @param integer $tag_id
	 * @return integer
	 */
	public function findBlogPostsByTagIdCount( $tag_id, $is_news = 0 )
	{
		$query = SK_MySQL::placeholder("SELECT COUNT(*) AS `items_count` FROM ". $this->getTableName(). " AS `b` 
			LEFT JOIN `". TBL_BLOG_POST_TAG."` AS `t` ON (`b`.`id` = `t`.`entity_id`) 
			WHERE `b`.`profile_status` = 1 AND `b`.`admin_status` = 1 AND `b`.`is_news` = ? AND `t`.`tag_id` = ?", $is_news, $tag_id);

		return (int)SK_MySQL::query($query)->fetch_cell();
	}
	
	/**
	 * Returns profile blogs count
	 *
	 * @return integer
	 */
	public function findBlogsCount()
	{
		$query = SK_MySQL::placeholder("SELECT COUNT(*) as `items_count` FROM 
			( SELECT * FROM ". $this->getTableName(). " WHERE `profile_status` = 1 AND `admin_status` = 1 AND `is_news` = 0 GROUP BY `profile_id` ) AS `b`");
		
		return (int)SK_MySQL::query($query)->fetch_cell();
	}
	
	
	/**
	 * Returns next profile post
	 *
	 * @param integer $profile_id
	 * @param integer $date
	 * @return BlogPost|null
	 */
	public function findNextPost( $profile_id, $date )
	{
		$query = SK_MySQL::placeholder( "SELECT * FROM ". $this->getTableName(). " 
			WHERE `profile_id` = ? AND `create_time_stamp` > ? AND `profile_status` = 1 AND `admin_status` = 1 AND `is_news` = 0 ORDER BY `create_time_stamp` ASC LIMIT 1", $profile_id, $date );
		
		return SK_MySQL::query( $query )->mapObject( $this->getDtoClassName() );
	}
	
	
	/**
	 * Returns previous profile post
	 *
	 * @param integer $profile_id
	 * @param integer $date
	 * @return BlogPost|null
	 */
	public function findPrevPost( $profile_id, $date )
	{
		$query = SK_MySQL::placeholder( "SELECT * FROM ". $this->getTableName(). " 
			WHERE `profile_id` = ? AND `create_time_stamp` < ? AND `profile_status` = 1 AND `admin_status` = 1 AND `is_news` = 0 ORDER BY `create_time_stamp` DESC LIMIT 1", $profile_id, $date );
		
		return SK_MySQL::query( $query )->mapObject( $this->getDtoClassName() );
	}
	
	
	/**
	 * Finds all profile posts
	 *
	 * @param integer $profile_id
	 * @return array
	 */
	public function findAllProfilePosts( $profile_id )
	{
		$query = SK_MySQL::placeholder( "SELECT * FROM ". $this->getTableName(). " WHERE `profile_id` = ?", $profile_id );

		return SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName() );
	}

	
	/* news temp methods */
	public function findLastNews( $count )
	{
		$query = SK_MySQL::placeholder( "SELECT * FROM ". $this->getTableName(). "
		WHERE `admin_status` = 1 AND `profile_status` = 1 AND `is_news` = 1 ORDER BY `create_time_stamp` DESC LIMIT ?", $count );
		
		return SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName());
	}
	
	public function findNews( $first, $count )
	{
		$query = SK_MySQL::placeholder("SELECT ". $this->getReducedFieldList(true).",`p`.`username`
			FROM ". $this->getTableName(). " AS `b` LEFT JOIN `". TBL_PROFILE."` AS `p` ON (`b`.`profile_id` = `p`.`profile_id`) 
			WHERE `b`.`profile_status` = 1 AND `b`.`admin_status` = 1 AND `b`.`is_news` = 1". $this->getOrderQueryIns($order, true)." LIMIT ?, ?", $first, $count);

		return SK_MySQL::query($query)->mapObjectArray($this->getDtoClassName());
	}
	/* /// news temp methods */
	
	
	/**
	 * Gets news count
	 *
	 * @return integer
	 */
	public function findNewsCount()
	{
		$query = SK_MySQL::placeholder("SELECT COUNT(*) AS `items_count` FROM ". $this->getTableName(). " WHERE `profile_status` = 1 AND `admin_status` = 1 AND `is_news` = 1");

		return (int)SK_MySQL::query($query)->fetch_cell();
	}
	
	public function findPostsForModeration( $first, $count )
	{
		$query = SK_MySQL::placeholder("SELECT ". $this->getReducedFieldList(true).",`p`.`username`
			FROM ". $this->getTableName(). " AS `b` LEFT JOIN `". TBL_PROFILE."` AS `p` ON (`b`.`profile_id` = `p`.`profile_id`) 
			WHERE `b`.`profile_status` = 1 AND `b`.`admin_status` = 0". $this->getOrderQueryIns($order, true)." LIMIT ?, ?", $first, $count);

		return SK_MySQL::query($query)->mapObjectArray($this->getDtoClassName());
	}
	
	public function findModerationPostsCount()
	{
		$query = SK_MySQL::placeholder("SELECT COUNT(*) AS `items_count` FROM ". $this->getTableName(). " WHERE `profile_status` = 1 AND `admin_status` = 0");

		return (int)SK_MySQL::query($query)->fetch_cell();
	}
	
	public function findMostPopularPosts( $first, $count, $news = 0 )
	{
		$query = SK_MySQL::placeholder("
			SELECT * FROM ( 
				( SELECT `bp`.*, AVG(`br`.`score`) AS `score` FROM `".TBL_BLOG_POST_RATE."` AS `br`
					LEFT JOIN `". TBL_BLOG_POST ."` AS `bp` ON( `br`.`entity_id` = `bp`.`id` ) 
					WHERE `bp`.`admin_status` = 1 AND `bp`.`profile_status` = 1 AND `bp`.`is_news` = ?
					GROUP BY `br`.`entity_id`  
				)
				UNION
				( SELECT *, 0 AS `score` FROM ".$this->getTableName()." WHERE `id` NOT IN 
					( SELECT `entity_id` FROM `".TBL_BLOG_POST_RATE."` GROUP BY `entity_id` )
					AND `admin_status` = 1 AND `profile_status` = 1 AND `is_news` = ?
				)
			) AS `rt`
			ORDER BY `rt`.`score` DESC
			LIMIT ?, ?", $news, $news, $first, $count);
		
		return SK_MySQL::query($query)->mapObjectArray($this->getDtoClassName());
		
//		$temp_arr = MySQL::fetchArray($query);	 
//
//		$ins_arr = array();
//		
//		foreach( $temp_arr as $value )
//			$ins_arr[] = $value['id'];
//		
//		
//		$query = SK_MySQL::placeholder("SELECT ". $this->getReducedFieldList(true).", IF ( `c`.`items_count` is null, 0, `c`.`items_count` ) AS `items_count`
//			FROM ". $this->getTableName(). " AS `b` 
//			LEFT JOIN ( SELECT COUNT(*) AS `items_count`, `entity_id` FROM `".TBL_BLOG_POST_COMMENT."` GROUP BY `entity_id` ) AS `c` ON (`b`.`id`=`c`.`entity_id`)
//			WHERE `id` IN 
//			( ?@ )", $ins_arr );
//
//		return SK_MySQL::query($query)->mapObjectArray($this->getDtoClassName());
	}
	
//	public function findMostPopularPostsCount()
//	{
//		$query = SK_MySQL::placeholder("SELECT COUNT(*) AS `items_count` FROM ".TBL_BLOG_POST_RATE." AS `br`
//			LEFT JOIN ". TBL_BLOG_POST ." AS `bp` ON( `br`.`entity_id` = `bp`.`id` ) WHERE `bp`.`admin_status` = 1 AND `bp`.`profile_status` = 1
//			GROUP BY `br`.`entity_id`");
//		
//		return (int)SK_MySQL::query($query)->fetch_cell();
//	}
	
//	/**
//	 * Deletes all profile posts
//	 *
//	 * @param integer $profile_id
//	 */
//	public function deleteProfilePosts( $profile_id )
//	{
//		$query = SK_MySQL::placeholder( "DELETE FROM ". $this->getTableName(). " WHERE `profile_id` = ?", $profile_id );
//		
//		SK_MySQL::query( $query );
//	}
	
//	/**
//	 * Returns profile active blog posts count
//	 *
//	 * @return integer
//	 */
//	public function findPublicPostsCount()
//	{
//		$query = SK_MySQL::placeholder("SELECT COUNT(*) as `items_count` FROM ". $this->getTableName(). 
//			"WHERE `profile_status` = 1 AND `admin_status` = 1");
//		
//		return (int)SK_MySQL::query($query)->fetch_cell();
//	}
	
	/* private sec functions */
	
	private function getOrderQueryIns( $order, $alias = false )
	{
		switch ( $order )
		{
			case 'view_count':
				return $alias ? " ORDER BY `b`.`view_count`" : " ORDER BY `view_count`";
			
			default:
				return $alias ? " ORDER BY `b`.`create_time_stamp` DESC" : " ORDER BY `create_time_stamp` DESC";
		}
	}
	
	private function getViewModeQueryIns( $view_mode, $alias = false )
	{
		switch ( $view_mode )
		{
			case 'owner':
				return $alias ? " AND `b`.`profile_status` = 0" : " AND `profile_status` = 0";
				
			case 'admin':
				return  $alias ? " AND `b`.`profile_status` = 1" : " AND `profile_status` = 1";
			
			default:
				return $alias ? " AND `b`.`profile_status` = 1 AND `b`.`admin_status` = 1 AND `b`.`is_news` = 0" : " AND `profile_status` = 1 and `admin_status` = 1 AND `is_news` = 0";	
		}	
	}
	
	private function getReducedFieldList( $alias = false )
	{
		return $alias ? 
			"`b`.`id`, `b`.`profile_id`, `b`.`title`, `b`.`preview_text`, `b`.`mored_text`, `b`.`create_time_stamp`, `b`.`update_time_stamp`, `b`.`view_count`" : 
			"`id`,`profile_id`,`title`,`preview_text`,`mored_text`,`create_time_stamp`,`update_time_stamp`,`view_count`";	
	}
	
	private function getTotalReducedFieldList( $alias = false )
	{
		return $alias ? 
			"`b`.`id`,`b`.`profile_id`,`b`.`title`,`b`.`preview_text`,`b`.`create_time_stamp`,`b`.`update_time_stamp`,`b`.`view_count`" : 
			"`id`,`profile_id`,`title`,`preview_text`,`create_time_stamp`,`update_time_stamp`,`view_count`";
	}
	
	public function findAllActivePostList(){
			$query = SK_MySQL::placeholder("SELECT `id`, `p`.`username`
			FROM ". $this->getTableName(). " AS `b` LEFT JOIN `". TBL_PROFILE."` AS `p` ON (`b`.`profile_id` = `p`.`profile_id`) 
			WHERE `b`.`profile_status` = 1 AND `b`.`admin_status` = 1 AND `b`.`is_news` = 0  ORDER BY `b`.`create_time_stamp` DESC");

		return SK_MySQL::query($query)->mapObjectArray($this->getDtoClassName()); 
	}
}