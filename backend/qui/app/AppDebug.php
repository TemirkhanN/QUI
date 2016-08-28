<?php

namespace qui\app;


class AppDebug
{
    private static $debugInfo;
    private static $debugIsActive;


    /**
     * Turns debug mode on or off
     *
     * @param bool|false $active
     */
    public static function switchDebugMode($active = false)
    {
        self::$debugIsActive = (bool)$active;
    }


    /**
     * @param string $tracker if needed to track from different places
     */
    public static function debugTrack($tracker = 'mainTracker')
    {
        static $setDisplayErrors = false;

        if(!$setDisplayErrors){
            $setDisplayErrors = true;
            ini_set('display_errors', true);
            error_reporting(E_ALL);
        }
        self::$debugInfo['timeTracker'][$tracker] = microtime(true);
    }


    /**
     * @param string $tracker of needed to see tracker with identifier
     * @return int|float|string|null if error occurred returns 999 seconds.
     */
    private static function debugShowTimeTrack($tracker = '')
    {
        if (isset(self::$debugInfo['timeTracker'][$tracker])) {
            return (microtime(true) - self::$debugInfo['timeTracker'][$tracker]) . ' sec';
        } else{
            return 'Tracker has not been initialized';
        }
    }



    public static function debugInfo($tracker = 'mainTracker')
    {
        echo PHP_EOL . '<br>time wasted ' . self::debugShowTimeTrack($tracker);
        echo PHP_EOL . '<br>memory used ' . round(memory_get_usage() / 1024) . ' kb';
        echo PHP_EOL . '<br>memory peak ' . round(memory_get_peak_usage(true) / 1024) . ' kb<br>' . PHP_EOL;
    }
}