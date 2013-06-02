<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Oct 21, 2008
 *
 */

final class component_AddComment extends SK_Component
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
	 * @var boolean ( true - full style || false - paging style )
	 */
	private $mode;

	/**
	 * Class
	 *
	 * @param integer $entityId
	 * @param string $feature ( blog | event | photo | video | music | profile )
	 */
	public function __construct( $entity_id, $feature, $entityType )
	{
		parent::__construct( 'add_comment' );

		$this->comment_service = app_CommentService::newInstance( $feature );

		if( $this->comment_service === null )
		{
			$this->annul();
		}

		$this->entity_id = $entity_id;
        $this->entityType = $entityType;
		$this->feature = $feature;


        if ( ($this->feature == FEATURE_PROFILE) && app_Bookmark::isProfileBlocked($this->entity_id, SK_httpUser::profile_id()) )
        {
            $this->annul();
        }

		switch ( $this->feature )
		{
			case FEATURE_EVENT:
				$this->mode = 1;
				break;

            case FEATURE_BLOG:
                if(!app_Features::isAvailable(61))
					$this->annul();
				$this->mode = 0;
				break;

            case FEATURE_NEWS:
                if(!app_Features::isAvailable(63))
					$this->annul();
				$this->mode = 0;
				break;

			case FEATURE_PHOTO:
				if(!app_Features::isAvailable(30))
					$this->annul();
				$this->mode = 0;
				break;

            case FEATURE_MUSIC:
				if(!app_Features::isAvailable(40))
					$this->annul();
				$this->mode = 0;
				break;

			case FEATURE_VIDEO:
				$this->mode = 0;
				break;

			case FEATURE_PROFILE:
				if(!app_Features::isAvailable(25))
					$this->annul();
				$this->mode = 0;
				break;

			case FEATURE_GROUP:
				if(!app_Features::isAvailable(38))
					$this->annul();
				$this->mode = 0;
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
		$handler = new SK_ComponentFrontendHandler('AddComment');
		$this->frontend_handler = $handler;
		$handler->construct();
	}


	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 */
	public function render ( SK_Layout $Layout ) 
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

        $Layout->assign( 'comment_list', new component_CommentList( array( 'entity_id' => $this->entity_id, 'entityType'=>$this->entityType, 'feature' => $this->feature, 'page' => 1, 'mode' => $this->mode ) ) );
		$Layout->assign( 'mode', $this->mode );

		return parent::render( $Layout );
	}

	/**
	 * @see SK_Component::handleForm()
	 *
	 * @param SK_Form $form
	 */
	public function handleForm( SK_Form $form )
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

				if (child instanceof component_CommentList) {
					child.reload({entity_id:data.entity_id, entityType:data.entityType, feature:data.feature, page:1, mode:data.mode});
				}
			}
		}");
	}
}