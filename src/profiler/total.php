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
class ProfilterTotal extends Tool
{
    /**
     * Profiler render - total info
     */
    protected function _profilerRenderTotal()
    {
        reset($this->_bufferInfo);
        $first      = current($this->_bufferInfo);
        $last       = end($this->_bufferInfo);
        $memoryPeak = memory_get_peak_usage(true);

        $memoryDeltas = $timeDeltas = array();
        foreach ($this->_bufferInfo as $oneMark) {
            $memoryDeltas[] = $oneMark['memoryDiff'];
            $timeDeltas[]   = $oneMark['timeDiff'];
        }

        $totalInfo   = array();
        $totalInfo[] = '- Points: ' . count($this->_bufferInfo);
        $totalInfo[] = '-------- Time (ms)';
        $totalInfo[] = '- Max delta, msec: ' . self::_profilerFormatTime(max($timeDeltas));
        $totalInfo[] = '- Min delta, msec: ' . self::_profilerFormatTime(min($timeDeltas));
        $totalInfo[] = '- Total delta, msec: ' . self::_profilerFormatTime(($last['time'] - $first['time']));
        $totalInfo[] = '- Limit, sec: ' . ini_get('max_execution_time');
        $totalInfo[] = '-------- Memory (MB)';
        $totalInfo[] = '- Max delta: ' . self::_profilerFormatMemory(max($memoryDeltas));
        $totalInfo[] = '- Min delta: ' . self::_profilerFormatMemory(min($memoryDeltas));
        $totalInfo[] = '- Usage on peak: ' . $this->_formatSize($memoryPeak) . ' (' . $memoryPeak . ')';
        $totalInfo[] = '- Total delta: ' . self::_profilerFormatMemory($last['memory'] - $first['memory']);
        $totalInfo[] = '- Limit: ' . ini_get('memory_limit');

        if (self::isAjax()) {
            $this->_dumpRenderLog($totalInfo, 'Profiler total');
        } else {
            $totalInfo = PHP_EOL . "\t" . implode(PHP_EOL . "\t", $totalInfo) . PHP_EOL;
            $this->_dumpRenderLite($totalInfo, '! <b>profiler total info</b> !');
        }
    }
}