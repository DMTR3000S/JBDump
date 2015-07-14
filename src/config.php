<?php
/**
 * JBDump
 *
 * Copyright (c) 2015, Denis Smetannikov <denis@jbzoo.com>.
 *
 * @package   JBDump
 * @author    Denis Smetannikov <denis@jbzoo.com>
 * @copyright 2015 Denis Smetannikov <denis@jbzoo.com>
 * @link      http://github.com/smetdenis/jbdump
 */

namespace SmetDenis\JBDump;

/**
 * Class Config
 * @package SmetDenis\JBDump
 */
class Config
{
    /**
     * Library version
     * @var string
     */
    const DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * Render type bit
     */
    const PROFILER_RENDER_NONE  = 0;
    const PROFILER_RENDER_FILE  = 1;
    const PROFILER_RENDER_ECHO  = 2;
    const PROFILER_RENDER_TABLE = 4;
    const PROFILER_RENDER_CHART = 8;
    const PROFILER_RENDER_TOTAL = 16;

    /**
     * Default configurations
     * @var array
     */
    protected static $_config = array
    (
        'root'     => null, // project root directory
        'showArgs' => 0, // show Args in backtrace
        'showCall' => 1,

        // // // file logger
        'log'      => array(
            'path'      => null, // absolute log path
            'file'      => 'jbdump', // log filename
            'format'    => "{DATETIME}\t{CLIENT_IP}\t\t{FILE}\t\t{NAME}\t\t{JBDUMP_MESSAGE}", // fields in log file
            'serialize' => 'print_r', // (none|json|serialize|print_r|var_dump|format|php_array)
        ),

        // // // profiler
        'profiler' => array(
            'auto'       => 1, // Result call automatically on destructor
            'render'     => 20, // Profiler render (bit mask). See constants jbdump::PROFILER_RENDER_*
            'showStart'  => 0, // Set auto mark after jbdump init
            'showEnd'    => 0, // Set auto mark before jbdump destruction
            'showOnAjax' => 0, // Show profiler information on ajax calls
            'traceLimit' => 3, // Limit for function JBDump::incTrace();
        ),

        // // // sorting (ASC)
        'sort'     => array(
            'array'   => 0, // by keys
            'object'  => 1, // by properties name
            'methods' => 1, // by methods name
        ),

        // // // personal dump
        'personal' => array(
            'ip'           => array(), // IP address for which to work debugging
            'requestParam' => 0, // $_REQUEST key for which to work debugging
            'requestValue' => 0, // $_REQUEST value for which to work debugging
        ),

        // // // error handlers
        'errors'   => array(
            'reporting'          => 0, // set error reporting level while construct
            'errorHandler'       => 0, // register own handler for PHP errors
            'errorBacktrace'     => 0, // show backtrace for errors
            'exceptionHandler'   => 0, // register own handler for all exeptions
            'exceptionBacktrace' => 0, // show backtrace for exceptions
            'context'            => 0, // show context for errors
            'logHidden'          => 0, // if error message not show, log it
            'logAll'             => 0, // log all error in syslog
        ),

        // // // mail send
        'mail'     => array(
            'to'      => 'jbdump@example.com', // mail to
            'subject' => 'JBDump debug', // mail subject
            'log'     => 0, // log all mail messages
        ),

        // // // dump config
        'dump'     => array(
            'render'       => 'html', // (lite|log|mail|print_r|var_dump|html)
            'stringLength' => 80, // cutting long string
            'maxDepth'     => 4, // the maximum depth of the dump
            'showMethods'  => 1, // show object methods
            'die'          => 0, // die after dumping variable
            'expandLevel'  => 1, // expand the list to the specified depth
        ),
    );

    /**
     * Set debug parameters
     * @param array  $data Params for debug, see self::$_config vars
     * @param string $section
     * @return JBDump
     */
    public function setParams($data, $section = null)
    {
        if ($section) {
            $newData = array($section => $data);
            $data    = $newData;
            unset($newData);
        }

        if (isset($data['errors']['reporting'])) {
            $this->showErrors($data['errors']['reporting']);
        }

        // set root directory
        if (!isset($data['root']) && !self::$_config['root']) {
            $data['root'] = $_SERVER['DOCUMENT_ROOT'];
        }

        // set log path
        if (isset($data['log']['path']) && $data['log']['path']) {
            $this->_logPath = $data['log']['path'];

        } elseif (!self::$_config['log']['path'] || !$this->_logPath) {
            $this->_logPath = dirname(__FILE__) . self::DS . 'logs';
        }

        // set log filename
        $logFile = 'jbdump';
        if (isset($data['log']['file']) && $data['log']['file']) {
            $logFile = $data['log']['file'];

        } elseif (!self::$_config['log']['file'] || !$this->_logfile) {
            $logFile = 'jbdump';
        }

        $this->_logfile = $this->_logPath . self::DS . $logFile . '_' . date('Y.m.d') . '.log.php';

        // merge new params with of config
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $keyInner => $valueInner) {
                    if (!isset(self::$_config[$key])) {
                        self::$_config[$key] = array();
                    }
                    self::$_config[$key][$keyInner] = $valueInner;
                }
            } else {
                self::$_config[$key] = $value;
            }
        }

        return $this;
    }

}