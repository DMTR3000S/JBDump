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
 * Class Trace
 * @package SmetDenis\JBDump
 */
class Trace
{
    /**
     * Last backtrace
     * @var array
     */
    protected $_trace = array();

    /**
     * Get system backtrace in formated view
     * @param   bool $trace     Custom php backtrace
     * @param   bool $addObject Show objects in result
     * @return  JBDump
     */
    public static function trace($trace = null, $addObject = false)
    {
        if (!self::isDebug()) {
            return false;
        }

        $_this = self::i();

        $trace = $trace ? $trace : debug_backtrace($addObject);
        unset($trace[0]);

        $result = $_this->convertTrace($trace, $addObject);

        return $_this->dump($result, '! backtrace !');
    }


    /**
     * Get relative path from absolute
     * @param   string $path Absolute filepath
     * @return  string
     */
    protected function _getRalativePath($path)
    {
        if ($path) {
            $rootPath = str_replace(array('/', '\\'), '/', self::$_config['root']);

            $path = str_replace(array('/', '\\'), '/', $path);
            $path = str_replace($rootPath, '/', $path);
            $path = str_replace('//', '/', $path);
            $path = trim($path, '/');
        }
        return $path;
    }

    /**
     * Get formated one trace info
     * @param   array $info      One trace element
     * @param   bool  $addObject Add object to result (low perfomance)
     * @return  array
     */
    protected function _getOneTrace($info, $addObject = false)
    {
        $_this = self::i();

        $_tmp = array();
        if (isset($info['file'])) {
            $_tmp['file'] = $_this->_getRalativePath($info['file']) . ' : ' . $info['line'];
        } else {
            $info['file'] = false;
        }

        if ($info['function'] != 'include' && $info['function'] != 'include_once' && $info['function'] != 'require'
            && $info['function'] != 'require_once'
        ) {
            if (isset($info['type']) && isset($info['class'])) {

                $_tmp['func'] = $info['class']
                    . ' ' . $info['type']
                    . ' ' . $info['function'] . '()';

            } else {
                $_tmp['func'] = $info['function'] . '()';
            }

            $args = isset($info['args']) ? $info['args'] : array();

            if (self::$_config['showArgs'] || $addObject) {
                $_tmp['args'] = isset($info['args']) ? $info['args'] : array();
            } else {
                $_tmp['count_args'] = count($args);
            }

        } else {
            $_tmp['func'] = $info['function'];
        }

        if (isset($info['object']) && (self::$_config['showArgs'] || $addObject)) {
            $_tmp['obj'] = $info['object'];
        }

        return $_tmp;
    }


    /**
     * Get last function name and it params from backtrace
     * @param   array $trace Backtrace
     * @return  string
     */
    protected function _getSourceFunction($trace)
    {
        $lastTrace = $this->_getLastTrace($trace);

        if (isset($lastTrace['function']) || isset($lastTrace['class'])) {

            $args = '';
            if (isset($lastTrace['args'])) {
                $args = '( ' . count($lastTrace['args']) . ' args' . ' )';
            }

            if (isset($lastTrace['class'])) {
                $function = $lastTrace['class'] . ' ' . $lastTrace['type'] . ' ' . $lastTrace['function'] . ' ' . $args;
            } else {
                $function = $lastTrace['function'] . ' ' . $args;
            }

            return 'Function: ' . $function . '<br />';
        }

        return '';
    }

    /**
     * Get last source path from backtrace
     * @param   array $trace    Backtrace
     * @param   bool  $fileOnly Show filename only
     * @return  string
     */
    protected function _getSourcePath($trace, $fileOnly = false)
    {
        $path         = '';
        $currentTrace = $this->_getLastTrace($trace);

        if (isset($currentTrace['file'])) {
            $path = $this->_getRalativePath($currentTrace['file']);

            if ($fileOnly && $path) {
                $path = pathinfo($path, PATHINFO_BASENAME);
            }

            if (isset($currentTrace['line']) && $path) {
                $path = $path . ':' . $currentTrace['line'];
            }
        }

        if (!$path) {
            $path = 'undefined:0';
        }

        return $path;
    }

    /**
     * Get Last trace info
     * @param   array $trace Backtrace
     * @return  array
     */
    protected function _getLastTrace($trace)
    {
        // current filename info
        $curFile       = pathinfo(__FILE__, PATHINFO_BASENAME);
        $curFileLength = $this->_strlen($curFile);

        $meta = array();
        $j    = 0;
        for ($i = 0; $trace && $i < sizeof($trace); $i++) {
            $j = $i;
            if (isset($trace[$i]['class'])
                && isset($trace[$i]['file'])
                && ($trace[$i]['class'] == 'JBDump')
                && (substr($trace[$i]['file'], -$curFileLength, $curFileLength) == $curFile)
            ) {

            } elseif (isset($trace[$i]['class'])
                && isset($trace[$i + 1]['file'])
                && isset($trace[$i]['file'])
                && $trace[$i]['class'] == 'JBDump'
                && (substr($trace[$i]['file'], -$curFileLength, $curFileLength) == $curFile)
            ) {

            } elseif (isset($trace[$i]['file'])
                && (substr($trace[$i]['file'], -$curFileLength, $curFileLength) == $curFile)
            ) {

            } else {
                // found!
                $meta['file'] = isset($trace[$i]['file']) ? $trace[$i]['file'] : '';
                $meta['line'] = isset($trace[$i]['line']) ? $trace[$i]['line'] : '';
                break;
            }
        }

        // get functions
        if (isset($trace[$j + 1])) {
            $result         = $trace[$j + 1];
            $result['line'] = $meta['line'];
            $result['file'] = $meta['file'];
        } else {
            $result = $meta;
        }

        return $result;
    }

    /**
     * Convert trace information to readable
     * @param array $trace Standard debug backtrace data
     * @param bool  $addObject
     * @return array
     */
    public function convertTrace($trace, $addObject = false)
    {
        $result = array();
        if (is_array($trace)) {
            foreach ($trace as $key => $info) {
                $oneTrace = self::i()->_getOneTrace($info, $addObject);

                $file = 'undefined';
                if (isset($oneTrace['file'])) {
                    $file = $oneTrace['file'];
                }

                $result['#' . ($key - 1) . ' ' . $oneTrace['func']] = $file;
            }
        }

        return $result;
    }


    /**
     * Get arguments for current function/method
     * @return bool
     */
    public static function args()
    {
        if (!self::isDebug()) {
            return false;
        }

        $_this = self::i();

        $trace        = debug_backtrace(0);
        $currentTrace = $trace[1];
        if (isset($currentTrace['args'])) {

            // get function info (class method or simple func)
            if (isset($currentTrace['class'])) {

                $classInfo = $_this->_getClass($currentTrace['class']);
                if (isset($classInfo['methods']['public'][$currentTrace['function']])) {
                    $funcInfo = $classInfo['methods']['public'][$currentTrace['function']];

                } elseif (isset($classInfo['methods']['private'][$currentTrace['function']])) {
                    $funcInfo = $classInfo['methods']['private'][$currentTrace['function']];

                } elseif (isset($classInfo['methods']['protected'][$currentTrace['function']])) {
                    $funcInfo = $classInfo['methods']['protected'][$currentTrace['function']];
                }

            } else {
                $funcInfo = $_this->_getFunction($currentTrace['function']);
            }

            // chech arguments info
            if (isset($funcInfo['parameters'])) {
                $result = array();
                $i      = 0;
                foreach ($funcInfo['parameters'] as $argName => $argInfo) {

                    if (isset($currentTrace['args'][$i])) {
                        $result[$argName] = $currentTrace['args'][$i];

                    } elseif (isset($argInfo['default'])) {
                        $result[$argName] = $argInfo['default'];

                    } else {
                        $result[$argName] = null;
                    }

                    $i++;
                }

            } else {
                $result = $currentTrace['args'];
            }

            $_this->dump($result);
        }

        return $_this;
    }
}
