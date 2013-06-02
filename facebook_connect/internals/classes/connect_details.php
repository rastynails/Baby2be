<?php

class FBC_ConnectDetails
{
    public $apiKey;
    public $apiSecret;
    public $appId;
    public $xdReceiverUrl;

    private $baceFbUrl = 'connect.facebook.net';
    public $jsLibUrl;
    public $locale = 'en_US';
    public function __construct()
    {
        $this->jsLibUrl = 'http://' . $this->baceFbUrl . '/' . $this->locale . '/all.js';
    }
}