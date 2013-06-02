<?php

class form_RssWidget extends SK_Form
{
    /**
     * Class constructor
     *
     */
    public function __construct( )
    {
        parent::__construct( 'rss_widget' );
    }

    /**
     * @see SK_Form::setup()
     *
     */
    public function setup()
    {

        $this->registerField( new field_url('url') );
        $this->registerField( new fieldType_text('title') );
        $this->registerField( new fieldType_hidden('cmp_id') );
        $this->registerField( new fieldType_checkbox('showDesc') );

        $count = new field_integer('count');
        $count->setSize(2);
        $this->registerField( $count );

        $this->registerAction( new form_RssWidget_action() );
    }

}

class form_RssWidget_action extends SK_FormAction
{
    public function __construct()
    {
        parent::__construct('save');
    }

    public function setup($form)
    {
        $this->required_fields = array('url', 'title');

        parent::setup($form);
    }

    public function process( array $data, SK_FormResponse $response, SK_Form $form )
    {
        $widgetDto = app_RssWidget::find($data['cmp_id']);

        if( !$widgetDto )
        {
            $widgetDto = new stdClass();
        }

        $widgetDto->cmpId = (int) $data['cmp_id'];
        $widgetDto->title = trim($data['title']);
        $widgetDto->url = trim($data['url']);
        $widgetDto->count = (int) $data['count'];
        $widgetDto->showDesc = (bool) $data['showDesc'];

        app_RssWidget::save($widgetDto);
    }
}



