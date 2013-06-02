<?php

class component_ProfileView extends SK_Component
{
	/**
	 * @var app_ProfileComponentService
	 */
	private $pc_service;

	/**
	 * @var integer
	 */
	private $profile_id;

	/**
	 * @var integer
	 */
	private $visitor_id;

	/**
	 * @var array
	 */
	private $cmp_list;

	/**
	 * @var array
	 */
	private $view_list;

	/**
	 * @var boolean
	 */
	private $owner_view_mode;

	/**
	 * @var array
	 */
	private $cmp_params;


        /**
	 * @var boolean
	 */
	private $is_private = false;

	/**
	 * Class constructor
	 *
	 * @param array $params
	 */
	public function __construct( array $params = null )
	{
		parent::__construct('profile_view');

		$this->profile_id = ( (int)SK_HttpRequest::$GET['profile_id'] > 0 ) ? (int)SK_HttpRequest::$GET['profile_id'] : null;

        if( SK_HttpUser::is_authenticated() )
        {
           $this->visitor_id = SK_HttpUser::profile_id();
        }

        if( $this->profile_id === null )
        {
            if( !SK_HttpUser::is_authenticated() )
            {
                SK_HttpRequest::showFalsePage();
            }

            $this->profile_id = SK_HttpUser::profile_id();
        }

		$this->owner_view_mode = ( $this->profile_id == $this->visitor_id ) ? true : false;

		$service_to_track = new SK_Service('view_profiles');

        if( !$this->owner_view_mode && $service_to_track->checkPermissions() !== SK_Service::SERVICE_FULL )
        {
            $_SESSION['messages'][] = array('message' => $service_to_track->permission_message['alert'], 'type' => 'error');
            SK_HttpRequest::redirect( SK_Navigation::href('payment_selection'));
        }

        $service_to_track->trackServiceUse();

		$this->pc_service = app_ProfileComponentService::newInstance();

		// getting cmp list

		$this->view_list = $this->pc_service->findProfileViewCMP( $this->profile_id );

        $this->is_private = false;
        if( $this->owner_view_mode )
        {
            $this->cmp_list = $this->pc_service->findNotInViewListCMP( $this->profile_id );
        }
        else if ( !app_FriendNetwork::checkRelation($this->profile_id, $this->visitor_id, 'friends') )
        {
            $private_service = new SK_Service( 'private_status', $this->profile_id );

            if( $private_service->checkPermissions() === SK_Service::SERVICE_FULL )
            {
                    $this->is_private = app_ProfilePreferences::get('my_profile', 'is_profile_private', $this->profile_id);
            }
        }

		$this->cmp_params = array( 'profile_id' => $this->profile_id, 'viewer_id' => SK_HttpUser::profile_id() );

		//Profile Views History tracking
		if ( $this->profile_id  != $this->visitor_id )
        {
			app_ProfileViewHistory::track(SK_HttpUser::profile_id(), $this->profile_id);
		}

	}


	/**
	 * @see SK_Component::prepare()
	 *
	 * @param SK_Layout $Layout
	 * @param SK_Frontend $Frontend
	 */
	public function prepare ( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$handler = new SK_ComponentFrontendHandler('ProfileView');
		$this->frontend_handler = $handler;

		$icons = array();
		$cmp_view = array();

                if ( !$this->is_private )
                {

                    if( $this->owner_view_mode )
                    {
                            $Frontend->include_js_file(URL_STATIC.'jquery.event.drag-1.2.js');
                            $Frontend->include_js_file(URL_STATIC.'jquery.event.drop.js');

                            foreach ( $this->cmp_list as $value )
                            {
                                    $icons['icmp_'.$value->getId()] = array( 'id' => 'icmp_'.$value->getId(), 'hid' => 'hcmp_'.$value->getId() );
                            }

                            foreach ( $this->view_list as $value )
                            {
                                    $cmp_view['vcmp_'.$value['dto']->getId()] = array( 'id' => 'vcmp_'.$value['dto']->getId(),
                                            'section' => $value['dto']->getSection(),
                                            //'label' => SK_Language::text( 'components.profile_cmp_select.cmp_'.$value['cid'] ),
                                            'position' => $value['dto']->getPosition(),
                                            'active' => false,
                                            'nid' => $value['dto']->getId() );
                            }
                    }
                    else
                    {
                            $icons = null;
                            $cmp_view = null;
                    }

                    $handler->construct( array( 'icons' => $icons, 'cmp_view' => $cmp_view, 'view_mode' => $this->owner_view_mode ) );
                }
	}

	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 */
	public function render( SK_Layout $Layout )
	{
		$cmps = array();
		$hidden_cmp = array();
		$left_cmp = array();
		$right_cmp = array();

//		if( $this->owner_view_mode )
//		{
//			foreach ( $this->cmp_list as $value )
//			{
//				$icon_cmps[] = array( 'className' => $value->getClass_name(), 'id' => $value->getId(), 'iid' => 'icmp_'.$value->getId() );
//
//				$class = new ReflectionClass('component_'.$value->getClass_name());
//
//				$hidden_cmp[] = array( 'cmp' => $class->newInstance( $this->cmp_params ), 'id' => $value->getId() );
//			}
//		}


                foreach ( $this->view_list as $value )
                {
                    if( !$this->owner_view_mode && !$this->pc_service->renderCmp( $value['class_name'], $this->profile_id ) )
                            continue;

                    $class = new ReflectionClass('component_'.$value['class_name']);

                    $this->cmp_params['cmp_id'] = $value['dto']->getId();

                    switch( $value['class_name'] ){
                            case "LatestActivity":
                                    $this->cmp_params['userId'] = $this->profile_id;
                                    $this->cmp_params['actor'] = 'user';
                                    break;
                    }


                    $temp_array = array(
                            'className' => $value['class_name'],
                            'cmp' => $class->newInstance( $this->cmp_params ),
                            'id' => $value['dto']->getId()
                    );

                    if ( !$this->is_private )
                    {
                        if( $value['dto']->getSection() === 1 )
                        {
                                $left_cmp[] = $temp_array;
                        }
                        else
                        {
                                $right_cmp[] = $temp_array;
                        }
                    }
                }

                if ( !$this->is_private )
                {
                    // Registering static cmps
                    if( app_Features::isAvailable( 25 ) )
                            $Layout->assign( 'comments_cmp', new component_AddComment( $this->profile_id, 'profile', 'profile_comment' ) );
                    else
                            $Layout->assign( 'comments_cmp', false );

                    $Layout->assign( 'photo_album_cmp', new component_PhotoGallery( $this->cmp_params ) );
                    $Layout->assign( 'profile_dtls', new component_ProfileDetails( $this->cmp_params ) );
                }

                $Layout->assign( 'profile_brief_info', new component_ProfileBriefInfo( $this->cmp_params ) );
                $Layout->assign( 'profile_notes', new component_ProfileNotes( $this->cmp_params ) );

		$Layout->assign( 'owner_mode', $this->owner_view_mode );
		$Layout->assign( 'icons', $icon_cmps );
		$Layout->assign( 'left_cmp', $left_cmp );
		$Layout->assign( 'right_cmp', $right_cmp );
		$Layout->assign( 'hidden_cmp', $hidden_cmp );

		$Layout->assign( 'report', new component_Report( array( 'type' => 'profile', 'reporter_id' => SK_HttpUser::profile_id(), 'entity_id' => $this->profile_id ) ) );
		$Layout->assign( 'profile_background', new component_ProfileBackground($this->cmp_params['profile_id']) );

		if( $this->owner_view_mode )
		{
			$Layout->assign( 'profile_component_select', new component_ProfileComponentSelect( $this->cmp_params ) );
		}

		//printArr( $Layout->_tpl_vars['comments_cmp'] );
		//printArr($hidden_cmp);exit();
		/* -------------------------------------------- */

		$recipient = SK_HttpRequest::$GET['profile_id'];
		$sender = SK_HttpUser::profile_id();
		$username = app_Profile::username($recipient);

		$Layout->assign('sender', $sender);
		$Layout->assign('recipient', $recipient);
		$Layout->assign('username', $username);
		$Layout->assign('actorId', $this->profile_id);
                $Layout->assign('is_private', (boolean)$this->is_private);

                $list_id = app_TempProfileList::getListSessionInfo( 'search', 'list_id' );

                $headline = app_Profile::getFieldValues( $this->profile_id, 'headline' );
                $headline = empty($headline) ? '' : $headline;

		SK_Language::defineGlobal( array(
                    'username' => app_Profile::username($this->profile_id),
                    'headline' => strip_tags($headline)
                ));

                $desc = app_Profile::getFieldValues($this->profile_id, 'general_description');
                if ( !empty($desc) )
                {
                    $this->getDocumentMeta()->description = strip_tags($desc);
                }

		return parent::render($Layout);
	}

	/**
	 * Ajax method for component adding
	 *
	 * @param stdObject $params
	 * @param SK_ComponentFrontendHandler $handler
	 */
//	public static function ajax_addCmp( $params, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response )
//	{
//		$service = app_ProfileComponentService::newInstance();
//
//		$cmp_entry = $service->findComponentById( (int)substr( $params->id, 5 ) );
//
//		if( $cmp_entry === null )
//		{
//
//		}
//
//		$pc = new ProfileComponent( $cmp_entry->getId(), SK_HttpUser::profile_id(), (int)$params->section, $params->position );
//
//		$service->saveOrUpdateProfileComponent( $pc );
//
//		$incrIds = array();
//
//		foreach ( $params->incrArray as $value )
//		{
//			if( $value )
//				$incrIds[] = (int)substr( $value, 5 );
//		}
//
//		if( !empty( $incrIds ) )
//			$service->incrementPosition( SK_HttpUser::profile_id(), $incrIds );
//
//		return array( 'id' => 'vcmp_'.$pc->getId() );
//	}
//
	/**
	 * Ajax method for component removing
	 *
	 * @param stdObject $params
	 * @param SK_ComponentFrontendHandler $handler
	 */
	public static function ajax_removeCmp( $params, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response )
	{
		$service = app_ProfileComponentService::newInstance();

		$pc = $service->findProfileComponentById( (int)substr($params->id,5) );

		$cmp_id = $pc->getComponent_id();

		$service->deleteById( $pc->getId() );

		$decrIds = array();

		foreach ( $params->decrArray as $value )
		{
			if( $value )
				$decrIds[] = (int)$value;
		}

		if( !empty( $decrIds ) )
			$service->decrementPosition( SK_HttpUser::profile_id(), $decrIds );

		$cmp = $service->findComponentById( $cmp_id );

		return array( 'id' => $cmp->getId(), 'class_name' => $cmp->getClass_name(), 'label' => SK_Language::text( 'components.profile_cmp_select.cmp_'.$cmp->getId() ) );
	}

	/**
	 * Ajax method for component removing
	 *
	 * @param stdObject $params
	 * @param SK_ComponentFrontendHandler $handler
	 */
	public static function ajax_changeCmpPosition( $params, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response )
	{
		$service = app_ProfileComponentService::newInstance();

		$id = (int)substr($params->id,5);
		$section = (int)$params->section;
		$position = (int)$params->position;

		$pc = $service->findProfileComponentById( $id );

		$service->saveOrUpdateProfileComponent( $pc->setSection( $section )->setPosition( $position ) );

		$incrIds = array();
		$decrIds = array();

		foreach ( $params->incrArray as $value )
		{
			if( $value )
				$incrIds[] = (int)$value;
		}

		foreach ( $params->decrArray as $value )
		{
			if( $value )
				$decrIds[] = (int)$value;
		}

		if( !empty( $incrIds ) )
			$service->incrementPosition( SK_HttpUser::profile_id(), $incrIds );

		if( !empty( $decrIds ) )
			$service->decrementPosition( SK_HttpUser::profile_id(), $decrIds );

	}

}
