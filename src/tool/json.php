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
 * Class ToolJson
 * @package SmetDenis\JBDump
 */
class ToolJson extends Tool
{
    /**
     * Convert JSON format to human readability
     * @param        $json
     * @param string $name
     * @return bool|JBDump
     */
    public static function json($json, $name = '...')
    {
        if (!self::isDebug()) {
            return false;
        }

        $jsonData = json_decode($json);
        $result   = self::i()->_jsonEncode($jsonData);

        return self::i()->dump($result, $name);
    }


    /**
     * Do the real json encoding adding human readability. Supports automatic indenting with tabs
     * @param array|object $in     The array or object to encode in json
     * @param int          $indent The indentation level. Adds $indent tabs to the string
     * @return string
     */
    protected function _jsonEncode($in, $indent = 0)
    {
        $out = '';

        foreach ($in as $key => $value) {

            $out .= str_repeat("    ", $indent + 1);
            $out .= json_encode((string)$key) . ': ';

            if (is_object($value) || is_array($value)) {
                $out .= $this->_jsonEncode($value, $indent + 1);
            } else {
                $out .= json_encode($value);
            }

            $out .= "," . PHP_EOL;
        }

        if (!empty($out)) {
            $out = substr($out, 0, -2);
        }

        $out = "{" . PHP_EOL . $out . PHP_EOL . str_repeat("    ", $indent) . "}";

        return $out;
    }
}