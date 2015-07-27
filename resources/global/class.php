<?php

use SmetDenis\JBDump\Debugger;

class JBDump
{
    /**
     * @var
     */
    static protected $debugger = null;

    /**
     * @param array $options
     * @return JBDump
     */
    public static function i($options = array())
    {
        if (!isset(self::$debugger)) {
            self::$debugger = new Debugger($options);
        }

        return self::$debugger;
    }

    /**
     * @return mixed
     */
    public static function dump()
    {
        return call_user_func_array(array(self::$debugger, 'dump'), func_get_args());
    }

    /**
     * @param $toolName  string
     * @param $arguments array
     * @return mixed
     */
    public static function __callStatic($toolName, $arguments)
    {
        $toolName = ucfirst(strtolower($toolName));
        $toolName = 'SmetDenis\\JBDump\\Tool' . $toolName;

        if (!class_exists($toolName)) {
            return self::$debugger;
        }

        /** @var Tool $tool */
        $tool = new $toolName(self::$debugger);
        return call_user_func_array(array($tool, 'exec'), func_get_args());
    }

}