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
 * Class Error
 * @package SmetDenis\JBDump
 */
class Error
{
    function __construct()
    {
        if (self::$_config['errors']['errorHandler']) {
            set_error_handler(array($this, '_errorHandler'));
        }

        if (self::$_config['errors']['exceptionHandler']) {
            set_exception_handler(array($this, '_exceptionHandler'));
        }
    }


    /**
     * Force show PHP error messages
     * @param $reportLevel error_reporting level
     * @return bool
     */
    public static function showErrors($reportLevel = -1)
    {
        if (!self::isDebug()) {
            return false;
        }

        if ($reportLevel === null || $reportLevel === false) {
            return false;
        }

        if ($reportLevel != 0) {
            error_reporting($reportLevel);
            ini_set('error_reporting', $reportLevel);
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);

        } else {
            error_reporting(0);
            ini_set('error_reporting', 0);
            ini_set('display_errors', 0);
            ini_set('display_startup_errors', 0);
        }

        return true;
    }


    /**
     * Get PHP error types
     * @return  array
     */
    protected static function _getErrorTypes()
    {
        $errType = array(
            E_ERROR             => 'Error',
            E_WARNING           => 'Warning',
            E_PARSE             => 'Parsing Error',
            E_NOTICE            => 'Notice',
            E_CORE_ERROR        => 'Core Error',
            E_CORE_WARNING      => 'Core Warning',
            E_COMPILE_ERROR     => 'Compile Error',
            E_COMPILE_WARNING   => 'Compile Warning',
            E_USER_ERROR        => 'User Error',
            E_USER_WARNING      => 'User Warning',
            E_USER_NOTICE       => 'User Notice',
            E_STRICT            => 'Runtime Notice',
            E_RECOVERABLE_ERROR => 'Catchable Fatal Error',
        );

        if (defined('E_DEPRECATED')) {
            $errType[E_DEPRECATED]      = 'Deprecated';
            $errType[E_USER_DEPRECATED] = 'User Deprecated';
        }

        $errType[E_ALL] = 'All errors';

        return $errType;
    }

    /**
     * Error handler for PHP errors
     * @param   integer $errNo
     * @param   string  $errMsg
     * @param   string  $errFile
     * @param   integer $errLine
     * @param   array   $errCont
     * @return  bool
     */
    function _errorHandler($errNo, $errMsg, $errFile, $errLine, $errCont)
    {
        $errType = $this->_getErrorTypes();

        $errorMessage = $errType[$errNo] . "\t\"" . trim($errMsg) . "\"\t" . $errFile . ' ' . 'Line:' . $errLine;

        if (self::$_config['errors']['logAll']) {
            error_log('JBDump:' . $errorMessage);
        }

        if (!(error_reporting() & $errNo) || error_reporting() == 0 || (int)ini_get('display_errors') == 0) {

            if (self::$_config['errors']['logHidden']) {
                $errorMessage = date(self::DATE_FORMAT, time()) . ' ' . $errorMessage . PHP_EOL;

                $logPath = self::$_config['log']['path']
                    . '/' . self::$_config['log']['file'] . '_error_' . date('Y.m.d') . '.log';

                error_log($errorMessage, 3, $logPath);
            }

            return false;
        }


        $errFile = $this->_getRalativePath($errFile);
        $result  = array(
            'file'    => $errFile . ' : ' . $errLine,
            'type'    => $errType[$errNo] . ' (' . $errNo . ')',
            'message' => $errMsg,
        );

        if (self::$_config['errors']['context']) {
            $result['context'] = $errCont;
        }

        if (self::$_config['errors']['errorBacktrace']) {
            $trace = debug_backtrace();
            unset($trace[0]);
            $result['backtrace'] = $this->convertTrace($trace);
        }

        if ($this->_isLiteMode()) {
            $errorInfo = array(
                'message' => $result['type'] . ' / ' . $result['message'],
                'file'    => $result['file']
            );
            $this->_dumpRenderLite($errorInfo, '* ' . $errType[$errNo]);

        } else {
            $desc = '<b style="color:red;">*</b> ' . $errType[$errNo] . ' / ' . $this->_htmlChars($result['message']);
            $this->dump($result, $desc);
        }

        return true;
    }

    /**
     * Exception handler
     * @param   Exception $exception PHP exception object
     * @return  boolean
     */
    function _exceptionHandler($exception)
    {
        $result['message'] = $exception->getMessage();

        if (self::$_config['errors']['exceptionBacktrace']) {
            $result['backtrace'] = $this->convertTrace($exception->getTrace());
        }

        $result['string'] = $exception->getTraceAsString();
        $result['code']   = $exception->getCode();

        if ($this->_isLiteMode()) {
            $this->_dumpRenderLite(PHP_EOL . $result['string'], '** EXCEPTION / ' . $this->_htmlChars($result['message']));

        } else {
            $this->_initAssets(true);
            $this->dump($result, '<b style="color:red;">**</b> EXCEPTION / ' . $this->_htmlChars($result['message']));
        }

        return true;
    }

    /**
     * Information about current PHP reporting
     * @return  JBDump
     */
    public static function errors()
    {
        $result                    = array();
        $result['error_reporting'] = error_reporting();
        $errTypes                  = self::_getErrorTypes();

        foreach ($errTypes as $errTypeKey => $errTypeName) {
            if ($result['error_reporting'] & $errTypeKey) {
                $result['show_types'][] = $errTypeName . ' (' . $errTypeKey . ')';
            }
        }

        return self::i()->dump($result, '! errors info !');
    }

}