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
 * Class ToolClasses
 * @package SmetDenis\JBDump
 */
class ToolClasses extends Tool
{

    /**
     * Profile render - to log file
     */
    protected function _profilerRenderFile()
    {
        $this->log('-------------------------------------------------------', 'Profiler start');
        foreach ($this->_bufferInfo as $key => $mark) {
            $this->log(self::_profilerFormatMark($mark));
        }
        $this->log('-------------------------------------------------------', 'Profiler end');
    }
}