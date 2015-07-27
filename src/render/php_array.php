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
 * Class RenderPhpArray
 * @package SmetDenis\JBDump
 */
class RenderPhpArray
{
    /**
     * Render variable as phpArray
     * @param mixed  $var
     * @param string $name
     * @param bool   $isReturn
     * @return mixed
     */
    public static function phpArray($var, $varname = 'varName', $isReturn = false)
    {
        if (!self::isDebug()) {
            return false;
        }

        $output = JBDump_array2php::toString($var, $varname);
        if ($isReturn) {
            return $output;
        }

        $_this = self::i();
        $_this->_dumpRenderHtml($output, $varname, $params);

        return $_this;
    }
}
