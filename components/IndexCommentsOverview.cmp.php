<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Jan 06, 2009
 * 
 */

final class component_IndexCommentsOverview extends component_CommentsOverview
{
	/**
	 * Class constructor
	 *
	 * @param array $params
	 */
	public function __construct( array $params = null )
	{
		parent::__construct( 'index_comments_overview' );

        $this->getLastCommentsList( 5 );
	}
	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 */
	public function render ( SK_Layout $Layout )
	{
        $Layout->assign('comments', $this->comments_array);
        $Layout->assign('comments_url', SK_Navigation::href('comments_overview'));

        return parent::render( $Layout );
	}


}