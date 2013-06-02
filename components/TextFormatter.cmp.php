<?php

class component_TextFormatter extends SK_Component
{
    const MODE_NORMAL = 'normal';
    const MODE_BBTAG = 'bbtag';

    private $targetId;
    private $mode;
    private $entity;

    private $controls = array('bold', 'italic', 'underline', 'link', 'emoticon');

    public function __construct($targetId, $entity, $controls = null)
    {
        parent::__construct('text_formatter');

        $this->targetId = $targetId;
        $this->entity = $entity;

        switch ($this->entity)
        {
            case app_TextFormatter::ENTITY_BLOG:
                $this->controls = array( 'bold', 'italic', 'underline', 'quote', 'link', 'emoticon', 'image' );
                break;
            case app_TextFormatter::ENTITY_FORUM:
                $this->controls = array( 'bold', 'italic', 'underline', 'link', 'emoticon', 'image' );
                $this->setMode(self::MODE_BBTAG);
                break;
        }

        if (!empty($controls))
        {
            $this->controls = $controls;
        }

    }

    public function setMode($mode) {
        $this->mode = $mode;
    }

    public function prepare(SK_Layout $layout, SK_Frontend $frontend)
    {
        // including js file
        $frontend->include_js_file(URL_STATIC.'jquery.selection.js');

        $this->frontend_handler = new SK_ComponentFrontendHandler('TextFormatter');
        $this->frontend_handler->construct($this->targetId, $this->mode);

        parent::prepare($layout, $frontend);
    }

    public function render(SK_Layout $layout)
    {
        $smiles = app_TextService::stGetSmiles();
        $layout->assign('smileString', implode('', $smiles));
        $layout->assign('controls', $this->controls);

        if (in_array('link', $this->controls))
        {
            SK_Layout::getInstance()->renderHiddenComponent('tf_link_suggest', new TF_Link($this->entity));
        }

        if (in_array('image', $this->controls) && $this->entity != app_TextFormatter::ENTITY_BLOG)
        {
            SK_Layout::getInstance()->renderHiddenComponent($this->auto_id . '-tf_image', new component_TextFormatterImage($this->entity, $this->auto_id));
        }

        return parent::render($layout);
    }
}

class TF_Control extends SK_Component
{
    public $controlName;

    public function __construct($controlName)
    {
        parent::__construct('text_formatter');

        $this->tpl_file = $controlName . '.tpl';
        $this->controlName = $controlName;
    }

}

class TF_Link extends TF_Control
{
    public function __construct()
    {
        parent::__construct('tf_link_suggest');

        SK_Layout::frontend_handler()->registerLanguageValue("interface.text_formatter.message.incorrect_url");
        SK_Layout::frontend_handler()->registerLanguageValue("interface.text_formatter.message.incorrect_title");
    }
}

