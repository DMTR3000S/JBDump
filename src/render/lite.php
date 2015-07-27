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
 * Class RenderCli
 * @package SmetDenis\JBDump
 */
class RenderLite
{
    /**
     * Dump render - Lite mode
     * @param mixed  $data
     * @param string $varname
     * @param array  $params
     */
    protected function _dumpRenderLite($data, $varname = '...', $params = array())
    {
        if (is_bool($data)) {
            $data = $data ? 'TRUE' : 'FALSE';
        } elseif (is_null($data)) {
            $data = 'NULL';
        }

        $printrOut = print_r($data, true);
        if (!self::isCli()) {
            $printrOut = $this->_htmlChars($printrOut);
        }

        if (self::isAjax()) {
            $printrOut = str_replace('] =&gt;', '] =>', $printrOut);
        }

        $output = array();
        if (!self::isCli()) {
            $output[] = '<pre>------------------------------' . PHP_EOL;
        }

        $output[] = $varname . ' = ';
        if (self::isCli()) {
            $trace = debug_backtrace();
            unset($trace[-1], $trace[0], $trace[1]);
            $trace = $this->convertTrace($trace);
            reset($trace);
            $path = current($trace);

            if (preg_match('#\/.*?([a-z\.]*)\s:\s(\d*)#i', $path, $matches)) {
                $output[] = $matches[1] . ':' . $matches[2] . ' | ';
            }

        }
        $output[] = rtrim($printrOut, PHP_EOL);

        if (!self::isCli()) {
            $output[] = PHP_EOL . '------------------------------</pre>' . PHP_EOL;
        } else {
            $output[] = PHP_EOL;
        }

        if (!self::isAjax()) {
            echo '<pre class="jbdump" style="text-align: left;">' . implode('', $output) . '</pre>' . PHP_EOL;
        } else {
            echo implode('', $output);
        }
    }
}
