<?php

class SK_QueryLogger
{
    /**
     * @var array
     */
    private static $classInstances;
    
    /**
     * @var array
     */
    private $enableProfiler;

    /**
     * @var int
     */
    private $profiler;

    /**
     * @var int
     */
    private $queryCount = 0;

    /**
     * @var int
     */
    private $totalQueryExecTime = 0 ;

    /**
     * @var array
     */
    private $queryLog = array();

    /**
     * @var array
     */
    private $isStart = false;

    /**
     * @var array
     */
    private $lastQuery = array();



    /**
     * Returns profiler result array
     *
     * @return array
     */
    public function getQueryLog()
    {
        $this->loggerStop();
        return array('total_time' => $this->totalQueryExecTime, 'query_count' => $this->queryCount, 'query_log' => $this->queryLog );
    }

    public function printQueryLog( $title = '' )
    {
        if ( defined('DEV_PROFILER') && DEV_PROFILER  )
        {
            $this->loggerStop();

            $html = '<b>'.$title.'</b>
                <style>.ql td{border:1px solid #666; font:16px Tahoma;color:blue;background:white;}</style>
                <table class="ql" style="color:black;">
                    <tr>
                        <td style="padding:5px" collspan=2 >total query exec time&nbsp;&nbsp;<b>'. $this->totalQueryExecTime .'</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;query count&nbsp;&nbsp;<b>'. $this->queryCount .'</b></td>
                        <td></td>
                    </tr>';


            foreach( $this->queryLog as $key => $value )
            {
                if( isset($_GET['qf']) )
                {
                    if( !strstr($value['query'], $_GET['qf']) )
                    {
                        continue;
                    }
                }

                $html .= '
                    <tr >
                        <td style="padding:5px">'. nl2br($value["query"]) .'</td>
                        <td style="padding:5px">'. sprintf('%.3f',$value["execTime"]) .'</td>
                    </tr>';
            }
            $html .= '</table>';

            $out = '
            <div style="
                margin:10px 2px;
                border:1px inset #efefef;
                padding:10px;
                background: #efefef;
                color: #000 !important;
                font-family: \'Courier New\';
                font-size: 12px;
                text-align: left;
                ">'.$html.'
                </div>';


            echo $out;
        }
    }

    /**
     * Returns total time past from the start.
     *
     * @return float
     */
    public function getTotalTime()
    {
        return (microtime(true) - $this->checkPoints['start']);
    }

    /**
     * Constructor
     *
     * @param string
     */
    private function __construct( $key )
    {
        $this->enableProfiler = false;
        
        if ( defined('DEV_PROFILER') && DEV_PROFILER  )
        {
            $this->enableProfiler = true;
        }

        $this->profiler = SK_Profiler::getInstance($key);
    }

    /**
     * Returns "single-tone" instance of class for every $key
     *
     * @param string $key #Profiler object identifier#
     * @return SK_QueryLogger
     */
    public static function getInstance( $key = null )
    {
        if ( self::$classInstances === null )
        {
            self::$classInstances = array();
        }

        if ( !isset(self::$classInstances[$key]) )
        {
            self::$classInstances[$key] = new self($key);
        }

        return self::$classInstances[$key];
    }

    /**
     * Sets new profiler checkpoint
     *
     * @param string $key
     */
    public function loggerStart( $query )
    {
        if ( !$this->enableProfiler )
        {
            return;
        }
        
        $this->lastQuery = $query;
        $this->isStart = true;
        
        $this->profiler->reset();
    }

    /**
     * Stops profiler and geberates result array
     */
    public function loggerStop()
    {
        if ( !$this->enableProfiler || !$this->isStart )
        {
            return;
        }

        $this->isStart = false;
        $totalTime = $this->profiler->getTotalTime();
        $this->queryCount++;
        $this->totalQueryExecTime += (float)$totalTime;
        $this->queryLog[] = array('query' => $this->lastQuery, 'execTime' => $totalTime);
        $this->lastQuery = null;
    }

    /**
     * Resets profiler
     *
     */
    /* public function reset()
    {
        $this->isStart = false;
        $this->queryCount = 0;
        $this->totalQueryExecTime = 0;
        $this->queryLog = array();
        $this->lastQuery = null;
        $this->profiler->reset();
    }(*/
}