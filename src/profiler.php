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
 * Class Profiler
 * @package SmetDenis\JBDump
 */
class Profiler
{

    /**
     * Profiler buffer info
     * @var array
     */
    protected $_bufferInfo = array();

    /**
     * Start microtime
     * @var float
     */
    protected $_start = 0.0;

    /**
     * Previous microtime for profiler
     * @var float
     */
    protected $_prevTime = 0.0;

    /**
     * Previous memory value for profiler
     * @var float
     */
    protected $_prevMemory = 0.0;

    /**
     * Counteins of pairs calling
     * @var array
     */
    protected static $_profilerPairs = array();

    /**
     * Counters of calling
     * @var array
     */
    protected static $_counters = array(
        'mode_0' => array(),
        'mode_1' => array(),
        'trace'  => array(),
    );

    function __construct()
    {
        $this->_start        = $this->_microtime();
        $this->_bufferInfo[] = array(
            'time'       => 0,
            'timeDiff'   => 0,
            'memory'     => self::_getMemory(),
            'memoryDiff' => 0,
            'label'      => 'jbdump::init',
            'trace'      => '',
        );
    }


    /**
     * Show current usage memory in filesize format
     * @return  JBDump
     */
    public static function memory($formated = true)
    {
        if (!self::isDebug()) {
            return false;
        }

        $memory = self::i()->_getMemory();
        if ($formated) {
            $memory = self::i()->_formatSize($memory);
        }

        return self::i()->dump($memory, '! memory !');
    }

    /**
     * Show current microtime
     * @return  JBDump
     */
    public static function microtime()
    {
        $_this = self::i();
        if (!$_this->isDebug()) {
            return false;
        }

        $data = $_this->_microtime();

        return $_this->dump($data, '! current microtime !');
    }

    /**
     * @param string $label
     * @return bool
     * @return  JBDump
     */
    public static function markStart($label = 'default')
    {
        $time   = self::_microtime();
        $memory = self::_getMemory();

        $_this = self::i();
        if (!$_this->isDebug()) {
            return false;
        }

        if (!isset(self::$_profilerPairs[$label])) {
            self::$_profilerPairs[$label] = array();
        }

        $length = count(self::$_profilerPairs[$label]);
        if (isset(self::$_profilerPairs[$label][$length]['start'])) {
            $length++;
        }

        self::$_profilerPairs[$label][$length] = array('start' => array($time, $memory));

        return $_this;
    }

    /**
     * @param int    $outputMode
     *      0 - on destructor (PHP Die)
     *      1 - immediately
     * @param string $name
     */
    public static function inc($name = null, $outputMode = 0)
    {
        $_this = self::i();
        if (!$_this->isDebug()) {
            return false;
        }

        if (!$name) {
            $trace     = debug_backtrace();
            $traceInfo = $_this->_getOneTrace($trace[1]);
            $line      = isset($trace[0]['line']) ? $trace[0]['line'] : 0;
            $name      = $traceInfo['func'] . ', line #' . $line;
        }

        if (is_string($outputMode)) {
            $name       = $outputMode;
            $outputMode = 0;
        }

        if (!isset(self::$_counters['mode_' . $outputMode][$name])) {
            self::$_counters['mode_' . $outputMode][$name] = 0;
        }

        self::$_counters['mode_' . $outputMode][$name]++;

        if ($outputMode == 1) {
            echo '<pre>' . $name . ' = ' . self::$_counters['mode_' . $outputMode][$name] . '</pre>';
        }

        return self::$_counters['mode_' . $outputMode][$name];
    }

    /**
     * @param string $label
     * @return bool
     * @return  int
     */
    public static function incTrace($label = null)
    {
        $_this = self::i();
        if (!$_this->isDebug()) {
            return false;
        }

        $trace = debug_backtrace();

        if (!$label) {
            $traceInfo = $_this->_getOneTrace($trace[1]);
            $line      = isset($trace[0]['line']) ? $trace[0]['line'] : 0;
            $label     = $traceInfo['func'] . ', line #' . $line;
        }

        unset($trace[0]);
        unset($trace[1]);
        $trace     = array_slice($trace, 0, self::$_config['profiler']['traceLimit']);
        $traceInfo = array();
        foreach ($trace as $oneTrace) {
            $traceData   = $_this->_getOneTrace($oneTrace);
            $line        = isset($oneTrace['line']) ? $oneTrace['line'] : 0;
            $traceInfo[] = $traceData['func'] . ', line #' . $line;
        }

        $hash = md5(serialize($traceInfo));

        if (!isset(self::$_counters['trace'][$hash])) {

            self::$_counters['trace'][$hash] = array(
                'count' => 0,
                'label' => $label,
                'trace' => $traceInfo,
            );
        }

        self::$_counters['trace'][$hash]['count']++;

        return self::$_counters['trace'][$hash]['count'];
    }

    /**
     * @param string $label
     * @return  JBDump
     */
    public static function markStop($label = 'default')
    {
        $time   = self::_microtime();
        $memory = self::_getMemory();

        $_this = self::i();
        if (!$_this->isDebug()) {
            return false;
        }

        if (!isset(self::$_profilerPairs[$label])) {
            self::$_profilerPairs[$label] = array();
        }

        $length = count(self::$_profilerPairs[$label]);
        if ($length > 0) {
            $length--;
        }

        self::$_profilerPairs[$label][$length]['stop'] = array($time, $memory);

        return $_this;
    }

    /**
     * Output a time mark
     * The mark is returned as text current profiler status
     * @param   string $label A label for the time mark
     * @return  JBDump
     */
    public static function mark($label = '')
    {
        $_this = self::i();
        if (!$_this->isDebug()) {
            return false;
        }

        $current = $_this->_microtime() - $_this->_start;
        $memory  = self::_getMemory();
        $trace   = debug_backtrace();

        $markInfo = array(
            'time'       => $current,
            'timeDiff'   => $current - $_this->_prevTime,
            'memory'     => $memory,
            'memoryDiff' => $memory - $_this->_prevMemory,
            'trace'      => $_this->_getSourcePath($trace, true),
            'label'      => $label,
        );

        $_this->_bufferInfo[] = $markInfo;

        if ((int)self::$_config['profiler']['render'] & self::PROFILER_RENDER_FILE) {
            $_this->log(self::_profilerFormatMark($markInfo), 'mark #');
        }

        $_this->_prevTime   = $current;
        $_this->_prevMemory = $memory;

        return $_this;
    }


    /**
     * Show profiler result
     * @param   int $mode Render mode
     * @return  JBDump
     */
    public function profiler($mode = 1)
    {
        if ($this->isDebug() && count($this->_bufferInfo) > 2 && $mode) {

            $mode = (int)$mode;

            if ($mode && self::isAjax()) {
                if ($mode & self::PROFILER_RENDER_TOTAL) {
                    $this->_profilerRenderTotal();
                }

            } else {
                if ($mode & self::PROFILER_RENDER_TABLE) {
                    $this->_profilerRenderTable();
                }

                if ($mode & self::PROFILER_RENDER_CHART) {
                    $this->_profilerRenderChart();
                }

                if ($mode & self::PROFILER_RENDER_TOTAL) {
                    $this->_profilerRenderTotal();
                }

                if ($mode & self::PROFILER_RENDER_ECHO) {
                    $this->_profilerRenderEcho();
                }
            }
        }
    }


    /**
     * Get current usage memory
     * @return int
     */
    protected static function _getMemory()
    {
        if (function_exists('memory_get_usage')) {
            return memory_get_usage();
        } else {
            $output = array();
            $pid    = getmypid();

            if (substr(PHP_OS, 0, 3) == 'WIN') {
                @exec('tasklist /FI "PID eq ' . $pid . '" /FO LIST', $output);
                if (!isset($output[5])) {
                    $output[5] = null;
                }
                return (int)substr($output[5], strpos($output[5], ':') + 1);
            } else {
                @exec("ps -o rss -p $pid", $output);
                return $output[1] * 1024;
            }
        }
    }

    /**
     * Get current microtime
     * @return float
     */
    public static function _microtime()
    {
        return microtime(true);
    }

    /**
     * @param array $data
     * @param bool  $sample
     * @return bool|float
     */
    protected function _stdDev(array $data, $sample = false)
    {
        $n = count($data);
        if ($n === 0) {
            trigger_error("The array has zero elements", E_USER_WARNING);
            return false;
        }

        if ($sample && $n === 1) {
            trigger_error("The array has only 1 element", E_USER_WARNING);
            return false;
        }

        $mean  = array_sum($data) / $n;
        $carry = 0.0;

        foreach ($data as $val) {
            $d = ((double)$val) - $mean;
            $carry += $d * $d;
        };

        if ($sample) {
            --$n;
        }

        return sqrt($carry / $n);
    }
}