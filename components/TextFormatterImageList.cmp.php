<?php

class component_TextFormatterImageList extends SK_Component
{
    private $entity;
    
    public function __construct($params)
    {
        parent::__construct('text_formatter_image_list');
        
        $this->entity = $params['entity'];
        
        if (!empty($params['rm']))
        {
            $this->deleteImage($params['rm']);
        }
    }
    
    public function prepare(SK_Layout $layout, SK_Frontend $frontend)
    {
        $profileId = SK_HttpUser::profile_id();
        $images = app_TextFormatter::getImageList($profileId, $this->entity);
        $tplImages = array();
        foreach ($images as $image)
        {
            $tplImages[] = array(
                'id' => $image->id,
                'label' => $image->label,
                'src' => app_TextFormatter::getImageUrl($image)
            );
        }     
        
        $layout->assign('images', $tplImages);
        
        $cid = $this->getTagAutoId('image-list-c');
        $frontend->onload_js('
        $(".add_btn", "#' . $cid .'").one("click", function() {
            window.sk_components[' . json_encode($this->auto_id) . '].parent.onSelect($(this).attr("sk-image-src"), $(this).text());
        });
        
        $(".rm_btn", "#' . $cid .'").one("click", function() {
            window.sk_components[' . json_encode($this->auto_id) . '].reload({entity: ' . json_encode($this->entity) . ', rm: $(this).attr("sk-image-id")});
        });');
    }
    
    public function deleteImage($id)
    {
        app_TextFormatter::deleteImage($id);
    }
}
