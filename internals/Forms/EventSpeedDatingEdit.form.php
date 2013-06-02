<?php

require_once DIR_APPS . 'appAux/Event.php';
require_once DIR_APPS . 'appAux/EventProfile.php';

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov , 2008
 * 
 */
class form_EventSpeedDatingEdit extends SK_Form
{

    public function __construct()
    {
        parent::__construct('event_speed_dating_edit');
    }

    /**
     * @see SK_Form::setup()
     *
     */
    public function setup()
    {
        $this->registerField(new fieldType_text('event_title'));

        $text_field = new fieldType_textarea('event_desc');
        $text_field->maxlength = 10000;
        $this->registerField($text_field);
        $this->registerField(new fieldType_hidden('event_id'));


        $s = new fieldType_date('date');

        $s->setRange(array('min' => date('Y', time()), 'max' => (date('Y', time()) + 5 )));

        $this->registerField($s);

        $this->registerField(new fieldType_time('start_time'));
        $this->registerField(new fieldType_time('end_time'));

        $this->registerField(new EventSpeedDatingUploadField());

        $this->registerField(new fieldType_checkbox('search_by_location'));

        $this->registerField(new fieldType_checkbox('i_am_attending'));

        $this->registerAction(new EventSpeedDatingEditAction());
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
        $this->allowed_mime_types = array('image/jpg', 'image/jpeg', 'image/gif', 'image/png', 'image/x-png');
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

class EventSpeedDatingEditAction extends SK_FormAction
{

    public function __construct()
    {
        parent::__construct('event_speed_dating_edit');
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

        $event_item = $this->getEventService()->findById((int) $data['event_id']);

        $event = $this->getEventService()->findPeriodEvents($start_time, $end_time, 0, 1, 1);
        if ( !empty($event) && $event[0]['dto']->getId() != $event_item->getId() )
        {
            $response->addError(SK_Language::text('event.msg_other_event'));
            return;
        }

        if ( $event_item === null || (!SK_HttpUser::isModerator() && ( SK_HttpUser::profile_id() != $event_item->getProfile_id())) )
        {
            $response->addError("No event to edit");
        }

        $event_item->setTitle(trim($data['event_title']));
        $event_item->setDescription(trim($data['event_desc']));
        $event_item->setStart_date($start_time);
        $event_item->setEnd_date($end_time);


        if ( isset($data['search_by_location']) )
        {
            $event_item->setSearch_by_location(1);
        }
        else
        {
            $event_item->setSearch_by_location(0);
        }

        $event_item->setNotified(0);


        if ( isset($data['i_am_attending']) )
        {
            if ( $this->getEventService()->findEventProfileEntry($event_item->getId(), SK_HttpUser::profile_id()) === null )
            {
                $event_item->setI_am_attand((isset($data['i_am_attending']) ? 1 : 0));
                $event_profile = new EventProfile($event_item->getId(), SK_HttpUser::profile_id());
                $this->getEventService()->saveOrUpdateEventProfile($event_profile);
            }
        }
        else
        {
            $this->getEventService()->deleteEventProfileByEventIdAndProfileId($event_item->getId(), SK_HttpUser::profile_id());
        }


        $this->getEventService()->saveEvent($event_item);

        if ( isset($data['file_speed_dating']) )
        {
            $event_item->setImage($this->getEventService()->saveEventImage($event_item->getId(), $tmp_file));

            $this->getEventService()->updateEvent($event_item);
        }

        if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
        {
            $newsfeedDataParams = array(
                'params' => array(
                    'feature' => FEATURE_EVENT,
                    'entityType' => 'event_add',
                    'entityId' => $event_item->getId(),
                    'userId' => $event_item->getProfile_id(),
                    'status' => ( $event_item->getAdmin_status() == 1 ) ? 'active' : 'approval'
                )
            );
            app_Newsfeed::newInstance()->action($newsfeedDataParams);
        }

        $response->redirect(component_Event::getEventUrl($event_item->getId()));
    }

    /**
     * @see SK_FormAction::setup()
     *
     * @param SK_Form $form
     */
    public function setup( SK_Form $form )
    {
        $this->required_fields = array('event_id', 'event_title', 'event_desc', 'date', 'start_time', 'end_time');
        parent::setup($form);
    }
}

