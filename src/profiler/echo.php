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
     * Profile render - echo lite
     */
    protected function _profilerRenderEcho()
    {
        $output = PHP_EOL;
        foreach ($this->_bufferInfo as $key => $mark) {
            $output .= "\t" . self::_profilerFormatMark($mark) . PHP_EOL;
        }
        $this->_dumpRenderLite($output, '! profiler !');
    }
}
