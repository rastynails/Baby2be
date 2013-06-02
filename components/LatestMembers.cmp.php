<?php

class component_LatestMembers extends component_ProfileList
{

    public function __construct()
    {
        parent::__construct('latest_members');
    }

    protected function profiles()
    {
        $sex = SK_HttpRequest::$GET['sex'];

        $page = app_ProfileList::getPage();
        $count = SK_Config::Section('site')->Section('additional')->Section('profile_list')->result_per_page;
        $offset = $count * ( $page - 1 );
        $list = app_ProfileList::findLatestList($offset, $count, $sex);

        return $list;
    }

    protected function is_permitted()
    {
        return true;
    }

}