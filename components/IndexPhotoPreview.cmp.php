<?php

class component_IndexPhotoPreview extends SK_Component
{
    public function __construct()
    {
        parent::__construct('index_photo_preview');
    }
    
    public function render( SK_Layout $layout )
    {
        $photos = array(
            'latest' => $this->getPhoto('latest', app_ProfilePhoto::PHOTOTYPE_PREVIEW),
            'topRated' => $this->getPhoto('top_rated', app_ProfilePhoto::PHOTOTYPE_THUMB),
            'mostViewed' => $this->getPhoto('most_viewed', app_ProfilePhoto::PHOTOTYPE_THUMB),
            'mostCommented' => $this->getPhoto('most_commented', app_ProfilePhoto::PHOTOTYPE_THUMB)
        );
        
        $urls = array(
            'latest' => SK_Navigation::href("latest_photo"),
            'topRated' => SK_Navigation::href("top_rated_photo"),
            'mostViewed' => SK_Navigation::href("most_viewed_photo"),
            'mostCommented' => SK_Navigation::href("most_commented_photo")
        );
        
        $layout->assign('urls', $urls);
        
        $layout->assign('photos', $photos);
    }
    
    private function getPhoto( $list, $type )
    {
        switch ($list) {
            case 'latest':
                $list = app_PhotoList::LatestPhotos(false);
                break;
            
            case 'top_rated':
                $list = app_PhotoList::TopRated(false);
                break;
                
            case 'most_commented':
                $list = app_PhotoList::MostCommented(false);
                break;
                
            case 'most_viewed':
                $list = app_PhotoList::MostViewed(false);
                break;
        }
        
        $p = reset($list["items"]);
        if ( !$p ) 
        {
            return false;    
        }
        
        return array( 'src' => app_ProfilePhoto::getUrl($p['photo_id'], $type), 'url' => app_ProfilePhoto::getPermalink($p['photo_id']));
    }
}