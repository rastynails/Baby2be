<?php

class component_Rss extends SK_Component
{
    protected $url;
    protected $count;
    protected $title;
    protected $cacheTime = 3600;
    protected $block = true;
    protected $length = 500;
    protected $type = 'full';

    public function __construct($params = array())
    {
        parent::__construct('rss');

        $this->url = empty($params['url']) ? null : $params['url'];
        $this->length = empty($params['length']) ? $this->length : (int) $params['length'];
        $this->count = empty($params['count']) ? null : (int) $params['count'];
        $this->title = empty($params['title']) ? SK_Language::text('components.rss.title') : $params['title'];

        if (isset($params['type']))
        {
            $this->type = $params['type'] == 'short' ? 'short' : 'full';
        }

        $this->cacheTime = empty($params['cacheTime']) ? $this->cacheTime : $params['cacheTime'];

        if (isset($params['block']))
        {
            $this->block = !empty($params['block']);
        }
    }

    public function render(SK_Layout $layout)
    {
        $rss = $this->getRss();

        foreach ($rss as &$item)
        {
            if (function_exists('mb_substr'))
            {
                $desc = mb_substr($item['description'], 0, $this->length);
                $desc .= mb_strlen($item['description']) > $this->length ? '...' : '';
            }
            else
            {
                $desc = substr($item['description'], 0, $this->length);
                $desc .= strlen($item['description']) > $this->length ? '...' : '';
            }

            $item['description'] = $desc;
        }

        $layout->assign('rss', $rss);

        $layout->assign('box', array(
            'block' => $this->block,
            'title' => $this->title,
            'type' => $this->type
        ));

        return parent::render($layout);
    }

    protected function getRss()
    {
        if (empty($this->url))
        {
            return array();
        }

        $cahceKey = md5($this->url . '-count-' . $this->count);

        $cahce = SK_Cache::get($cahceKey);
        if ($cahce)
        {
            return json_decode($cahce, true);
        }

        $rss = app_Rss::read($this->url, $this->count);

        if ($rss)
        {
            SK_Cache::set($cahceKey, json_encode($rss), $this->cacheTime);
        }

        return $rss;
    }

    private function truncate()
    {

    }
}