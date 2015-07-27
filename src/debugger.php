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
 * Class Error
 * @package SmetDenis\JBDump
 */
class Debugger
{
    /**
     * Fix bug anti cycling destructor
     * @var bool
     */
    protected static $_isDie = false;

    /**
     * Flag enable or disable the debugger
     * @var bool
     */
    public static $enabled = true;

    /**
     * Directory separator
     */
    const DS = '/';

    /**
     * Constructor, set internal variables and self configuration
     * @param array $options Initialization parameters
     */
    public function __construct(array $options = array())
    {
        // params
        // error handlers
        // profiler
    }

    /**
     * Destructor, call _shutdown method
     */
    function __destruct()
    {
        if (!self::$_isDie) {
            self::$_isDie = true;
            if (self::$_config['profiler']['showEnd']) {
                self::mark('jbdump::end');
            }
            $this->profiler(self::$_config['profiler']['render']);
        }

        if (!self::$_config['profiler']['showOnAjax'] && self::isAjax()) {
            return;
        }

        // JBDump incriment output
        if (!empty(self::$_counters['mode_0'])) {

            arsort(self::$_counters['mode_0']);

            foreach (self::$_counters['mode_0'] as $counterName => $count) {
                echo '<pre>JBDump Increment / "' . $counterName . '" = ' . $count . '</pre>';
            }
        }

        // JBDump trace incriment output
        if (!empty(self::$_counters['trace'])) {

            uasort(self::$_counters['trace'], function ($a, $b) {
                if ($a['count'] == $b['count']) {
                    return 0;
                }
                return ($a['count'] < $b['count']) ? 1 : -1;
            });

            foreach (self::$_counters['trace'] as $counterHash => $traceInfo) {
                self::i()->dump($traceInfo['trace'], $traceInfo['label'] . ' = ' . $traceInfo['count']);
            }
        }

        // JBDump pairs profiler
        if (!empty(self::$_profilerPairs)) {

            foreach (self::$_profilerPairs as $label => $pairs) {

                $timeDelta = $memDelta = $count = 0;
                $memDiffs  = $timeDiffs = array();

                foreach ($pairs as $key => $pair) {

                    if (!isset($pair['stop']) || !isset($pair['start'])) {
                        continue;
                    }

                    $count++;

                    $tD = $pair['stop'][0] - $pair['start'][0];
                    $mD = $pair['stop'][1] - $pair['start'][1];

                    $timeDiffs[] = $tD;
                    $memDiffs[]  = $mD;

                    $timeDelta += $tD;
                    $memDelta += $mD;
                }

                if ($count > 0) {

                    $timeAvg = array_sum($timeDiffs) / $count;
                    $memoAvg = array_sum($memDiffs) / $count;

                    $timeStd = $memoStd = '';
                    if ($count > 1) {
                        $timeStdValue = $this->_stdDev($timeDiffs);
                        $memoStdValue = $this->_stdDev($memDiffs);

                        $timeStd = ' <span title="' . round(($timeStdValue / $timeAvg) * 100) . '%">(&plusmn;'
                            . self::_profilerFormatTime($timeStdValue, true, 2) . ')</span>';
                        $memoStd = ' <span title="' . round(($memoStdValue / $memoAvg) * 100) . '%">(&plusmn;'
                            . self::_profilerFormatMemory($memoStdValue, true) . ')</span>';
                    }

                    $output = array(
                        '<pre>JBDump ProfilerPairs / "' . $label . '"',
                        'Count  = ' . $count,
                        'Time   = ' . implode(";\t\t", array(
                            'ave: ' . self::_profilerFormatTime($timeAvg, true, 2) . $timeStd,
                            'sum: ' . self::_profilerFormatTime(array_sum($timeDiffs), true, 2),
                            'min(' . (array_search(min($timeDiffs), $timeDiffs) + 1) . '):' . self::_profilerFormatTime(min($timeDiffs), true, 2),
                            'max(' . (array_search(max($timeDiffs), $timeDiffs) + 1) . '): ' . self::_profilerFormatTime(max($timeDiffs), true, 2),
                        )),
                        'Memory = ' . implode(";\t\t", array(
                            'ave: ' . self::_profilerFormatMemory($memoAvg, true) . $memoStd,
                            'sum: ' . self::_profilerFormatMemory(array_sum($memDiffs), true),
                            'min(' . (array_search(min($memDiffs), $memDiffs) + 1) . '): ' . self::_profilerFormatMemory(min($memDiffs), true),
                            'max(' . (array_search(max($memDiffs), $memDiffs) + 1) . '): ' . self::_profilerFormatMemory(max($memDiffs), true),
                        )),
                        '</pre>'
                    );
                } else {
                    $output = array(
                        '<pre>JBDump ProfilerPairs / "' . $label . '"',
                        'Count  = ' . $count,
                        '</pre>'
                    );
                }

                echo implode(PHP_EOL, $output);
            }
        }
    }

    /**
     * Returns the global JBDump object, only creating it
     * if it doesn't already exist
     * @param   array $options Initialization parameters
     * @return  JBDump
     */
    public static function i($options = array())
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new self($options);
            if (self::$_config['profiler']['showStart']) {
                self::mark('jbdump::start');
            }

        }

        return $instance;
    }


    /**
     * Check permissions for show all debug messages
     *  - check ip, it if set in config
     *  - check requestParam, if it set in config
     *  - else return self::$enabled
     * @return  bool
     */
    public static function isDebug()
    {

        $result = self::$enabled;
        if ($result) {

            if (self::$_config['personal']['ip']) {

                if (is_array(self::$_config['personal']['ip'])) {
                    $result = in_array(self::getClientIP(), self::$_config['personal']['ip']);

                } else {
                    $result = self::getClientIP() == self::$_config['personal']['ip'];

                }

            }

            if (self::$_config['personal']['requestParam'] && $result) {

                if (isset($_REQUEST[self::$_config['personal']['requestParam']])
                    &&
                    $_REQUEST[self::$_config['personal']['requestParam']] == self::$_config['personal']['requestValue']
                ) {
                    $result = true;
                } else {
                    $result = false;
                }
            }

        }

        return $result;
    }

    /**
     * Set max execution time
     * @param   integer $time Time limit in seconds
     * @return  JBDump
     */
    public static function maxTime($time = 600)
    {
        if (!self::isDebug()) {
            return false;
        }

        ini_set('max_execution_time', $time);
        set_time_limit($time);

        return self::i();
    }

    /**
     * Enable debug
     * @return  JBDump
     */
    public static function on()
    {
        self::$enabled = true;
        return self::i();
    }


    /**
     * Disable debug
     * @return JBDump
     */
    public static function off()
    {
        self::$enabled = false;
        return self::i();
    }


    /**
     * Get the IP number of differnt ways
     * @param bool $getSource
     * @return string
     */
    public static function getClientIP($getSource = false)
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip     = $_SERVER['HTTP_CLIENT_IP'];
            $source = 'HTTP_CLIENT_IP';

        } elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
            $ip     = $_SERVER['HTTP_X_REAL_IP'];
            $source = 'HTTP_X_REAL_IP';

        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip     = $_SERVER['HTTP_X_FORWARDED_FOR'];
            $source = 'HTTP_X_FORWARDED_FOR';

        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip     = $_SERVER['REMOTE_ADDR'];
            $source = 'REMOTE_ADDR';

        } else {
            $ip     = '0.0.0.0';
            $source = 'undefined';
        }

        if ($getSource) {
            return $source;
        } else {
            return $ip;
        }
    }

    /**
     * Is current request ajax or lite mode is enabled
     * @return  bool
     */
    protected function _isLiteMode()
    {
        if (self::$_config['dump']['render'] == 'lite') {
            return true;
        }

        return self::isAjax();
    }

    /**
     * Check is current HTTP request is ajax
     * @return  bool
     */
    public static function isAjax()
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        ) {
            return true;

        } elseif (self::isCli()) {
            return true;

        } elseif (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            foreach ($headers as $key => $value) {
                if (strtolower($key) == 'x-requested-with' && strtolower($value) == 'xmlhttprequest') {
                    return true;
                }
            }

        } elseif (isset($_REQUEST['ajax']) && $_REQUEST['ajax']) {
            return true;

        } elseif (isset($_REQUEST['AJAX']) && $_REQUEST['AJAX']) {
            return true;

        }

        return false;
    }

    /**
     * Check invocation of PHP is from the command line (CLI)
     * @return  bool
     */
    public static function isCli()
    {
        return php_sapi_name() == 'cli';
    }
}