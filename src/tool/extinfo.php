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
 * Class ToolExtInfo
 * @package SmetDenis\JBDump
 */
class ToolExtInfo extends Tool
{
    /**
     * Dump all info about extension
     * @param   string $extensionName Extension name
     * @return  JBDump
     */
    public static function extInfo($extensionName)
    {
        $result = self::_getExtension($extensionName);
        if ($result) {
            $extensionName = $result['name'];
        }

        return self::i()->dump($result, '! extension (' . $extensionName . ') !');
    }
}