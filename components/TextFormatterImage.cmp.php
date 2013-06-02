<?php
class component_TextFormatterImage extends SK_Component
{
    private $entity;
    
    public function __construct($entity)
    {
        parent::__construct('text_formatter_image');
        
        $this->tfId = $tfId;
        $this->entity = $entity;
    }
    
    public function render(SK_Layout $layout)
    {
        $layout->assign('imageList', new component_TextFormatterImageList(array('entity' => $this->entity)));
    }
    
    public function prepare(SK_Layout $layout, SK_Frontend $frontend)
    {
        $this->frontend_handler = new SK_ComponentFrontendHandler('TextFormatterImage');
        $this->frontend_handler->construct();
    }
    
    public function handleForm(SK_Form $form)
    {
        $form->getField('entity')->setValue($this->entity);
        
        $form->frontend_handler->bind("success", 'function(data){
            var owner = this.ownerComponent;
            $("a.delete_file_btn").click();

            this.$form.get(0).reset();
            this.ownerComponent.children[0].reload({entity: data.entity});
        }');
    }
}