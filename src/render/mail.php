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
 * Class RenderMail
 * @package SmetDenis\JBDump
 */
class RenderMail
{
    /**
     * Dump render - send to email
     * @param mixed  $data
     * @param string $varname
     * @param array  $params
     */
    protected function _dumpRenderMail($data, $varname = '...', $params = array())
    {
        $this->mail(array(
            'varname' => $varname,
            'data'    => $data
        ));
    }


    /**
     * Send message mail
     * @param mixed  $text
     * @param string $subject
     * @param string $to
     * @return bool
     */
    public static function mail($text, $subject = null, $to = null)
    {
        if (!self::isDebug()) {
            return false;
        }

        $_this = self::i();

        if (empty($subject)) {
            $subject = self::$_config['mail']['subject'];
        }

        if (empty($to)) {
            $to = isset(self::$_config['mail']['to'])
                ? self::$_config['mail']['to']
                : 'jbdump@' . $_SERVER['HTTP_HOST'];
        }

        if (is_array($to)) {
            $to = implode(', ', $to);
        }

        // message
        $message   = array();
        $message[] = '<html><body>';
        $message[] = '<p><b>JBDump mail from '
            . '<a href="http://' . $_SERVER['HTTP_HOST'] . '">' . $_SERVER['HTTP_HOST'] . '</a>'
            . '</b></p>';

        $message[] = '<p><b>Date</b>: ' . date(DATE_RFC822, time()) . '</p>';
        $message[] = '<p><b>IP</b>: ' . self::getClientIP() . '</p>';
        $message[] = '<b>Debug message</b>: <pre>' . print_r($text, true) . '</pre>';
        $message[] = '</body></html>';
        $message   = wordwrap(implode(PHP_EOL, $message), 70);

        // To send HTML mail, the Content-type header must be set
        $headers   = array();
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=utf-8';
        $headers[] = 'To: ' . $to;
        $headers[] = 'From: JBDump debug <jbdump@' . $_SERVER['HTTP_HOST'] . '>';
        $headers[] = 'X-Mailer: JBDump v' . self::VERSION;
        $headers   = implode("\r\n", $headers);

        $result = mail($to, $subject, $message, $headers);
        if (self::$_config['mail']['log']) {
            $_this->log(
                array(
                    'email'   => $to,
                    'subject' => $subject,
                    'message' => $message,
                    'headers' => $headers,
                    'result'  => $result
                ),
                'JBDump::mail'
            );
        }

        return $result;
    }
}
