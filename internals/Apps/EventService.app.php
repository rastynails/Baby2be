<?php
require_once DIR_APPS . 'appAux/EventDao.php';
require_once DIR_APPS . 'appAux/EventProfileDao.php';

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov 07, 2008
 *
 * Desc: Blog Feature Service Class
 */
final class app_EventService
{
    /**
     * @var EventDao
     */
    private $eventDao;

    /**
     * @var EventProfileDao
     */
    private $eventProfileDao;

    /**
     * @var array
     */
    private $configs;

    /**
     * @var BlogService
     */
    private static $classInstance;

    /**
     * Class constructor
     */
    private function __construct()
    {
        $this->eventDao = new EventDao();
        $this->eventProfileDao = new EventProfileDao();

        $conf1 = new SK_Config_Section('site');
        $conf_section1 = $conf1->Section('automode');

        $this->configs['events_on_page'] = 10000;
        $this->configs['admin_status'] = $conf_section1->get('set_active_event_on_submit');
        $this->configs['event_thumb_list_limit'] = 100;
        $this->configs['event_thumb_width'] = 330;
        $this->configs['events_on_index_cmp'] = 4;
        $this->configs['events_on_profile_page'] = 5;
    }

    /**
     * Returns the only instance of the class
     *
     * @return app_EventService
     */
    public static function newInstance()
    {
        if ( self::$classInstance === null )
            self::$classInstance = new self();
        return self::$classInstance;
    }

    /**
     * Returns service configs
     *
     * @return array
     */
    public function getConfigs()
    {
        return $this->configs;
    }

    /* !Class auxilary methods devider! */

    public function findUserPendingApprovalEvents( $userId )
    {
        return $this->eventDao->findUserPendingApprovalEvents($userId);
    }

    /**
     * Returns full info day events
     *
     * @param integer $year
     * @param integer $month
     * @param integer $day
     * @param integer $page
     * @return array
     */
    public function findDayEvents( $year, $month, $day, $page, $is_speed_dating = 0 )
    {
        $first = ( $page - 1 ) * (int) $this->configs['events_on_page'];
        $count = (int) $this->configs['events_on_page'];

        $start_ts = mktime(0, 0, 0, $month, $day, $year);
        $end_ts = mktime(23, 59, 59, $month, $day, $year);

        return $this->eventDao->findPeriodEvents($start_ts, $end_ts, $first, $count, $is_speed_dating);
    }


    /**
     * Returns full info month events
     *
     * @param integer $year
     * @param integer $month
     * @param integer $page
     * @return array
     */
    public function findMonthEvents( $year, $month, $page, $is_speed_dating = 0 )
    {
        $first = ( $page - 1 ) * (int) $this->configs['events_on_page'];
        $count = (int) $this->configs['events_on_page'];

        $start_ts = mktime(0, 0, 0, $month, 1, $year);
        $end_ts = mktime(23, 59, 59, $month, date('t', mktime(0, 0, 0, $month, 1, $year)), $year);

        return $this->eventDao->findPeriodEvents($start_ts, $end_ts, $first, $count, $is_speed_dating);
    }

    public function findPeriodEvents( $start_ts, $end_ts, $first, $count, $is_speed_dating = 0 )
    {
        return $this->eventDao->findPeriodEvents($start_ts, $end_ts, $first, $count, $is_speed_dating);
    }


    /**
     * Returns full info Current Month Events
     *
     * @param integer $page
     * @return array
     */
    public function findDefaultMonthEvents( $page, $is_speed_dating = 0 )
    {
        $first = ( $page - 1 ) * (int) $this->configs['events_on_page'];
        $count = (int) $this->configs['events_on_page'];
        $year = date('Y', SK_I18n::mktimeLocal());
        $month = date('n', SK_I18n::mktimeLocal());

        $start_ts = SK_I18n::mktimeLocal();
        $end_ts = mktime(23, 59, 59, $month, date('t', mktime(0, 0, 0, $month, 1, $year)), $year);

        return $this->eventDao->findPeriodEvents($start_ts, $end_ts, $first, $count, $is_speed_dating);
    }


    /**
     * Returns full info Current Month Events for Index Page
     *
     * @return array
     */
    public function findDefaultMonthEventsForIndex( $count )
    {
        $first = 0;

        $year = date('Y', SK_I18n::mktimeLocal());
        $month = date('n', SK_I18n::mktimeLocal());

        $start_ts = SK_I18n::mktimeLocal();
        $end_ts = mktime(23, 59, 59, $month, date('t', mktime(0, 0, 0, $month, 1, $year)), $year);

        return $this->eventDao->findPeriodEvents($start_ts, $end_ts, $first, $count);
    }


    /**
     * Returns event array for month with link flag
     *
     * @param integer $year
     * @param integer $month
     * @return array
     */
    public function getWeeksArray( $year, $month )
    {
        $event_array = $this->eventDao->findMonthEventsStartDates($year, $month); //TODO insert flags into array

        $day_array = array();

        foreach ( $event_array as $key => $value )
        {
            $startDay = date('j', $value->getStart_date());
            $startMonth = date('n', $value->getStart_date());
            $startYear = date('Y', $value->getStart_date());
            $daysCount = date('t', mktime(null, null, null, $month, 1, $year));

            for( $i = 1; $i<=$daysCount; $i++ )
            {
                $dayStart = mktime(0,0,0,$month,$i,$year);
                $dayEnd = mktime(23,59,59,$month,$i,$year);

                if( ( $dayStart <= $value->getStart_date() && $dayEnd >= $value->getEnd_date() ) || ( $dayStart >= $value->getStart_date() && $dayStart <= $value->getEnd_date() ) || ( $dayEnd >= $value->getStart_date() && $dayEnd <= $value->getEnd_date() )  )
                {
                    $day_array[] = $i;
                }
            }
        }

        $day_array = array_unique($day_array);

        $dayofmonth = date('t', mktime(0, 0, 0, $month, 1, $year));
        $day_count = 1;
        $num = 0;
        for ( $i = 0; $i < 7; $i++ )
        {
            $dayofweek = date('w', mktime(0, 0, 0, $month, $day_count, $year));
            $dayofweek = ($dayofweek == 7 ? 0 : $dayofweek);
            if ( $dayofweek == $i )
            {
                $week[$num][$i] = array('date' => $day_count, 'link' => ( in_array($day_count, $day_array) ? true : false ));
                $day_count++;
            }            else
            {
                $week[$num][$i] = array();
            }
        }
        while ( true )
        {
            $num++;
            for ( $i = 0; $i < 7; $i++ )
            {
                $week[$num][$i] = array('date' => $day_count, 'link' => ( in_array($day_count, $day_array) ? true : false ));
                $day_count++;
                if ( $day_count > $dayofmonth )
                    break;
            }
            if ( $day_count > $dayofmonth )
                break;
        }
        return $week;

    }

    /**
     * Saves event item
     *
     * @param Event $event
     */
    public function saveEvent( Event $event )
    {
        if ( ( $this->configs['admin_status'] || SK_HttpUser::isModerator()) && !$event->getId() )
        {
            $event->setAdmin_status(1);
        }

        $event->setTitle(htmlspecialchars($event->getTitle()));
        $event->setAddress(htmlspecialchars($event->getAddress()));

        $this->eventDao->saveOrUpdate($event);
    }


    /**
     * Updates event item
     *
     * @param Event $event
     */
    public function updateEvent( Event $event )
    {
        $event->setTitle(htmlspecialchars($event->getTitle()));
        $event->setAddress(htmlspecialchars($event->getAddress()));

        $this->eventDao->saveOrUpdate($event);
    }

    private static $eventsCache = array();

    /**
     * Returns full event info
     *
     * @param integer $event_id
     * @return array
     */
    public function findEventInfo( $event_id )
    {
        $event_id = intval($event_id);

        if( $event_id < 1 )
        {
            return array();
        }

        if( !isset(self::$eventsCache[$event_id]) )
        {
            self::$eventsCache[$event_id] = $this->eventDao->findEventFullInfo($event_id);
        }

        return self::$eventsCache[$event_id];
    }

    /**
     * Rteurns Event object by  event_id
     *
     * @param integer $event_id
     * @return Event
     */
    public function findById( $event_id )
    {
        return $this->eventDao->findById($event_id);
    }


    /**
     * Saves EventProfile item
     *
     * @param EventProfile $event_profile
     */
    public function saveOrUpdateEventProfile( EventProfile $event_profile )
    {
        $this->eventProfileDao->saveOrUpdate($event_profile);
    }


    /**
     * Deletes all EventProfile entires for event
     *
     * @param integer $event_id
     */
    public function deleteEventProfileByEventId( $event_id )
    {
        $this->eventProfileDao->deleteEventProfileByEventId($event_id);
    }

    /**
     * Deletes EventProfile entry by entry_id
     *
     * @param integer $id
     */
    public function deleteEventProfileById( $id )
    {
        $this->eventProfileDao->deleteById($id);
    }


    /**
     * Deletes EventProfile entry by event_id and profiel_id
     *
     * @param integer $event_id
     * @param integer $profile_id
     */
    public function deleteEventProfileByEventIdAndProfileId( $event_id, $profile_id )
    {
        $this->eventProfileDao->deleteEventProfileByEventIdAndProfileId($event_id, $profile_id);
    }


    /**
     * Returns array with profile_id attanding event
     *
     * @param integer $event_id
     */
    public function findAttendingProfileIdsForEvent( $event_id, $limit = false )
    {
        if ( $limit )
            $limit = $this->configs['event_thumb_list_limit'];

        $dto_array = $this->eventProfileDao->findProfileIdsForEvent($event_id, 1, $limit);

        $return_array = array();

        foreach ( $dto_array as $value )
        {
            $return_array[] = array('id' => $value['dto']->getProfile_id(), 'username' => $value['username']);
        }

        return $return_array;
    }


    /**
     * Returns array with profile_id NOT attanding event
     *
     * @param integer $event_id
     */
    public function findNotAttendingProfileIdsForEvent( $event_id, $limit = false )
    {
        if ( $limit )
            $limit = $this->configs['event_thumb_list_limit'];

        $dto_array = $this->eventProfileDao->findProfileIdsForEvent($event_id, 0, $limit);

        $return_array = array();

        foreach ( $dto_array as $value )
        {
            $return_array[] = array('id' => $value['dto']->getProfile_id(), 'username' => $value['username']);
        }

        return $return_array;
    }


    /**
     * Returns count of Attending profiles for Event
     *
     * @param integer $event_id
     * @return integer
     */
    public function findAttendingProfilesCount( $event_id )
    {
        return $this->eventProfileDao->findProfilesCountForEvent($event_id, 1);
    }


    /**
     * Returns count of NOT Attending profiles for Event
     *
     * @param integer $event_id
     * @return integer
     */
    public function findNotAttendingProfilesCount( $event_id )
    {
        return $this->eventProfileDao->findProfilesCountForEvent($event_id, 0);
    }

    /**
     * Returns entry by event and profile Ids
     *
     * @param integer $event_id
     * @param integer $profile_id
     * @return EventProfile
     */
    public function findEventProfileEntry( $event_id, $profile_id )
    {
        return $this->eventProfileDao->findEventProfileByEventIdAndProfileId($event_id, $profile_id);
    }


    /**
     * Saves event image and returns image URL
     *
     * @param integer $event_id
     * @param string $file_code
     * @return string
     */
    public function saveEventImage( $event_id, $tmp_file )
    {
        $temp_file_name = rand() . '_file.' . $tmp_file->getExtension();

        $tmp_file->move($this->getEventImagePath($temp_file_name));

        $return_name = 'event_' . $event_id . '.' . $tmp_file->getExtension();

        app_Image::resize($this->getEventImagePath($temp_file_name), $this->configs['event_thumb_width'], 10000, false, $this->getEventImagePath($return_name));

        $temp_jpg = 'event_icon_' . $event_id . '.jpg';

        app_Image::convert($this->getEventImagePath($temp_file_name), IMAGETYPE_JPEG, $this->getEventImagePath($temp_jpg));

        app_Image::resize($this->getEventImagePath($temp_jpg), 60, 60, true, $this->getEventImagePath('event_icon_' . $event_id . '.jpg'));

        if ( file_exists($this->getEventImagePath($temp_file_name)) )
            unlink($this->getEventImagePath($temp_file_name));

        return $return_name;
    }


    /**
     * Deletes Event Image
     *
     * @param string $file_name
     */
    public function deleteEventImage( Event $event )
    {
        if ( $event->getImage() === null )
            return;

        unlink($this->getEventImagePath($event->getImage()));

        unlink($this->getEventImagePath('event_icon_' . $event->getId() . '.jpg'));

        $event->setImage(null);

        $this->saveEvent($event);

        return;
    }


    /**
     * Returns event default image URL
     *
     * @return string
     */
    public function getEventDefaultImageURL()
    {
        return URL_LAYOUT . SK_Layout::theme_dir(true) . 'img/event.jpg';
    }

    /**
     * Returns path of Neo
     *
     * @param string $image_name
     * @return string
     */
    public function getEventImagePath( $image_name )
    {
        return DIR_USERFILES . $image_name;
    }


    /**
     * Raturns image url
     *
     * @param string $image_name
     * @return string
     */
    public function getEventImageURL( $image_name )
    {
        return URL_USERFILES . $image_name;
    }


    /**
     * Checks if Event id expired
     *
     * @param Event $event
     * @return boolean
     */
    public function isEventExpired( Event $event )
    {
        return ( $event->getEnd_date() <= time() ) ? true : false;
    }

    public function findProfileEvents( $profile_id )
    {
        return $this->eventDao->findProfileEvents($profile_id, $this->configs['events_on_profile_page']);
    }

    /**
     * Returns events count profile joined
     *
     * @param integer $profile_id
     * @return integer
     */
    public function findProfileEventsCount( $profile_id )
    {
        return $this->eventDao->findProfileEventsCount($profile_id);
    }


    /**
     * Deletes event by id
     *
     * @param integer $event_id
     * @return boolean
     */
    public function deleteEventById( $event_id )
    {
        $event = $this->eventDao->findById($event_id);

        if ( $event === null )            return false;

        $this->deleteEvent($event);

        //LActivity
        app_UserActivities::deleteActivities($event_id, 'event_add');
        app_UserActivities::deleteActivities($event_id, 'event_comment');
        //~~

        return true;
    }


    /**
     * Deletes event
     *
     * @param Event $event
     */
    public function deleteEvent( Event $event )
    {
        $this->deleteEventImage($event);
        app_CommentService::stDeleteEntityComments(FEATURE_EVENT, $event->getId(), ENTITY_TYPE_EVENT_ADD);
        app_CommentService::stDeleteEntityComments(FEATURE_EVENT, $event->getId(), ENTITY_TYPE_EVENT_ATTEND);
        app_TagService::stUnlinkAllTags(FEATURE_EVENT, $event->getId());

        $eventProfiles = $this->eventProfileDao->findProfileIdsForEvent($event->getId(), 1, false);

        if( $event->getEnd_date() > time() )
        {
            foreach ($eventProfiles as $value)
            {
                if ( !app_Unsubscribe::isProfileUnsubscribed($value['dto']->getProfile_id()) )
                {
                    /* @var $value EventProfile */
                    $msg = app_Mail::createMessage();
                    $msg->setTpl('event_delete');
                    $msg->setRecipientProfileId($value['dto']->getProfile_id());
                    $msg->assignVarRange( array('event_name'=>$event->getTitle()) );

                    app_Mail::send($msg);
                }
            }
        }

        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_EVENT_SPEED_DATING . "` WHERE `event_id`=?", $event->getId());
        SK_MySQL::query($query);
        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_EVENT_SPEED_DATING_PROFILE . "` WHERE `event_id`=?", $event->getId());
        SK_MySQL::query($query);

        $this->eventProfileDao->deleteEventEntries($event->getId());
        $this->eventDao->deleteById($event->getId());

        //LActivity
        app_UserActivities::deleteActivities($event->getId(), 'event_add');
        app_UserActivities::deleteActivities($event->getId(), 'event_comment');
        //~~

        //Newsfeed
        if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
        {
            app_Newsfeed::newInstance()->removeAction('event_add', $event->getId());
        }
    }

    /**
     * Deletes profile events
     *
     * @param integer $profile_id
     */
    public function deleteProfileEvents( $profile_id )
    {
//		$events = $this->eventDao->findProfileEvents( $profile_id, 10000 );
//
//		foreach ( $events as $value )
//			$this->deleteEvent( $value );
    }

    public function findEventsToModerate( $page )
    {
        $first = ( $page - 1 ) * (int) $this->configs['events_on_page'];
        $count = (int) $this->configs['events_on_page'];

        return $this->eventDao->findEventsToModerate($first, $count);
    }

    public function findEventsToModerateCount()
    {
        return $this->eventDao->findEventsToModerateCount();
    }

    public function findAllActiveEvents()
    {
        return $this->eventDao->findAllActiveEvents();
    }


    public function findSpeedDatingEventForProfile( $current_timestamp, $profile_id )
    {
        return $this->eventDao->findSpeedDatingEventForProfile($current_timestamp, $profile_id);
    }

    public function findSpeedDatingEventProfileMatches( $event_id, $profile_id, array $drawn_opponents, $search_by_location )
    {
        return $this->eventDao->findSpeedDatingEventProfileMatches($event_id, $profile_id, $drawn_opponents, $search_by_location);
    }

    public function findSpeedDatingEventInvitation( $event_id, $profile_id, $drawn_opponents )
    {
        return $this->eventDao->findSpeedDatingEventInvitation($event_id, $profile_id, $drawn_opponents);
    }

    public function speedDatingSearchByLocation( $event_id )
    {
        return $this->eventDao->speedDatingSearchByLocation($event_id);
    }

    public function addDatingSession( $event_id, $profile_id )
    {
        $this->eventDao->addDatingSession($event_id, $profile_id);
    }

    public function updateDatingSession( $event_id, $profile_id, $is_free )
    {
        return $this->eventDao->updateDatingSession($event_id, $profile_id, $is_free);
    }

    public function addDatingSessionOpponent( $event_id, $profile_id, $opponent_id, $start_time, $end_time )
    {
        return $this->eventDao->addDatingSessionOpponent($event_id, $profile_id, $opponent_id, $start_time, $end_time);
    }

    public function truncateSpeedDatingEvent( $profile_id )
    {
        $this->eventDao->truncateSpeedDatingEvent($profile_id);
    }

    public function getEventSpeedDatingSessionEndTime( $event_id, $profile_id, $opponent_id )
    {
        return $this->eventDao->getEventSpeedDatingSessionEndTime($event_id, $profile_id, $opponent_id);
    }

    public function stopEventSpeedDatingSession( $event_id, $profile_id, $opponent_id )
    {
        return $this->eventDao->stopEventSpeedDatingSession($event_id, $profile_id, $opponent_id);
    }

    /** ---------------------------- static interface -------------------------------------- * */


    public static function stFindEventInfo( $event_id )
    {
        $service = self::newInstance();
        return $service->findEventInfo($event_id);
    }

    /**
     * Returns events count profile joined
     *
     * @param integer $profile_id
     * @return integer
     */
    public static function stFindProfileEventsCount( $profile_id )
    {
        $service = self::newInstance();

        return $service->findProfileEventsCount($profile_id);
    }


    /**
     * Deletes profile events
     *
     * @param integer $profile_id
     */
    public static function stDeleteProfileEvents( $profile_id )
    {
        $service = self::newInstance();

        $service->deleteProfileEvents($profile_id);
    }

    public static function stFindEventsToModerateCount()
    {
        $service = self::newInstance();

        return $service->findEventsToModerateCount();
    }

    /**
     * Find active speed dating event
     *
     * @param integer $profile_id
     */
    public static function stFindSpeedDatingEventForProfile( $profile_id )
    {
        $service = self::newInstance();
        return $service->findSpeedDatingEventForProfile(SK_I18n::mktimeLocal(), $profile_id);
    }


    /**
     * Find match for profile in current active speed dating event
     *
     * @param integer $event_id
     * @param integer $profile_id
     * @param array $drawn_opponents
     * @return integer
     */
    public static function stFindSpeedDatingEventProfileMatches( $event_id, $profile_id, array $drawn_opponents, $search_by_location )
    {
        $service = self::newInstance();
        return $service->findSpeedDatingEventProfileMatches($event_id, $profile_id, $drawn_opponents, $search_by_location);
    }

    /**
     * Find invitation to private session in active speed dating event for profile
     *
     * @param integer $event_id
     * @param integer $profile_id
     * @param array $drawn_opponents
     * @return integer
     */
    public static function stFindSpeedDatingEventInvitation( $event_id, $profile_id, array $drawn_opponents )
    {
        $service = self::newInstance();
        return $service->findSpeedDatingEventInvitation($event_id, $profile_id, $drawn_opponents);
    }

    public static function stSpeedDatingSearchByLocation( $event_id )
    {
        $service = self::newInstance();
        return $service->speedDatingSearchByLocation($event_id);
    }

    /**
     * Add private session of profile in current speed dating event
     *
     * @param integer $event_id
     * @param integer $profile_id
     */
    public static function stAddDatingSession( $event_id, $profile_id )
    {
        $service = self::newInstance();
        $service->addDatingSession($event_id, $profile_id);
    }

    /**
     *  Update status of profile in current speed dating event
     *
     * @param integer $event_id
     * @param integer $profile_id
     * @param boolean $is_free
     */
    public static function stUpdateDatingSession( $event_id, $profile_id, $is_free )
    {
        $service = self::newInstance();
        $service->updateDatingSession($event_id, $profile_id, $is_free);
    }

    /**
     * Update status of profiles in private session
     *
     * @param integer $event_id
     * @param integer $profile_id
     * @param integer $opponent_id
     * @param integer $start_time
     * @param integer $end_time
     *
     */
    public static function stAddDatingSessionOpponents( $event_id, $profile_id, $opponent_id, $start_time, $end_time )
    {
        $service = self::newInstance();
        $service->addDatingSessionOpponent($event_id, $profile_id, $opponent_id, $start_time, $end_time);
        $service->addDatingSessionOpponent($event_id, $opponent_id, $profile_id, $start_time, $end_time);

        $service->updateDatingSession($event_id, $profile_id, 0);
        $service->updateDatingSession($event_id, $opponent_id, 0);
    }


    /**
     * Return profile's private session end time in current speed dating event
     *
     * @param integer $event_id
     * @param integer $profile_id
     * @param integer $opponent_id
     * @return integer
     */
    public static function stGetEventSpeedDatingSessionEndTime( $event_id, $profile_id, $opponent_id )
    {
        $service = self::newInstance();
        return $service->getEventSpeedDatingSessionEndTime($event_id, $profile_id, $opponent_id);
    }

    /**
     * Stop private session of profile and his opponent in current speed dating event
     *
     * @param integer $event_id
     * @param integer $profile_id
     * @param integer $opponent_id
     */
    public static function stStopEventSpeedDatingSession( $event_id, $profile_id, $opponent_id )
    {
        $service = self::newInstance();
        $service->stopEventSpeedDatingSession($event_id, $profile_id, $opponent_id);
        $service->stopEventSpeedDatingSession($event_id, $opponent_id, $profile_id);
    }

    /**
     * Clean profile's info about previous speed dating events
     *
     * @param integer $profile_id
     */
    public static function stTruncateSpeedDatingEvent( $profile_id )
    {
        $service = self::newInstance();
        $service->truncateSpeedDatingEvent($profile_id);
    }

    /**
     * Add profile in speed dating event bookmark list
     *
     * @param integet $event_id
     * @param integer $profile_id
     * @param integer $bookmark_profile_id
     * @return boolean
     */
    public static function BookmarkSpeedDatingProfile( $event_id, $profile_id, $bookmark_profile_id )
    {
        if ( !($event_id = intval($event_id)) || !($profile_id = intval($profile_id)) || !($bookmark_profile_id = intval($bookmark_profile_id)) )
            return false;

        $query = SK_MySQL::placeholder("UPDATE `" . TBL_EVENT_SPEED_DATING_PROFILE . "` SET `is_bookmarked`=1 WHERE `event_id`=? AND `profile_id`=? AND `opponent_profile_id`=? ",
                $event_id, $profile_id, $bookmark_profile_id);

        return (bool) ( MySQL::affectedRows($query) );
        //return true;

    }


    /**
     * Deletes profile from speed dating bookmark list.
     * Returns boolean - result of the query OR error code : -1 - incorrect parameters.
     *
     * @param integer $profile_id
     * @param integer $unbookmark_profile_id
     * @return boolean|integer
     */
    public static function UnbookmarkSpeedDatingProfile( $profile_id, $unbookmark_profile_id )
    {
        if ( !($profile_id = intval($profile_id)) || !($unbookmark_profile_id = intval($unbookmark_profile_id)) )
            return false;

        SK_MySQL::query(SK_MySQL::placeholder(
                    "UPDATE `" . TBL_EVENT_SPEED_DATING_PROFILE . "` SET `is_bookmarked`=0
							WHERE `profile_id`=? AND `opponent_profile_id`=?", $profile_id, $unbookmark_profile_id));
        return (bool) SK_MySQL::affected_rows();
    }

    /**
     * Checks if second profile is in the speed dating bookmark list of the first profile.
     *
     * @param integer $profile_id
     * @param integer $bookmark_profile_id
     * @return boolean
     */
    public static function isSpeedDatingProfileBookmarked( $profile_id, $bookmark_profile_id )
    {
        if ( !($profile_id = intval($profile_id)) || !($bookmark_profile_id = intval($bookmark_profile_id)) )
            return false;

        if( isset(self::$speedDatingBookmarksCache[$profile_id.'_'.$bookmark_profile_id]))
        {
            return self::$speedDatingBookmarksCache[$profile_id.'_'.$bookmark_profile_id];
        }

        $query = SK_MySQL::placeholder("SELECT `is_bookmarked` FROM `" . TBL_EVENT_SPEED_DATING_PROFILE . "`
			WHERE `profile_id`=? AND `opponent_profile_id`=? AND `is_bookmarked`=1", $profile_id, $bookmark_profile_id);

        return ( SK_MySQL::query($query)->fetch_cell() ) ? true : false;
    }

    private static $speedDatingBookmarksCache = array();

    public static function initSpeedDatingProfileBookmarks( $profile_id, array $bookmark_profile_id_list )
    {
        if ( !($profile_id = intval($profile_id)) || empty($bookmark_profile_id_list) )
            return false;

        foreach ( $bookmark_profile_id_list as $id )
        {
            self::$speedDatingBookmarksCache[$profile_id.'_'.$id] = false;
        }

        $query = SK_MySQL::placeholder("SELECT `profile_id`, `opponent_profile_id` FROM `" . TBL_EVENT_SPEED_DATING_PROFILE . "`
			WHERE `profile_id`=? AND `opponent_profile_id` IN (?@) AND `is_bookmarked`=1", $profile_id, $bookmark_profile_id_list);

        $result = SK_MySQL::queryForList($query);

        foreach ( $result as $item )
        {
            self::$speedDatingBookmarksCache[$item['profile_id'].'_'.$item['opponent_profile_id']] = true;
        }
    }

    /**
     * Return Speed dating bookmark list
     *
     * @param integer $profile_id
     * @param integer $event_id
     * @return array
     */
    public static function BookmarkSpeedDatingList( $profile_id, $event_id = '' )
    {
        $page = app_ProfileList::getPage();

        $config = SK_Config::section( 'site.additional.profile_list' );
        // get numbers on page:
        $result_per_page = $config->result_per_page;

        $query_parts['limit'] = SK_MySQL::placeholder("LIMIT ?, ?", $result_per_page * ($page - 1), $result_per_page);

        $query_parts['projection'] = "`h`.*,`p`.*,`pe`.*,`online`.`hash` AS `online`, `pn`.`note_text` AS `note` ";

        $query_parts['left_join'] = "
				INNER JOIN `" . TBL_PROFILE . "` AS `p` ON( `h`.`opponent_profile_id`=`p`.`profile_id` )
				INNER JOIN `" . TBL_PROFILE_EXTEND . "` AS `pe` ON( `h`.`opponent_profile_id`=`pe`.`profile_id`)
				LEFT JOIN `" . TBL_PROFILE_NOTE . "` AS `pn` ON( `h`.`profile_id`=`pn`.`by_profile` AND `h`.`opponent_profile_id`=`pn`.`on_profile`)
				LEFT JOIN `" . TBL_PROFILE_ONLINE . "` AS `online` ON( `h`.`opponent_profile_id`=`online`.`profile_id` )
			";

        $event_id = !empty($event_id) ? " AND `h`.`event_id`=" . $event_id . " " : "";

        $query_parts['condition'] = SK_MySQL::placeholder(
                "`h`.`profile_id`=? " . $event_id . " AND `h`.`is_bookmarked`=1 AND " . app_Profile::SqlActiveString('p')
                , $profile_id);

        $query_parts['order'] = "";

        if ( empty($event_id) )
        {
            $query_parts['group'] = "`p`.`profile_id`";
        }


        foreach ( explode("|", SK_Config::Section('site')->Section('additional')->Section('profile_list')->order) as $val )
        {
            if ( in_array($val, array('', 'none')) )
                continue;

            app_ProfileList::_configureOrder($query_parts, $val, "p");
        }

        $sex_condition = '';

        if ( $config->display_only_looking_for )
        {
            $match_sex = app_Profile::getFieldValues(SK_HttpUser::profile_id(), 'match_sex');

            $sex_condition = !empty( $match_sex ) ? " AND `p`.`sex` & " . $match_sex . " " : '';
        }

        $gender_exclusion = '';

        if ( $config->gender_exclusion )
        {
            $gender_exclusion = ' AND `p`.`sex` != ' . app_Profile::getFieldValues( $profile_id, 'sex' );
        }

        $query = "SELECT {$query_parts['projection']} FROM `" . TBL_EVENT_SPEED_DATING_PROFILE . "` AS `h`
			{$query_parts['left_join']}
			WHERE {$query_parts['condition']} $sex_condition $gender_exclusion " .
            ( isset($query_parts['group']) && (strlen($query_parts['group'])) ? " GROUP BY {$query_parts['group']}" : "" ) .
            ( (strlen($query_parts['order'])) ? " ORDER BY {$query_parts['order']}" : "" ) .
            " {$query_parts['limit']}";

        $query_result = SK_MySQL::query($query);

        while ( $item = $query_result->fetch_assoc() )
            $result['profiles'][] = $item;


        $profile_id = intval($profile_id);

        $query = SK_MySQL::placeholder("SELECT COUNT(DISTINCT `h`.`opponent_profile_id`)
								FROM `" . TBL_EVENT_SPEED_DATING_PROFILE . "` AS `h`
								INNER JOIN `" . TBL_PROFILE . "` AS `p` ON( `h`.`opponent_profile_id`=`p`.`profile_id` )
								WHERE `h`.`profile_id`=? AND `h`.`is_bookmarked`=1  AND " . app_Profile::SqlActiveString('p') . $gender_exclusion, $profile_id);
        $result['total'] = SK_MySQL::query($query)->fetch_cell();

        return $result;
    }

    /**
     * Send notification about upcoming speed dating events to all attendees
     *
     * @static
     * @return void
     */
    public static function sendSpeedDatingNotifications()
    {
        $service = self::newInstance();
        $year = date("y", time());
        $month = date("m", time());
        $day = date("d", time() + 86400);
        $events = $service->findDayEvents($year, $month, $day, 1, 1);

        $idListToUpdate = array();

        if ( !empty($events) )
        {
            foreach ( $events as $event )
            {
                if ( $event['dto']->getNotified() )
                {
                    continue;
                }
                $sent = true;
                $eventProfiles = $service->findAttendingProfileIdsForEvent($event['dto']->getId());

                foreach ( $eventProfiles as $profile )
                {
                    if (app_Profile::isProfileDeleted($profile['id']))
                    {
                        continue;
                    }
                    $attendees = "";
                    foreach ( $eventProfiles as $attendee )
                    {
                        if (app_Profile::isProfileDeleted($attendee['id']))
                        {
                            continue;
                        }
                        if ( $profile['id'] == $attendee['id'] )
                        {
                            continue;
                        }

                        $url = app_Profile::getUrl($attendee['id']);
                        $attendees .= "<a href='{$url}'>" . $attendee['username'] . "</a>, ";
                    }
                    $attendees = rtrim($attendees, ", ");

                    $msg = app_Mail::createMessage(app_Mail::NOTIFICATION)
                            ->setRecipientProfileId($profile['id'])
                            ->setTpl('event_speed_dating_notification')
                            ->assignVarRange(array(
                                'username' => $profile['username'],
                                'event_title' => $event['dto']->getTitle(),
                                'description' => $event['dto']->getDescription(),
                                'start_date' => SK_I18n::getSpecFormattedDate($event['dto']->getStart_date(), false, true),
                                'attendees' => $attendees
                            ));

                    $sent = $sent && app_Mail::send($msg);
                }

                if ( $sent )
                {
                    $idListToUpdate[] = $event['dto']->getId();
                }
            }

            if( !empty ($idListToUpdate) )
            {
                SK_MySQL::query(SK_MySQL::placeholder("UPDATE `" . TBL_EVENT . "` SET `notified`=1 WHERE `id` IN (?@)", $idListToUpdate));
            }
        }
    }

    public function deleteProfileAttendance( $profile_id )
    {
        $this->eventProfileDao->deleteByProfileId((int)$profile_id);
    }
}