<?php

class component_TaggedPhotoList extends component_PhotoList 
{
    public function items()
    {
        return app_PhotoList::TaggedPhotoList(SK_HttpRequest::$GET['tag']);
    }

}
