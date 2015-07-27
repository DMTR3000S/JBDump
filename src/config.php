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
 * Class Config
 * @package SmetDenis\JBDump
 */
class Config
{

    public function getList($params = array())
    {
        $default = array(

            'global'    => array(
                'date_short' => 'Y-m-d',
                'date_long'  => 'Y-m-d H:i:s',
                'doc_root'   => $_SERVER['DOCUMENT_ROOT'], // project root directory
            ),

            'dump'      => array(
                'maxDepth'   => 3, // the maximum depth of the dump
                'show_call'  => 1,
                'show_level' => 1, // expand the list to the specified depth
                'die'        => 1, // die after dumping variable
                'render'     => array(
                    'auto',
                    // 'cli',
                    // 'header',
                    // 'html',
                    // 'lite',
                    // 'log',
                    // 'mail',
                    // 'php_array',
                    // 'print_r',
                    // 'var_dump',
                ),

                'array'      => array(
                    'sort' => 0,
                ),

                'object'     => array(
                    'sort_props'     => 1,
                    'sort_methods'   => 1,

                    'show_methods'   => 1,
                    'show_private'   => 1,
                    'show_protected' => 1,
                    'show_other'     => 1,

                    'use_casters'    => 1,
                ),

                'string'     => array(
                    'anons'           => 80, // cutting long string for anons
                    'show_spec_chars' => 1,
                    'force_utf'       => 0,
                    'force_extra'     => 0,
                ),

                'closure'    => array(
                    'show_code' => 1,
                ),

                'extra'      => array(
                    'json' => 1,
                )
            ),

            'backtrace' => array(
                'show_args' => 0, // show Args in backtrace
            ),

            'personal'  => array(
                'ip'      => array( // IP address for which to work debugging
                    //'127.0.0.0'
                ),
                'request' => array( // $_REQUEST for which to work debugging
                    'key'   => false,
                    'value' => false,
                )
            ),

            'logger'    => array(
                'path'      => './logs/', // absolute log path
                'file'      => 'jbdump', // log filename
                'format'    => "{DATETIME}\t{CLIENT_IP}\t\t{FILE}\t\t{NAME}\t\t{MESSAGE}", // fields in log file
                'serialize' => 'print_r', // (none|json|serialize|print_r|var_dump|format|php_array)
            ),

            'profiler'  => array(
                'auto'        => 1, // Result call automatically on destructor
                'render'      => array( // Profiler renders
                    'auto',
                    // 'log',
                    // 'echo',
                    // 'table',
                    // 'chart',
                    // 'total',
                ),
                'mark_start'  => 0, // Set auto mark after jbdump init
                'mark_end'    => 0, // Set auto mark before jbdump destruction
                'trace_limit' => 3, // Limit for function JBDump::incTrace();
            ),

            'error'     => array(
                'reporting_level'   => null, // set error reporting level while construct

                'error_handler'     => 0, // register own handler for PHP errors
                'error_trace'       => 0, // show backtrace for errors
                'error_context'     => 0, // show context for errors

                'exception_handler' => 0, // register own handler for all exceptions
                'exception_trace'   => 0, // show backtrace for exceptions
                'exception_context' => 0, // show context for errors

                'log_hidden'        => 0, // if error message not show, log it
                'log_all'           => 0, // log all error in syslog
            ),

            'mail'      => array(
                'to'      => 'jbdump@example.com', // mail to
                'subject' => 'JBDump debug', // mail subject
            ),

        );

        $params = array_merge_recursive($default, $params);

        return $params;
    }

}