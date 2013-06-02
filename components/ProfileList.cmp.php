<?php

abstract class component_ProfileList extends SK_Component
{
    protected $enable_pagging = true;
    protected $change_view_mode = true;
    /**
     * Array of List Items
     *
     * @var string
     */
    protected $list = array();
    /**
     * Count of list items
     *
     * @var int
     */
    protected $total;
    /**
     * Count of pages which will display in paging
     *
     * @var int
     */
    protected $display_page_count;
    /**
     * Count of list items on page
     *
     * @var int
     */
    protected $items_on_page;
    private $no_profiles = false;
    /**
     * View Mode
     *
     * @var string
     */
    protected $view_mode = 'gallery';
    /**
     * Number of list item after which will be displaed Advertisement
     *
     * @var int
     */
    protected $ads_position;
    /**
     * Display Join Date
     *
     * @var bool
     */
    protected $display_join_date;
    public $list_name = 'profile_list';
    /**
     * Tabs
     *
     * @var array
     */
    protected $page_tabs = array();
    protected $no_permission_msg;
    private $no_permitted = false;
    protected $search_type;

    private $thumbTempData;

    /**
     * Component constructor
     *
     * @param string $list_name
     */
    public function __construct( $list_name )
    {
        if ( !$this->is_permitted() && !isset($this->no_permission_msg) )
            $this->annul();
        elseif ( isset($this->no_permission_msg) )
            $this->no_permitted = true;

        $this->list_name = $list_name;

        parent::__construct('profile_list');
        
        //$this->cache_lifetime = 0;
    }

    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'profile_list':
                $this->list = isset($value['profiles']) && is_array($value['profiles']) ? $value['profiles'] : array();
                $this->total = isset($value['total']) ? intval($value['total']) : 0;

                break;
        }
    }

    public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
    {
        if ( $this->no_permitted )
        {
            $this->tpl_file = 'no_permitted.tpl';
            $Layout->assign('no_permission_msg', $this->no_permission_msg);
            return parent::prepare($Layout, $Frontend);
        }

        $this->profile_list = $this->profiles();
        $this->no_profiles = !(bool) count($this->list);

        if ( $this->no_profiles )
        {
            $this->tpl_file = 'no_items.tpl';
            return parent::prepare($Layout, $Frontend);
        }

        $this->view_mode = app_ProfileList::getViewMode();

        $this->items_on_page = SK_Config::Section('site')->Section('additional')->Section('profile_list')->result_per_page;

        $this->display_page_count = SK_Config::Section('site')->Section('additional')->Section('profile_list')->nav_per_page;

        if ( in_array($this->view_mode, array('gallery', 'details')) )
            app_ProfileList::setViewMode($this->view_mode);
        else
            $this->view_mode = SK_Config::section('site')->Section('additional')->Section('profile_list')->view_mode;

        $this->display_join_date = SK_Config::Section('site')->Section('additional')->Section('profile_list')->display_sign_up_date;

        // Calling function for custom configuration
        $this->setup();

        switch ( $this->view_mode )
        {
            case 'gallery':
                $this->tpl_file = 'gallery.tpl';
                break;
            case 'details':
                $this->tpl_file = 'details.tpl';
                break;
        }

        $this->ads_position = round($this->items_on_page / 2) - fmod(round($this->items_on_page / 2), 3);

        $page = isset(SK_HttpRequest::$GET['page']) ? SK_HttpRequest::$GET['page'] : 1;

        $this->cache_id = $this->list_name . '|' . $page;

        parent::prepare($Layout, $Frontend);
    }

    public function render( SK_Layout $Layout )
    {
        $this->page_tabs = is_array($tabs = $this->tabs()) ? $tabs : array();

        $Layout->assign('tabs', $this->page_tabs);

        if ( $this->no_profiles || $this->no_permitted )
        {
            return parent::render($Layout);
        }

        // Prepare profile list to out
        $this->prepare_list();


        $Layout->assign('enable_pagging', $this->enable_pagging);

        if ( $this->enable_pagging )
        {
            $Layout->assign('paging', array(
                'total' => $this->total,
                'on_page' => $this->items_on_page,
                'pages' => $this->display_page_count,
            ));
        }


        $Layout->assign('list', $this->list);

        $Layout->assign('change_vm', $this->change_view_mode);

        $Layout->assign('ads_pos', $this->ads_position);

        $other_view_mode = ($this->view_mode == 'gallery') ? 'details' : 'gallery';
        $change_view_mode_url = sk_make_url($_SERVER['REQUEST_URI'], array('view_mode' => $other_view_mode));

        $view_mode = array(
            'href' => $change_view_mode_url,
            'label' => SK_Language::section('profile.list')->text('view_' . $other_view_mode),
            'class' => 'view_mode_' . $this->view_mode
        );

        $Layout->assign('view_mode', $view_mode);

        $Layout->assign('list_name', $this->list_name);
        $Layout->assign('list_total', $this->total);
        $Layout->assign('thumbInfo', $this->thumbTempData);
        return parent::render($Layout);
    }

    protected function prepare_list()
    {
        $lang_section = SK_Language::section('profile_fields')->section('value');
        $f_profile_list_section = SK_Language::section('profile_fields')->section('label_profile_list');
        $birthdate_fields = app_ProfileField::getProfileListBirthdateFields();
        $birthdate_fields_permissions = array();

        foreach ( $birthdate_fields as $field )
        {
            $birthdate_fields_permissions[$field] = SK_ProfileFields::permissions($field);
        }

        $page = app_ProfileList::getPage();
        $count = SK_Config::Section('site')->Section('additional')->Section('profile_list')->result_per_page;

        $startUserNumber = ($page - 1) * $count;
        $i = 1;

        $profileIdList = array();
        $tempDataToAssign = array();
        
        foreach( $this->list as $item )
        {
            $profileIdList[] = $item['profile_id'];
            $tempDataToAssign[$item['profile_id']] = array( 'sex' => $item['sex'], 'profile_id' => $item['profile_id'], 'membership_type_id' => $item['membership_type_id'] );
        }

        $userNames = app_Profile::getUsernamesForUsers($profileIdList);
        app_ProfilePhoto::getThumbUrlList($profileIdList);
        app_Profile::getOnlineStatusForUsers($profileIdList);
        app_ProfilePreferences::initForList($profileIdList);
        $profileFieldData = app_Profile::getFieldValuesForUsers($profileIdList, array_merge($birthdate_fields, array('country', 'state', 'city', 'zip', 'custom_location')));
        $profileStatusData = app_Profile::getUserStatusForList($profileIdList);

        //-------------------//
        if( SK_HttpUser::is_authenticated() )
        {
            app_Bookmark::initUserBlockCacheForValues(SK_HttpUser::profile_id(), $profileIdList);
            app_Bookmark::initProfileBookmarkedCache(SK_HttpUser::profile_id(), $profileIdList);
            app_FriendNetwork::initUserRelationCacheForValues(SK_HttpUser::profile_id(), $profileIdList);
            app_EventService::initSpeedDatingProfileBookmarks(SK_HttpUser::profile_id(), $profileIdList);
            app_Newsfeed::newInstance()->initCacheForFollow(SK_HttpUser::profile_id(), 'user', $profileIdList);
        }
        //-------------------//

        if ( SK_Config::Section('site')->Section('additional')->Section('profile_list')->display_photo_count )
        {
            $photoCount = app_ProfilePhoto::photo_count_by_user_id_list($profileIdList);
        }

        foreach ( $this->list as $key => $value )
        {
            $profile = &$this->list[$key];

            if ( empty($profile['profile_id']) )
            {
                continue;
            }

            $profileId = $profile['profile_id'];

            $profile['profile_label'] = $userNames[$profileId];

            try
            {
                $profile['sex_label'] = $lang_section->text('sex_' . $profile['sex']);
            }
            catch ( SK_LanguageException $e )
            {
                $profile['sex_label'] = "-";
            }

            $profile['location'] = $profileFieldData[$profileId];

            if ( !empty($profile['location']['custom_location']) )
            {
                $profile['location']['custom_location'] = trim(htmlspecialchars($profile['location']['custom_location']));
            }

            $profile['join_date'] = $this->display_join_date ? SK_I18n::getSpecFormattedDate($profile['join_stamp'], true, true) : '';

            $profile['textStatus'] = app_TextService::stCensor(SK_I18n::getHandleSmile($profileStatusData[$profileId]), 'profile');

            if ( $birthdate_fields )
            {
                $birthdate_fields_values = array();

                foreach ( $profileFieldData[$profileId] as $key => $value )
                {
                    if( in_array($key, $birthdate_fields) )
                    {
                        $birthdate_fields_values[$key] = $value;
                    }
                }

                $age_values = array();

                foreach ( $birthdate_fields_values as $age_key => $val )
                {
                    $profile_field = SK_ProfileFields::get($age_key);

                    if ( !($birthdate_fields_permissions[$age_key] & $profile['sex']) )
                    {
                        continue;
                    }

                    if ( $val && $profile_field )
                    {
                        $profile_field_id = $profile_field->profile_field_id;

                        try
                        {
                            $text = $f_profile_list_section->cdata($profile_field_id);

                            if ( !$text )
                            {
                                continue;
                            }

                            if ( strpos($text, '{') !== false )
                            {
                                $text = SK_Language::exec($text, array('value' => app_Profile::getAge($val)));
                            }

                            $age_values[$age_key] = $text;
                        }
                        catch ( Exception $ex )
                        {
                            // ignore;
                        }
                    }
                }
            }

            $profile['age'] = $age_values;

            if ( SK_Config::Section('site')->Section('additional')->Section('profile_list')->display_photo_count )
            {
                $profile['photo_count'] = $photoCount[$profileId];
            }

            if ( app_ProfilePreferences::get('my_profile', 'hide_online_activity', $profile['profile_id']) )
            {
                $profile['activity_info']['item'] = false;
            }
            else
            {
                $profile['activity_info'] = app_Profile::ActivityInfo($profile['activity_stamp'], $profile['online']);
                $profile['activity_info']['item_label'] = isset($profile['activity_info']['item']) ? SK_Language::section('profile.labels')->text('activity_' . $profile['activity_info']['item']) : false;
            }

            $url_params = array();
            
            $url_params['page'] = app_ProfileList::getPage();
            $url_params['list_name'] = $this->list_name;
            $query_uri = SK_HttpRequest::$GET;
            
            if ( is_array($query_uri) )
            {
                $url_params = array_merge( $url_params, $query_uri );
            }
            $profile['url_params'] = $url_params;

            $i++;
        }
    }

    /**
     * Returns true if the service is permitted else false
     *
     * @return bool
     */
    protected function is_permitted()
    {
        return true;
    }

    /**
     * Function which can be redeclared for assign some List to component
     *
     * @return array()
     */
    protected function setup()
    {

    }

    /**
     * Function which can be redeclared for assign some List to component
     * <li>Must return array with two elements : profiles, total
     *
     * @return array()
     */
    protected abstract function profiles();

    protected function tabs()
    {
        return array();
    }
}

