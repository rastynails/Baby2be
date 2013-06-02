<?php

class component_OnlineListLocationMap extends component_ProfileListLocationMap
{
    protected function profiles()
	{
        app_ProfileList::setLimit(100, 0);
        $list = app_ProfileList::OnlineList();
        app_ProfileList::unsetLimit();
        return $list;
    }
}