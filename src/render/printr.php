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
 * Class RenderPrintr
 * @package SmetDenis\JBDump
 */
class RenderPrintr
{

    /**
     * Wrapper for PHP print_r function
     * @param mixed  $var     The variable to dump
     * @param string $varname Label to prepend to output
     * @param array  $params  Echo output if true
     * @return bool|JBDump
     */
    public static function print_r($var, $varname = '...', $params = array())
    {
        if (!self::isDebug()) {
            return false;
        }

        $output = print_r($var, true);

        $_this = self::i();
        $_this->_dumpRenderHtml($output, $varname, $params);

        return $_this;
    }

    /**
     * Dump render - php print_r
     * @param mixed  $data
     * @param string $varname
     * @param array  $params
     */
    protected function _dumpRenderPrintr($data, $varname = '...', $params = array())
    {
        $this->print_r($data, $varname, $params);
    }
}
