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
 * Class ToolPathInfo
 * @package SmetDenis\JBDump
 */
class ToolPathInfo extends Tool
{
    /**
     * Dump all file info
     * @param   string $file path to file
     * @return  JBDump
     */
    public static function pathInfo($file)
    {
        $result = self::_pathInfo($file);
        return self::i()->dump($result, '! pathInfo (' . $file . ') !');
    }

    /**
     * Get all file info
     * @param   string $path
     * @return  array|bool
     */
    protected static function _pathInfo($path)
    {
        $result = array();

        $filename = realpath($path);

        $result['realpath'] = $filename;
        $result             = array_merge($result, pathinfo($filename));

        $result['type']  = filetype($filename);
        $result['exist'] = file_exists($filename);
        if ($result['exist']) {

            $result['time created']  = filectime($filename) . ' / ' . date(self::DATE_FORMAT, filectime($filename));
            $result['time modified'] = filemtime($filename) . ' / ' . date(self::DATE_FORMAT, filemtime($filename));
            $result['time access']   = fileatime($filename) . ' / ' . date(self::DATE_FORMAT, fileatime($filename));

            $result['group'] = filegroup($filename);
            $result['inode'] = fileinode($filename);
            $result['owner'] = fileowner($filename);
            $perms           = fileperms($filename);

            if (($perms & 0xC000) == 0xC000) { // Socket
                $info = 's';
            } elseif (($perms & 0xA000) == 0xA000) { // Symbolic Link
                $info = 'l';
            } elseif (($perms & 0x8000) == 0x8000) { // Regular
                $info = '-';
            } elseif (($perms & 0x6000) == 0x6000) { // Block special
                $info = 'b';
            } elseif (($perms & 0x4000) == 0x4000) { // Directory
                $info = 'd';
            } elseif (($perms & 0x2000) == 0x2000) { // Character special
                $info = 'c';
            } elseif (($perms & 0x1000) == 0x1000) { // FIFO pipe
                $info = 'p';
            } else { // Unknown
                $info = 'u';
            }

            // owner
            $info .= (($perms & 0x0100) ? 'r' : '-');
            $info .= (($perms & 0x0080) ? 'w' : '-');
            $info .= (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x') : (($perms & 0x0800) ? 'S' : '-'));

            // group
            $info .= (($perms & 0x0020) ? 'r' : '-');
            $info .= (($perms & 0x0010) ? 'w' : '-');
            $info .= (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x') : (($perms & 0x0400) ? 'S' : '-'));

            // other
            $info .= (($perms & 0x0004) ? 'r' : '-');
            $info .= (($perms & 0x0002) ? 'w' : '-');
            $info .= (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x') : (($perms & 0x0200) ? 'T' : '-'));

            $result['perms'] = $perms . ' / ' . $info;

            $result['is_readable'] = is_readable($path);
            $result['is_writable'] = is_writable($path);

            if ($result['type'] == 'file') {

                $size = filesize($filename);

                $result['size'] = $size . ' / ' . self::_formatSize($size);
            }

        } else {
            $result = false;
        }

        return $result;
    }


}