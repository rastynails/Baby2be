<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov 16, 2008
 * 
 */
class component_EventsList extends SK_Component
{
    /**
     * @var app_EventService
     */
    private $event_service;
    /**
     * @var array
     */
    private $page_params;

    /**
     * Class constructor
     *
     * @param array
     */
    public function __construct( array $page_params )
    {
        parent::__construct('events_list');

        $this->event_service = app_EventService::newInstance();

        $this->page_params = $page_params;
    }

    /**
     * @see SK_Component::render()
     *
     * @param SK_Layout $Layout
     */
    public function render( SK_Layout $Layout )
    {

        $lang_section = SK_Language::section('components.event_list');

        $date_section = SK_Language::section('i18n.date');

        $event_section = SK_Language::section('event');

        $service = new SK_Service('event_view', SK_httpUser::profile_id());

        if ( $this->page_params['year'] === null || $this->page_params['month'] === null )
        {
            $events = $this->event_service->findDefaultMonthEvents($this->page_params['page']);
            $speed_dating_events = $this->event_service->findDefaultMonthEvents($this->page_params['page'], 1);
            $cap_label = $lang_section->text('default_list_label');
            $speed_dating_cap_label = $lang_section->text('speed_dating_default_list_label');
        }
        elseif ( $this->page_params['day'] === null )
        {
            $events = $this->event_service->findMonthEvents($this->page_params['year'], $this->page_params['month'], $this->page_params['page']);
            $speed_dating_events = $this->event_service->findMonthEvents($this->page_params['year'], $this->page_params['month'], $this->page_params['page'], 1);
            $cap_label = $date_section->text('month_full_' . $this->page_params['month']) . ' ' . $event_section->text('events');
            $speed_dating_cap_label = $date_section->text('month_full_' . $this->page_params['month']) . ' ' . $event_section->text('speed_dating_events');
        }
        else
        {
            $events = $this->event_service->findDayEvents($this->page_params['year'], $this->page_params['month'], $this->page_params['day'], $this->page_params['page']);
            $speed_dating_events = $this->event_service->findDayEvents($this->page_params['year'], $this->page_params['month'], $this->page_params['day'], $this->page_params['page'], 1);
            $cap_label = $this->page_params['day'] . ' ' . $date_section->text('month_full_' . $this->page_params['month']) . ' ' . $event_section->text('events');
            $speed_dating_cap_label = $this->page_params['day'] . ' ' . $date_section->text('month_full_' . $this->page_params['month']) . ' ' . $event_section->text('speed_dating_events');
        }
        
        foreach ( $events as $key => $value )
        {
            if ( $value['dto']->getImage() != null )
                $events[$key]['image_url'] = $this->event_service->getEventImageURL('event_icon_' . $value['dto']->getId() . '.jpg');
            else
                $events[$key]['image_url'] = $this->event_service->getEventDefaultImageURL();

            $events[$key]['event_url'] = component_Event::getEventUrl($value['dto']->getId());
            $events[$key]['profile_url'] = SK_Navigation::href('profile', array('profile_id' => $value['dto']->getProfile_id()));
            $events[$key]['description'] = app_TextService::stOutputFormatter($value['dto']->getDescription());
            $events[$key]['username'] = app_Profile::username($value['dto']->getProfile_id());

            if ( $value['dto']->getCity_id() != null && trim($value['dto']->getCity_id()) )
            {
                $events[$key]['city_label'] = app_Location::CityNameById($value['dto']->getCity_id());
            }
            if ( $service->checkPermissions() != SK_Service::SERVICE_FULL )
            {
                $events[$key]['dto']->setDescription('');
            }
        }

        foreach ( $speed_dating_events as $key => $value )
        {
            if ( $value['dto']->getImage() != null )
                $speed_dating_events[$key]['image_url'] = $this->event_service->getEventImageURL('event_icon_' . $value['dto']->getId() . '.jpg');
            else
                $speed_dating_events[$key]['image_url'] = $this->event_service->getEventDefaultImageURL();

            $speed_dating_events[$key]['event_url'] = component_Event::getEventUrl($value['dto']->getId());
            $speed_dating_events[$key]['profile_url'] = SK_Navigation::href('profile', array('profile_id' => $value['dto']->getProfile_id()));
            $speed_dating_events[$key]['description'] = app_TextService::stOutputFormatter($value['dto']->getDescription());

            if ( $value['dto']->getCity_id() != null && trim($value['dto']->getCity_id()) )
            {
                $speed_dating_events[$key]['city_label'] = app_Location::CityNameById($value['dto']->getCity_id());
            }

            if ( $service->checkPermissions() != SK_Service::SERVICE_FULL )
            {
                $speed_dating_events[$key]['dto']->setDescription('');
            }
        }

        $Layout->assign('cap_label', $cap_label);
        $Layout->assign('speed_dating_cap_label', $speed_dating_cap_label);


        if ( empty($events) )
            $Layout->assign('no_events', true);

        $Layout->assign('events', $events);
        $Layout->assign('speed_dating_events', $speed_dating_events);

        // hotfix - add pending approval events

        if ( SK_HttpUser::is_authenticated() )
        {
            $pevents = $this->event_service->findUserPendingApprovalEvents(SK_HttpUser::profile_id());

            foreach ( $pevents as $key => $value )
            {
                if ( $value['dto']->getImage() != null )
                    $pevents[$key]['image_url'] = $this->event_service->getEventImageURL('event_icon_' . $value['dto']->getId() . '.jpg');
                else
                    $pevents[$key]['image_url'] = $this->event_service->getEventDefaultImageURL();

                $pevents[$key]['event_url'] = component_Event::getEventUrl($value['dto']->getId());
                $pevents[$key]['profile_url'] = SK_Navigation::href('profile', array('profile_id' => $value['dto']->getProfile_id()));
                $pevents[$key]['description'] = app_TextService::stOutputFormatter($value['dto']->getDescription());

                if ( $value['dto']->getCity_id() != null && trim($value['dto']->getCity_id()) )
                {
                    $pevents[$key]['city_label'] = app_Location::CityNameById($value['dto']->getCity_id());
                }

                if ( $service->checkPermissions() != SK_Service::SERVICE_FULL )
                {
                    $pevents[$key]['dto']->setDescription('');
                }
            }

            $Layout->assign('pevents', $pevents);
            $Layout->assign('pevents_title', $lang_section->text('user_pending_approval_events_block_title'));
        }



        return parent::render($Layout);
    }
}