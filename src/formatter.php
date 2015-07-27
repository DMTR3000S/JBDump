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
 * Class Formatter
 * @package SmetDenis\JBDump
 */
class Formatter
{

    /**
     * Convert profiler memory value to usability view
     * @param int  $memoryBytes
     * @param bool $addMeasure
     * @return float
     */
    protected static function _profilerFormatMemory($memoryBytes, $addMeasure = false)
    {
        $bytes = round($memoryBytes / 1024 / 1024, 3);

        if ($addMeasure) {
            $bytes .= ' MB';
        }

        return $bytes;
    }

    /**
     * Convert profiler time value to usability view
     * @param      $time
     * @param bool $addMeasure
     * @return float
     */
    protected static function _profilerFormatTime($time, $addMeasure = false, $round = 0)
    {
        $time = round($time * 1000, $round);

        if ($addMeasure) {
            $time .= ' ms';
        }

        return $time;
    }

    /**
     * Convert profiler mark to string
     * @param array $mark
     * @return string
     */
    protected static function _profilerFormatMark(array $mark)
    {
        return sprintf("%0.3f sec (+%.3f); %0.3f MB (%s%0.3f) - %s",
            (float)$mark['time'],
            (float)$mark['timeDiff'],
            ($mark['memory'] / 1024 / 1024),
            ($mark['memoryDiff'] / 1024 / 1024 >= 0) ? '+' : '',
            ($mark['memoryDiff'] / 1024 / 1024),
            $mark['label']
        );
    }

    /**
     * Convert file size to formatted string
     * @param   integer $bytes Count bytes
     * @return  string
     */
    protected static function _formatSize($bytes)
    {
        $exp    = 0;
        $value  = 0;
        $symbol = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

        if ($bytes > 0) {
            $exp   = floor(log($bytes) / log(1024));
            $value = ($bytes / pow(1024, floor($exp)));
        }

        return sprintf('%.2f ' . $symbol[$exp], $value);
    }
}