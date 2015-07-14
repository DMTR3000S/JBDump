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
 * Class ToolHeaders
 * @package SmetDenis\JBDump
 */
class ToolHeaders extends Tool
{
    /**
     * Show HTTP headers
     * @return  JBDump
     */
    public static function headers()
    {
        if (!self::isDebug()) {
            return false;
        }

        if (function_exists('apache_request_headers')) {
            $data = array(
                'Request'  => apache_request_headers(),
                'Response' => apache_response_headers(),
                'List'     => headers_list()
            );

        } else {
            $data = array(
                'List' => headers_list()
            );
        }

        if (headers_sent($filename, $linenum)) {
            $data['Sent'] = 'Headers already sent in ' . self::i()->_getRalativePath($filename) . ':' . $linenum;
        } else {
            $data['Sent'] = false;
        }

        return self::i()->dump($data, '! headers !');
    }
}