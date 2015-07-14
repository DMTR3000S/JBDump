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
 * Class RenderLog
 * @package SmetDenis\JBDump
 */
class RenderLog
{
    /**
     * Dump render - to logfile
     * @param mixed  $data
     * @param string $varname
     * @param array  $params
     */
    protected function _dumpRenderLog($data, $varname = '...', $params = array())
    {
        $this->log($data, $varname, $params);
    }
}
