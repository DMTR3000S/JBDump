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
 * Class render
 * @package SmetDenis\JBDump
 */
class Render
{
    /**
     * Get valid string length
     * @param   string $string Some string
     * @return  int
     */
    protected function _strlen($string)
    {
        $encoding = function_exists('mb_detect_encoding') ? mb_detect_encoding($string) : false;
        return $encoding ? mb_strlen($string, $encoding) : strlen($string);
    }

    /**
     * Dumper variable
     * @param   mixed  $data    Mixed data for dump
     * @param   string $varname Variable name
     * @param   array  $params  Additional params
     * @return  JBDump
     */
    public static function dump($data, $varname = '...', $params = array())
    {
        if (!self::isDebug()) {
            return false;
        }

        $_this = self::i();

        if (self::isAjax()) {
            $_this->_dumpRenderLite($data, $varname, $params);

        } elseif (self::$_config['dump']['render'] == 'lite') {
            $_this->_dumpRenderLite($data, $varname, $params);

        } elseif (self::$_config['dump']['render'] == 'html') {
            $_this->_dumpRenderHtml($data, $varname, $params);

        } elseif (self::$_config['dump']['render'] == 'log') {
            $_this->_dumpRenderLog($data, $varname, $params);

        } elseif (self::$_config['dump']['render'] == 'mail') {
            $_this->_dumpRenderMail($data, $varname, $params);

        } elseif (self::$_config['dump']['render'] == 'print_r') {
            $_this->_dumpRenderPrintr($data, $varname, $params);

        } elseif (self::$_config['dump']['render'] == 'var_dump') {
            $_this->_dumpRenderVardump($data, $varname, $params);
        }

        if (self::$_config['dump']['die']) {
            die('JBDump_die');
        }

        return $_this;
    }


}
