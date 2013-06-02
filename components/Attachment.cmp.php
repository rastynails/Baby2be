<?php

class component_Attachment extends SK_Component
{
    private $entity;
    private $entityId;
    private $entityOwnerId;
    
    public function __construct($params)
    {
        parent::__construct('attachment');
        
        $this->entity = $params['entity'];
        $this->entityId = $params['entityId'];
        $this->entityOwnerId = $params['owner'];     
    }
    
    public function prepare(SK_Layout $layout, SK_Frontend $frontend)
    {
        $layout->assign('permissionMode', SK_HttpUser::profile_id() == $this->entityOwnerId || SK_HttpUser::isModerator());
        
        $list = app_Attachment::getList($this->entity, $this->entityId);
        if (empty($list))
        {
            return false;
        }
        
        $tplList = array();
        foreach ($list as $item)
        {
            $tplList[] = array(
                'id' => $item->id,
                'label' => $item->label,
                'url' => app_Attachment::getImageUrl($item),
                'size' => round($item->size / 1024, 2)
            );
        }
        
        $layout->assign('list', $tplList);
        
        $this->frontend_handler = new SK_ComponentFrontendHandler('Attachment');
        $this->frontend_handler->construct();
    }
    
    public static function ajax_Delete(SK_HttpRequestParams $params, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response)
    {
        $id = (int) $params->id;
        app_Attachment::delete($id);
    }
}