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
     * Get all available hash from data
     * @param   string $data Data from get hash
     * @return  JBDump
     */
    public static function hash($data)
    {
        $result = array();
        foreach (hash_algos() as $algoritm) {
            $result[$algoritm] = hash($algoritm, $data, false);
        }
        return self::i()->dump($result, '! hash !');
    }
}