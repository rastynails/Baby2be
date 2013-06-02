<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Oct 21, 2008
 * 
 */

final class component_NewsfeedAddComment extends SK_Component
{
	/**
	 * @var string
	 */
	private $feature;
	
	/**
	 * @var app_CommentService
	 */
	private $comment_service;
	
	/**
	 * @var integer
	 */
	private $entity_id;
	/**
	 * @var string
	 */
	private $entityType;
	/**
	 * Class
	 *
	 * @param integer $entityId
	 * @param string $feature ( blog | event | photo | video | music | profile )
	 */
	public function __construct( $entityType, $entity_id, $feature )
	{
		parent::__construct( 'newsfeed_add_comment' );

		$this->comment_service = app_CommentService::newInstance( $feature );
		
		if( $this->comment_service === null )
		{
			$this->annul();
		}

        $this->entityType = $entityType;
		$this->entity_id = $entity_id;
		$this->feature = $feature;

		switch ( $this->feature )
		{
			case FEATURE_BLOG:
			case FEATURE_EVENT:
				break;
				
			case FEATURE_PHOTO:
				if(!app_Features::isAvailable(30))
					$this->annul();
				break;
				
            case FEATURE_MUSIC:
				if(!app_Features::isAvailable(40))
					$this->annul();
				break;
				
			case FEATURE_VIDEO:
				break;
				
			case FEATURE_PROFILE:
				if(!app_Features::isAvailable(25))
					$this->annul();
				break;
				
			case FEATURE_GROUP:
				if(!app_Features::isAvailable(38))
					$this->annul();
				break;
            
            case FEATURE_CLASSIFIEDS:
                if(!app_Features::isAvailable(45))
					$this->annul();
				break;
                
			case FEATURE_NEWSFEED:
				if(!app_Features::isAvailable(app_Newsfeed::FEATURE_ID))
					$this->annul();
				break;
		}
	}
	
	
	/**
	 * @see SK_Component::prepare()
	 *
	 * @param SK_Layout $Layout
	 * @param SK_Frontend $Frontend
	 */
	function prepare ( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$handler = new SK_ComponentFrontendHandler('NewsfeedAddComment');
		$this->frontend_handler = $handler;
		$handler->construct();
	}

	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 */
	public function render( SK_Layout $Layout )
	{
		$configs = $this->comment_service->getConfigs();
		
		$service = new SK_Service( $configs['add_service'], SK_httpUser::profile_id() );
		
		if ( $service->checkPermissions() == SK_Service::SERVICE_FULL )
		{
			//custom cond for profile cmts
			if( $this->feature == FEATURE_PROFILE && 
				app_ProfilePreferences::get('my_profile','friends_only_comments', $this->entity_id ) && 
				( $this->entity_id != SK_HttpUser::profile_id() && !app_FriendNetwork::isProfileFriend( $this->entity_id, SK_HttpUser::profile_id() ) ) )
			{
				$Layout->assign( 'err_message', SK_Language::text( 'comment.friends_only_comments_msg' ) );
			}

                        //custom cond for blog cmts
			if( in_array($this->feature, array(FEATURE_BLOG, FEATURE_NEWS)) &&
				!app_ProfilePreferences::get('blogs','allow_comments', $this->comment_service->findEntityOwnerId($this->entity_id, $this->feature) )
                        ) {
				$Layout->assign( 'err_message', SK_Language::text( 'comment.blog_comments_not_allowed' ) );
			}

			//custom cond for group comments
			if( $this->feature == FEATURE_GROUP && (int)SK_HttpRequest::$GET['group_id'] && !app_Groups::isGroupMember(SK_HttpUser::profile_id(), SK_HttpRequest::$GET['group_id']) )
			{
				$Layout->assign( 'err_message', SK_Language::text( 'comment.group_members_only_msg' ) );
			}
		}
		else 
		{
			$Layout->assign( 'err_message', $service->permission_message['message'] );
		}

		$Layout->assign( 'comment_list', new component_NewsfeedCommentList( array( 'tpl'=>'newsfeed_comment_list', 'entityType'=> $this->entityType, 'entity_id' => $this->entity_id, 'feature' => $this->feature, 'page' => 1, 'mode' => 0 ) ) );
		
		return parent::render( $Layout );
	}
	
	/**
	 * @see SK_Component::handleForm()
	 *
	 * @param SK_Form $form
	 */
	public function handleForm ( SK_Form $form )
	{
		$form->getField( 'entity_id' )->setValue( $this->entity_id );
        $form->getField( 'entityType' )->setValue( $this->entityType );
		$form->getField( 'feature' )->setValue( $this->feature );
		$form->getField( 'mode' )->setValue( $this->mode );
		$component_var = $this->frontend_handler->auto_var();
		
		$form->frontend_handler->bind("success", "function(data) {
			 
			this.\$form[0].reset();
			
			var children = this.ownerComponent.children;
			
			for (var i = 0; i < children.length; i++) {
				var child = children[i];
				
				if (child instanceof component_NewsfeedCommentList && child.feature == data.feature && child.entityType == data.entityType && child.entity_id == data.entity_id) {
					child.reload({tpl: 'newsfeed_comment_list', entityType: data.entityType, entity_id:data.entity_id, feature:data.feature, page:1, mode:data.mode});
                    this.ownerComponent.parent.comments++;
                    this.ownerComponent.parent.refreshCounter();
				}
			}


		}");
	}
}