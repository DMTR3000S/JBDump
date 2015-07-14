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
 * Class Logger
 * @package SmetDenis\JBDump
 */
class Logger
{

    /**
     * Absolute path current log file
     * @var string|resource
     */
    protected $_logfile = null;

    /**
     * Absolute path for all log files
     * @var string
     */
    protected $_logPath = null;

    /**
     * Add message to log file
     * @param   mixed  $entry    Text to log file
     * @param   string $markName Name of log record
     * @param   array  $params   Additional params
     * @return  JBDump
     */
    public static function log($entry, $markName = '...', $params = array())
    {
        if (!self::isDebug()) {
            return false;
        }

        // emulate normal class
        $_this = self::i();

        // check var type
        if (is_bool($entry)) {
            $entry = ($entry) ? 'TRUE' : 'FALSE';
        } elseif (is_null($entry)) {
            $entry = 'NULL';
        } elseif (is_resource($entry)) {
            $entry = 'resource of "' . get_resource_type($entry) . '"';
        }

        // serialize type
        if (self::$_config['log']['serialize'] == 'formats') {
            // don't change log entry

        } elseif (self::$_config['log']['serialize'] == 'none') {
            $entry = array('jbdump_message' => $entry);

        } elseif (self::$_config['log']['serialize'] == 'json') {
            $entry = array('jbdump_message' => @json_encode($entry));

        } elseif (self::$_config['log']['serialize'] == 'serialize') {
            $entry = array('jbdump_message' => serialize($entry));

        } elseif (self::$_config['log']['serialize'] == 'print_r') {
            $entry = array('jbdump_message' => print_r($entry, true));

        } elseif (self::$_config['log']['serialize'] == 'php_array') {
            $markName = (empty($markName) || $markName == '...') ? 'dumpVar' : $markName;
            $entry    = array('jbdump_message' => JBDump_array2php::toString($entry, $markName));

        } elseif (self::$_config['log']['serialize'] == 'var_dump') {
            ob_start();
            var_dump($entry);
            $entry = ob_get_clean();
            $entry = array('jbdump_message' => var_dump($entry, true));
        }

        if (isset($params['trace'])) {
            $_this->_trace = $params['trace'];
        } else {
            $_this->_trace = debug_backtrace();
        }

        $entry['name']      = $markName;
        $entry['datetime']  = date(self::DATE_FORMAT);
        $entry['client_ip'] = self::getClientIP();
        $entry['file']      = $_this->_getSourcePath($_this->_trace, true);
        $entry              = array_change_key_case($entry, CASE_UPPER);

        $fields = array();
        $format = isset($params['format']) ? $params['format'] : self::$_config['log']['format'];
        preg_match_all("/{(.*?)}/i", $format, $fields);

        // Fill in the field data
        $line = $format;
        for ($i = 0; $i < count($fields[0]); $i++) {
            $line = str_replace($fields[0][$i], (isset ($entry[$fields[1][$i]])) ? $entry[$fields[1][$i]] : "-", $line);
        }

        // Write the log entry line
        if ($_this->_openLog()) {
            error_log($line . PHP_EOL, 3, $_this->_logfile);
        }

        return $_this;
    }

    /**
     * Open log file
     * @return  bool
     */
    function _openLog()
    {

        if (!@file_exists($this->_logfile)) {

            if (!is_dir($this->_logPath) && $this->_logPath) {
                mkdir($this->_logPath, 0777, true);
            }

            $header[] = "#<?php die('Direct Access To Log Files Not Permitted'); ?>";
            $header[] = "#Date: " . date(DATE_RFC822, time());
            $header[] = "#Software: JBDump v" . self::VERSION . ' by Joomla-book.ru';
            $fields   = str_replace("{", "", self::$_config['log']['format']);
            $fields   = str_replace("}", "", $fields);
            $fields   = strtolower($fields);
            $header[] = '#' . str_replace("\t", "\t", $fields);

            $head = implode(PHP_EOL, $header);
        } else {
            $head = false;
        }

        if ($head) {
            error_log($head . PHP_EOL, 3, $this->_logfile);
        }

        return true;
    }
}