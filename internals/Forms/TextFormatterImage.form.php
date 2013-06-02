<?php

class form_TextFormatterImage extends SK_Form 
{
    public function __construct()
    {
        parent::__construct( 'text_formatter_image' );
    }
    
    /**
     * @see SK_Form::setup()
     *
     */
    public function setup() 
    {       
        $this->registerField( new TF_ImageField() );
        
        $this->registerField(new fieldType_text('label'));
        
        $this->registerField(new fieldType_hidden('entity'));
        
        $this->registerAction( new TF_ImageFormAction() );
    }

}

class TF_ImageField extends fieldType_file
{
    public function __construct()
    {
        parent::__construct('file');
    }
    /**
     * @see fieldType_file::setup()
     *
     * @param SK_Form $form
     */
    public function setup ( $form )
    {
        $this->allowed_extensions = array('jpg', 'jpeg', 'gif', 'png');
        $this->max_file_size = 2048*1024;
        
        parent::setup( $form );
    }

    
    /**
     * @see fieldType_file::preview()
     *
     * @param SK_TemporaryFile $tmp_file
     */
    public function preview ( SK_TemporaryFile $tmp_file )
    {
        $file_url = $tmp_file->getURL();
        return '<div><img src="'.$file_url.'" width="100" />&nbsp;&nbsp;&nbsp;<a class="delete_file_btn" style="cursor:pointer;">Delete</a></div>';
        
    }
}

class TF_ImageFormAction extends SK_FormAction
{
    public function __construct()
    {
        parent::__construct( 'add' );
    }
    
    public function setup(SK_Form $form)
    {
        $this->required_fields = array('file');
        
        parent::setup($form);
    }
    
    /**
     * @see SK_FormAction::process()
     *
     * @param array $data
     * @param SK_FormResponse $response
     * @param SK_Form $form
     */
    public function process( array $data, SK_FormResponse $response, SK_Form $form )
    {   
        $file = new SK_TemporaryFile($data['file']);
        
        $label = empty($data['label']) ? $file->getFileName() : $data['label'];  
        $dto = app_TextFormatter::addImage(SK_HttpUser::profile_id(), $data['entity'], $file, $label);
        if (!$dto)
        {
            return false;
        }
        
        return array('src' => app_TextFormatter::getImageUrl($dto), 'entity' => $dto->entity, 'label' => $dto->label );
    }
}

