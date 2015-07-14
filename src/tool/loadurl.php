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
 * Class ToolLoadurl
 * @package SmetDenis\JBDump
 */
class ToolLoadurl extends Tool
{
    /**
     * @param string $url
     * @param array  $data
     * @param string $method
     * @param array  $params
     * @return JBDump
     */
    public static function loadUrl($url, $data = array(), $method = 'get', $params = array())
    {
        $result = array(
            'lib'     => '',
            'code'    => 0,
            'headers' => array(),
            'body'    => null,
            'error'   => null,
            'info'    => null,
        );

        $method    = trim(strtolower($method));
        $queryData = http_build_query((array)$data, null, '&');
        if ($method == 'get') {
            $url = $url . (strpos($url, '?') === false ? '?' : '&') . $queryData;
        }

        if (function_exists('curl_init') && is_callable('curl_init')) {
            $result['lib'] = 'cUrl';

            $options = array(
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => true,     // return web page
                CURLOPT_HEADER         => true,     // return headers
                CURLOPT_ENCODING       => "",       // handle all encodings
                CURLOPT_USERAGENT      => "JBDump", // who am i
                CURLOPT_AUTOREFERER    => true,     // set referer on redirect
                CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
                CURLOPT_TIMEOUT        => 120,      // timeout on response
                CURLOPT_MAXREDIRS      => 20,       // stop after 10 redirects

                // Disabled SSL Cert checks
                CURLOPT_SSL_VERIFYPEER => isset($params['ssl']) ? $params['ssl'] : true,

                CURLOPT_HTTPHEADER     => array(
                    'Expect:', // http://the-stickman.com/web-development/php-and-curl-disabling-100-continue-header/
                    'Content-Type:application/x-www-form-urlencoded; charset=utf-8',
                ),
            );
            if (isset($params['cert'])) {
                $options[CURLOPT_CAINFO] = __DIR__ . '/jbdump.pem';
            }

            if (!ini_get('safe_mode') && !ini_get('open_basedir')) {
                $options[CURLOPT_FOLLOWLOCATION] = true;
            }

            if ($method == 'post') {
                $options[CURLOPT_POSTFIELDS] = $queryData;
                $options[CURLOPT_POST]       = true;
            }

            $ch = curl_init($url);
            curl_setopt_array($ch, $options);
            $result['full'] = curl_exec($ch);

            if (curl_errno($ch) || curl_error($ch)) {
                $result['error'] = '#' . curl_errno($ch) . ' - "' . curl_error($ch) . '"';
            }

            $info = curl_getinfo($ch);
            curl_close($ch);

            // parse response
            $redirects      = isset($info['redirect_count']) ? $info['redirect_count'] : 0;
            $response       = explode("\r\n\r\n", $result['full'], 2 + $redirects);
            $result['body'] = array_pop($response);
            $headers        = explode("\r\n", array_pop($response));
            // code
            preg_match('/[0-9]{3}/', array_shift($headers), $matches);
            $result['code'] = count($matches) ? $matches[0] : null;

            // parse headers
            $resHeaders = array();
            foreach ($headers as $header) {
                $pos   = strpos($header, ':');
                $name  = trim(substr($header, 0, $pos));
                $value = trim(substr($header, ($pos + 1)));

                $resHeaders[$name] = $value;
            }

            $result['info']    = $info;
            $result['headers'] = $resHeaders;

        } else {
            $result['lib'] = 'file_get_contents';

            $context = null;
            if ($method == 'post') {
                $context = stream_context_create(array('http' => array(
                    'method'  => 'POST',
                    'header'  => 'Content-type: application/x-www-form-urlencoded',
                    'content' => $queryData
                )));
            }

            $result['full'] = file_get_contents($url, false, $context);
        }

        return self::i()->dump($result, 'Load URL');
    }
}