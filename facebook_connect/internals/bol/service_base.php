<?php

class FBC_ServiceBase
{
    /**
     *
     * @var Facebook
     */
    protected $faceBook;

    /**
     *
     * @var FBC_ConnectDetails
     */
    protected $faceBookDetails;

    /**
     *
     * @var FBC_FieldDao
     */
    protected $fieldDao;

    /**
     * Class constructor
     *
     */
    protected function __construct()
    {
        $this->fieldDao = new FBC_FieldDao();
    }

    /**
     *
     * @return FBC_ConnectDetails
     */
    public function getFaceBookAccessDetails()
    {
        if (empty($this->faceBookDetails))
        {
            $this->faceBookDetails = new FBC_ConnectDetails();
            $config = SK_Config::section('facebook_connect');
            $this->faceBookDetails->appId = $config->app_id;
            $this->faceBookDetails->apiSecret = $config->api_secret;

            $this->faceBookDetails->xdReceiverUrl =  SITE_URL . 'facebook_connect/channel.php';
        }

        return $this->faceBookDetails;
    }

    /**
     *
     * @return Facebook
     */
    public function getFaceBook()
    {
        if (empty($this->faceBook))
        {
            $access = $this->getFaceBookAccessDetails();

            $this->faceBook = new Facebook(array(
              'appId'  => $access->appId,
              'secret' => $access->apiSecret
            ));
        }

        return $this->faceBook;
    }
}