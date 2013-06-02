<?php

require_once DIR_APPS . 'appAux/Event.php';

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov 17, 2008
 *
 */
class form_EventSpeedDatingAdd extends SK_Form
{

    public function __construct()
    {
        parent::__construct('event_speed_dating_add');
    }

    /**
     * @see SK_Form::setup()
     *
     */
    public function setup()
    {
        $this->registerField(new fieldType_text('event_title'));

        $text_field = new fieldType_textarea('event_desc');
        $text_field->maxlength = 100000;
        $this->registerField($text_field);

        $s = new fieldType_date('date');

        $s->setRange(array('min' => date('Y', SK_I18N::mktimeLocal()), 'max' => (date('Y', SK_I18N::mktimeLocal()) + 5 )));

        $this->registerField($s);

        $this->registerField(new fieldType_time('start_time'));
        $this->registerField(new fieldType_time('end_time'));

        $this->registerField(new EventSpeedDatingUploadField());

        $this->registerField(new fieldType_checkbox('search_by_location'));

        $this->registerField(new fieldType_checkbox('i_am_attending'));

        $this->registerAction(new EventSpeedDatingAddAction());
    }
}

class EventSpeedDatingUploadField extends fieldType_file
{

    public function __construct( $name = 'file_speed_dating' )
    {
        parent::__construct($name);
    }

    /**
     * @see fieldType_file::setup()
     *
     * @param SK_Form $form
     */
    public function setup( SK_Form $form )
    {
        $this->allowed_extensions = array('jpg', 'jpeg', 'gif', 'png');
        $this->allowed_mime_types = array('image/jpg', 'image/jpeg', 'image/gif', 'image/png', 'image/x-png', 'image/pjpeg');
        $this->max_file_size = 2 * 1024 * 1024;

        parent::setup($form);
    }

    /**
     * @see fieldType_file::preview()
     *
     * @param SK_TemporaryFile $tmp_file
     */
    public function preview( SK_TemporaryFile $tmp_file )
    {
        $file_url = $tmp_file->getURL();
        return '<div><img src="' . $file_url . '" width="100" />&nbsp;&nbsp;&nbsp;<a class="delete_file_btn" style="cursor:pointer;">Delete</a></div>';
    }
}

class EventSpeedDatingAddAction extends SK_FormAction
{

    public function __construct()
    {
        parent::__construct('event_speed_dating_add');
    }

    /**
     * @return app_EventService
     */
    private function getEventService()
    {
        return app_EventService::newInstance();
    }

    /**
     * @see SK_FormAction::process()
     *
     * @param array $data
     * @param SK_FormResponse $response
     * @param SK_Form $form
     */
    public function process( array $data, SK_FormResponse $response, SK_Form $form )
    {
        if ( !SK_HttpUser::is_authenticated() )
        {
            $response->addError("You should sign in to add comments!");
            return;
        }

        if ( isset($data['file_speed_dating']) )
        {
            $conf = new SK_Config_Section('blogs');

            $conf_section = $conf->Section('image');

            $max_width = $conf_section->get('blog_post_image_max_width');

            $max_height = $conf_section->get('blog_post_image_max_height');

            $max_size = $conf_section->get('blog_post_image_max_size');

            $tmp_file = new SK_TemporaryFile($data['file_speed_dating']);

            if ( $tmp_file->getSize() > (int) $max_size * 1024 )
            {
                $response->addError(SK_Language::text('blogs.error_max_image_size', array('size' => $max_size . 'KB')));
                return;
            }

            $properties = GetImageSize($tmp_file->getPath());

            if ( !$properties || $properties[0] > $max_width )
            {
                $response->addError(SK_Language::text('blogs.error_max_image_width', array('width' => $max_width . 'px')));
                return;
            }

            if ( !$properties || $properties[1] > $max_height )
            {
                $response->addError(SK_Language::text('blogs.error_max_image_height', array('height' => $max_height . 'px')));
                return;
            }
        }

        if ( !isset($data['start_time']) || !isset($data['end_time']) )
        {
            $start_time = mktime(0, 0, 0, $data['date']['month'], $data['date']['day'], $data['date']['year']);
            $end_time = null;
        }
        elseif ( (isset($data['start_time']['day_part']) && $data['start_time']['day_part']) || ( isset($data['end_time']['day_part']) && $data['end_time']['day_part'] ) )
        {
            $data['start_time']['hour'] = $data['start_time']['hour'] == 12 ? 0 : $data['start_time']['hour'];
            $data['end_time']['hour'] = $data['end_time']['hour'] == 12 ? 0 : $data['end_time']['hour'];

            $start_time = mktime(( $data['start_time']['day_part'] == 'am' ? (int) $data['start_time']['hour'] : (int) $data['start_time']['hour'] + 12), (int) $data['start_time']['minute'], 0, (int) $data['date']['month'], (int) $data['date']['day'], (int) $data['date']['year']);

            $end_time = mktime(( $data['end_time']['day_part'] == 'am' ? (int) $data['end_time']['hour'] : (int) $data['end_time']['hour'] + 12), (int) $data['end_time']['minute'], 0, (int) $data['date']['month'], (int) $data['date']['day'], (int) $data['date']['year']);
        }
        else
        {
            $start_time = mktime((int) $data['start_time']['hour'], (int) $data['start_time']['minute'], 0, (int) $data['date']['month'], (int) $data['date']['day'], (int) $data['date']['year']);

            $end_time = mktime((int) $data['end_time']['hour'], (int) $data['end_time']['minute'], 0, (int) $data['date']['month'], (int) $data['date']['day'], (int) $data['date']['year']);
        }

        $event = $this->getEventService()->findPeriodEvents($start_time, $end_time, 0, 1, 1);
        if ( !empty($event) )
        {
            $response->addError(SK_Language::text('event.msg_other_event'));
            return;
        }

        $search_by_location = (isset($data['search_by_location'])) ? 1 : 0;

        $new_event = new Event(
                trim($data['event_title']),
                trim($data['event_desc']),
                $start_time,
                $end_time,
                time(),
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                (isset($data['i_am_attending']) ? 1 : 0),
                SK_HttpUser::profile_id(),
                null,
                1,
                0,
                $search_by_location
        );

        $this->getEventService()->saveEvent($new_event);

        //-- LActivity
        $action = new SK_UserAction('event_add', SK_HttpUser::profile_id());
        $action->status = ( SK_Config::section('site')->Section('automode')->set_active_event_on_submit || SK_HttpUser::isModerator() ) ? 'active' : 'approval';
        $action->item = $new_event->getId();
        $action->unique = $new_event->getId();

        app_UserActivities::trace_action($action);

        //~~
        if ( isset($data['file_speed_dating']) )
        {
            $new_event->setImage($this->getEventService()->saveEventImage($new_event->getId(), $tmp_file));

            $this->getEventService()->updateEvent($new_event);
        }


        /* --------------------------------------- */

        if ( isset($data['i_am_attending']) )
        {
            $new_entry = new EventProfile($new_event->getId(), SK_HttpUser::profile_id());

            $this->getEventService()->saveOrUpdateEventProfile($new_entry);
        }

        /* --------------------------------------- */

        if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
        {
            $newsfeedDataParams = array(
                'params' => array(
                    'feature' => FEATURE_EVENT,
                    'entityType' => 'event_add',
                    'entityId' => $new_event->getId(),
                    'userId' => $new_event->getProfile_id(),
                    'status' => ( $new_event->getAdmin_status() == 1 ) ? 'active' : 'approval'
                )
            );
            app_Newsfeed::newInstance()->action($newsfeedDataParams);
        }

        /*
          $service_to_track = new SK_Service( 'event_submit', SK_httpUser::profile_id() );
          $service_to_track->checkPermissions();
          $service_to_track->trackServiceUse();
         */
        $response->addMessage(SK_Language::text('event.msg_submit_event_speed_dating'));

        $response->redirect(SK_Navigation::href('events'));
    }

    /**
     * @see SK_FormAction::setup()
     *
     * @param SK_Form $form
     */
    public function setup( SK_Form $form )
    {
        $this->required_fields = array('event_title', 'event_desc', 'date', 'start_time', 'end_time');
        parent::setup($form);
    }
}

