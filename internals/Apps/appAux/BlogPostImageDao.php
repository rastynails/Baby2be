<?php

require_once DIR_APPS.'appAux/BlogPostImage.php';

class BlogPostImageDao extends SK_BaseDao
{
	
	/**
	 * @see SK_BaseDao::getDtoClassName()
	 *
	 */
	protected function getDtoClassName(){return 'BlogPostImage';}
	
	/**
	 * @see SK_BaseDao::getTableName()
	 *
	 */
	protected function getTableName(){return '`'.TBL_BLOG_POST_IMAGE.'`';}
	
//	public function findPostImageList( $post_id )
//	{
//		$query = SK_MySQL::placeholder("SELECT * FROM ".$this->getTableName()." WHERE `post_id`=?", $post_id);
//
//		return SK_MySQL::query($query)->mapObjectArray($this->getDtoClassName());
//		
//	}
	
	public function findPostImages( $post_id, $profile_id )
	{
		if( is_null($post_id) )
			$query = SK_MySQL::placeholder("SELECT * FROM ".$this->getTableName()." WHERE `post_id` IS NULL AND `profile_id` = ?", $profile_id);
		else 
			$query = SK_MySQL::placeholder("SELECT * FROM ".$this->getTableName()." WHERE `post_id`=?", $post_id);
		
		return SK_MySQL::query($query)->mapObjectArray($this->getDtoClassName());
	}
	
	public function setImagePostId( $post_id, $profile_id )
	{
		$query = SK_MySQL::placeholder("UPDATE ".$this->getTableName()." SET `post_id` = ? WHERE `post_id` IS NULL AND `profile_id` = ? ", $post_id, $profile_id);
		
		SK_MySQL::query($query);
	}
	
	

}