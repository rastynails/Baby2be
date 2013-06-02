<?php

class component_ClsIndexCategories extends SK_Component
{
    /**
     * @var string
     */
    private $entity;
    
    private $parentGroupId;
    
    /**
     * @var array
     */ 
    private $groups;
    
    public function __construct( array $params = null )
    {
        $this->entity = $params['entity'];
        $this->parentGroupId = empty($params['parent']) ? false : (int) $params['parent'];
        
        parent::__construct('cls_index_categories');
    }
    
    /**
     * @see SK_Component::prepare()
     *
     * @param SK_Layout $Layout
     * @param SK_Frontend $Frontend
     */
    public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
    {       
        return parent::prepare($Layout, $Frontend);
    }
    
    /**
     * @see SK_Component::render()
     *
     * @param SK_Layout $Layout
     * @return unknown
     */
    public function render( SK_Layout $Layout )
    {
        $this->groups = app_ClassifiedsGroupService::stGetItemGroups( $this->entity, 0 );

        if ( !empty($this->parentGroupId) && empty($this->groups[$this->parentGroupId]['has_child']) )
        {
            return false;
        }
        
        $Layout->assign('profile_id', SK_HttpUser::profile_id());
        $Layout->assign('entity', $this->entity);
        $Layout->assign('groups_list', $this->printGroup($this->parentGroupId, empty($this->parentGroupId)));
        $Layout->assign('showButton', empty($this->parentGroupId));

        return parent::render($Layout);
    }

    
    /**
     * print list of Classifieds groups
     *
     * @param int $group_id
     * @return string
     */
    public function printGroup ( $group_id = false, $printParent = true )
    {
        if ( $group_id ) 
        {
            $group_childs = explode( ' ', $this->groups[$group_id]['has_child'] );

            foreach ( $group_childs as $child_id )
            {
                if ( !$child_id ) {
                    continue;
                }       
                if ( $this->groups[$child_id]['has_child'] ) {
                    $content .= $this->printGroup( $child_id ); 
                }
                else {
                    $content .= '<li><a href="'.SK_Navigation::href( 'classifieds_category', array('cat_id'=>$this->groups[$child_id]['group_id']) ).'">'
                        .$this->groups[$child_id]['name'].'</a> ('.$this->groups[$child_id]['items_count'].')</li>';
                }
                
                $this->groups[$group_id]['items_count'] += $this->groups[$child_id]['items_count'];         
            }
            
            
            $start = '';    
            if ($printParent)
            {   
                 $start .= '<li><a href="'.SK_Navigation::href( 'classifieds_category', array('cat_id'=>$group_id) ).'">'
                    .$this->groups[$group_id]['name'].'</a> ('.$this->groups[$group_id]['items_count'].')';
            }
                
            $start .='<ul class="classifieds_cat">';    
            $end = '</ul></li>';
            
            return $start.$content.$end;        
        }
        
            $out = '';

            foreach ( $this->groups as $group )
            {
                if ( $group['parent_id'] ) {
                    continue;
                }
                if ( $group['has_child'] )
                {
                    $group_childs = explode( ' ', $group['has_child'] );
                    $content = '';
                    foreach ( $group_childs as $child_id )
                    {
                        if ( !$child_id ) {
                            continue;
                        }
                        if ( $this->groups[$child_id]['has_child'] ) {
                            $content .= $this->printGroup( $child_id );
                        }
                        else {
                            $content .= '<li><a href="'.SK_Navigation::href( 'classifieds_category', array('cat_id'=>$this->groups[$child_id]['group_id']) ).'">'
                            .$this->groups[$child_id]['name'].'</a> ('.$this->groups[$child_id]['items_count'].')</li>';
                        }
                            
                        $this->groups[$group['group_id']]['items_count'] += $this->groups[$child_id]['items_count'];
                    }
                    $start = '<li><a href="'.SK_Navigation::href( 'classifieds_category', array('cat_id'=>$group['group_id']) ).'">'
                    .$group['name'].'</a> ('.$this->groups[$group['group_id']]['items_count'].')
                    <ul class="classifieds_cat">';

                    $end = '</ul></li>';

                    $out .= $start.$content.$end;
                }
                else {
                    $out .= '<li><a href="'.SK_Navigation::href( 'classifieds_category', array('cat_id'=>$group['group_id']) ).'">'.$group['name'].'</a> ('.$group['items_count'].')</li>';
                }
                    
            }

            return '<ul class="classifieds_cat">'.$out.'</ul>';
    
        
    }
}
