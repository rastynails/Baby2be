<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov 10, 2008
 *
 */


class component_Event extends SK_Component
{
    /**
     * @var app_EventService
     */
    private $event_service;

    /**
     * @var Event
     */
    private $event;

    /**
     * Class constructor
     *
     */
    public function __construct()
    {
        parent::__construct('event');
        $this->event_service = app_EventService::newInstance();

        $event = $this->event_service->findById((int) SK_HttpRequest::$GET['eventId']);

        if ( $event === null )
        {
            SK_HttpRequest::showFalsePage();
        }

        $this->event = $event;
    }

    /**
     * @see SK_Component::prepare()
     *
     * @param SK_Layout $Layout
     * @param SK_Frontend $Frontend
     */
    public function prepare ( SK_Layout $Layout, SK_Frontend $Frontend )
    {
        $this->frontend_handler = new SK_ComponentFrontendHandler('Event');

        $js_array = array(
            'id' => $this->event->getId(),
            'status' => ( $this->event->getAdmin_status() ) ? 'none' : 'active',
            'block_label' => SK_Language::text('label.moder_block'),
            'approve_label' => SK_Language::text('blogs.label_approve'),
            'confirm_cap' => SK_Language::text('label.confirm_cap_label'),
            'confirm_msg' => SK_Language::text('event.event_dlt_confirm_msg')
        );

        $this->frontend_handler->construct($js_array);
    }


    public function render( SK_Layout $Layout )
    {
        $event_info = $this->event_service->findEventInfo($this->event->getId());

        if ( $this->event->getImage() != null )
        {
            $Layout->assign('image_url', $this->event_service->getEventImageURL($this->event->getImage()));
        }

        $event_info['title'] = app_TextService::stCensor($event_info['dto']->getTitle(), FEATURE_EVENT, true);
        $event_info['desc'] = app_TextService::stOutputFormatter(nl2br(app_TextService::stCensor($event_info['dto']->getDescription(), FEATURE_EVENT)));

        $Layout->assign('event', $event_info);

        if ( $this->event->getCity_id() != null && trim($this->event->getCity_id()) )
        {
            $event_info['city_label'] = app_Location::CityNameById($this->event->getCity_id());
        }

        if ( !$this->event_service->isEventExpired($this->event) )
        {
            $Layout->assign('not_expired', 1);


            if ( $this->event->getIs_speed_dating() &&
                $this->event_service->findEventProfileEntry($this->event->getId(), SK_HttpUser::profile_id()) == null &&
                SK_HttpUser::profile_id() != $this->event->getProfile_id()
            )
            {
                $service = new SK_Service('participate_speed_dating', SK_httpUser::profile_id());

                if ( $service->checkPermissions() == SK_Service::SERVICE_FULL )
                {
                    $Layout->assign('event_attend', new component_EventAttend($this->event->getId()));
                }
                else
                {
                    $Layout->assign('speed_dating_err_message', $service->permission_message['message']);
                }
            }
            else
            {
                $Layout->assign('event_attend', new component_EventAttend($this->event->getId()));
            }
        }
        else
        {
            if ( $this->event->getIs_speed_dating() && ( time() >= $this->event->getEnd_date()) )
            {
                $Layout->assign('bookmarks_url', SK_Navigation::href('event_speed_dating_bookmark_list', array('event_id' => $this->event->getId())));
            }
        }

        $Layout->assign('event_not_attendees', new component_EventNotAttendees($this->event));
        $Layout->assign('event_attendees', new component_EventAttendees($this->event));
        $Layout->assign('event_comments', new component_AddComment($this->event->getId(), 'event', 'event_add'));

        if ( SK_HttpUser::is_authenticated() && ( SK_HttpUser::isModerator() || $this->event->getProfile_id() == SK_HttpUser::profile_id() ) )
        {
            if ( $this->event->getIs_speed_dating() )
            {
                $Layout->assign('edit_url', component_EventSpeedDatingEdit::getEventEditURL($this->event->getId()));
            }
            else
            {
                $Layout->assign('edit_url', component_EventEdit::getEventEditURL($this->event->getId()));
            }

            $Layout->assign('delete_button', true);

            if ( SK_HttpUser::isModerator() )
            {
                if ( $this->event->getAdmin_status() )
                    $Layout->assign('block_button', true);
                else
                    $Layout->assign('approve_button', true);

            }
        }

        SK_Navigation::removeBreadCrumbItem();

        SK_Navigation::addBreadCrumbItem(app_TextService::stCensor($this->event->getTitle(), FEATURE_EVENT, true));

        SK_Language::defineGlobal(array('eventtitle' => app_TextService::stCensor($this->event->getTitle(), FEATURE_EVENT, true)));

        $this->getDocumentMeta()->description = $this->event->getDescription();

        if ( !SK_HttpUser::isModerator(SK_httpUser::profile_id()) )
        {
            $service = new SK_Service('event_view', SK_httpUser::profile_id());
            if ( $service->checkPermissions() == SK_Service::SERVICE_FULL )
            {
                $service->trackServiceUse();

            }
            else
            {
                $Layout->assign('err_message', $service->permission_message['message']);
            }
        }

        return parent::render($Layout);
    }

    /**
     * Ajax method for updating event status
     *
     * @param stdObject $params
     * @param SK_ComponentFrontendHandler $handler
     */
    public static function ajax_updateEventStatus( $params, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response )
    {
        $service = app_EventService::newInstance();

        $event = $service->findById($params->id);

        if ( !SK_HttpUser::isModerator() || $event === null )
        {
            return;
        }

        $new_status = ( $params->status == 'active' ) ? 1 : 0;

        if ( $event->getAdmin_status() == $new_status )
            return;

        $event->setAdmin_status($new_status);
        $service->saveEvent($event);

        $aList = app_UserActivities::getWhere(" `item`={$event->getId()} and `type` IN ('event_add', 'event_comment')");

        foreach ( $aList as $a )
        {
            $s = ( $new_status == 1 ) ? 'active' : 'approval';

            app_UserActivities::setStatus($a['skadate_user_activity_id'], $s);
        }

        if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
        {
            $newsfeedDataParams = array(
                'params' => array(
                    'feature' => FEATURE_EVENT,
                    'entityType' => 'event_add',
                    'entityId' => $event->getId(),
                    'userId' => $event->getProfile_id(),
                    'status' => ( $event->getAdmin_status() == 1 ) ? 'active' : 'approval'
                )
            );
            app_Newsfeed::newInstance()->updateStatus($newsfeedDataParams);
        }
    }

    public static function ajax_deleteEvent( $params, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response )
    {
        $service = app_EventService::newInstance();

        $event = $service->findById($params->id);

        if ( $event === null || (!SK_HttpUser::isModerator() && $event->getProfile_id() != SK_HttpUser::profile_id() ) )
        {
            $response->addError('Error');
            return;
        }

        $service->deleteEvent($event);

        $handler->redirect(SK_Navigation::href('events'));
    }

    /* static util methods */

    public static function getEventUrl( $event_id )
    {
        return SK_Navigation::href('event', array('eventId' => $event_id));
    }

}