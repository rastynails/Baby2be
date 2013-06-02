<?php

class component_TopRatedPhoto extends component_PhotoList 
{
    public function items()
    {
        return app_PhotoList::TopRated(true, SK_HttpRequest::$GET['sex']);
    }

    /**
     * @see component_PhotoList::tabs()
     *
     * @return unknown
     */
    public function tabs ( ) {
        if(!SK_Config::section('photo.general')->top_list_gender_separate)
            return array();
        $tabs = array();
        
        $document = SK_HttpRequest::getDocument();
        
        $tabs[] = array(
                        'href'  =>$document->url,
                        'label' =>SK_Language::text('components.top_rated_photo_list.all_sex_label'),
                        'active'=>!isset(SK_HttpRequest::$GET['sex'])
                        );
        foreach (SK_ProfileFields::get('sex')->values as $sex) 
        {
            $tabs[] = array(
                        'href'  => sk_make_url($document->url, array('sex'=>$sex)),
                        'label' => SK_Language::text(
                                        'components.top_rated_photo_list.tab_label',
                                        array(
                                            'sex'=>SK_Language::text('profile_fields.value.sex_'.$sex)
                                    )),
                        'active'=> (@SK_HttpRequest::$GET['sex']==$sex)
            );
        }
                    
        return $tabs;
    }
}
