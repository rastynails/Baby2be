<?php

class component_Ping extends SK_Component
{
    public function __construct( array $params = null )
    {
        parent::__construct('ping');
    }


    public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
    {
        $handler = new SK_ComponentFrontendHandler('Ping');
        $handler->construct();

        if ( SK_HttpUser::is_authenticated() )
        {
            $handler->startUpdateActivity(300000);
        }

        $this->frontend_handler = $handler;

        parent::prepare($Layout, $Frontend);
    }

    public static function ajax_Ping($params = null, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response)
    {
        $request = $params->request;
        $stack = $request['stack'];

        $responseStack = array();

        foreach ( $stack as $c )
        {
            $commandHandler = new SK_ComponentFrontendHandler('ping');
            $commandParams = new SK_HttpRequestParams($c['params']);
            $commandResult = self::onCommand(trim($c['command']), $commandParams, $commandHandler, $response, $handler);

            $responseStack[] = array(
                'command' => $c['command'],
                'result' => array(
                    'data' => $commandResult,
                    'js' => $commandHandler->compile_js('this')
                )
            );
        }

        return array(
            'stack' => $responseStack
        );
    }



    public static function onCommand( $command, $params, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response, SK_ComponentFrontendHandler $pingHandler)
    {
        try
        {
            switch ( $command )
            {
                case 'shoutbox':
                    return component_Shoutbox::ping($params, $handler, $response);

                case 'imListener':
                    return component_IMListener::ping($params, $handler, $response);

                case 'chat':
                    return component_Chat::ping($params, $handler, $response);

                case 'speedDating':
                    return component_EventSpeedDatingNotifier::ping($params, $handler, $response);

                case 'updateActivity':
                    return self::updateActivity($params, $handler, $response);
            }
        }
        catch ( SK_HttpRequestException $e )
        {
            if ( $e->getCode() == SK_HttpRequestException::AUTH_REQUIRED )
            {
                $pingHandler->signIn();
            }
        }
    }


    public static function updateActivity($params = null, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response)
    {
        return app_Profile::updateProfileActivity();
    }

}
