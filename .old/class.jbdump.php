<?php
/**
 * Library for dump variables and profiling PHP code
 * The idea and the look was taken from Krumo project
 * PHP version 5.3 or higher
 * *
 * Example:<br/>
 *      jbdump($myLoveVariable);<br/>
 *      jbdump($myLoveVariable, false, 'Var name');<br/>
 *      jbdump::mark('Profiler mark');<br/>
 *      jbdump::log('Message to log file');<br/>
 *      jbdump::i()->dump($myLoveVariable);<br/>
 *      jbdump::i()->post()->get()->mark('Profiler mark');<br/>
 * *
 * Simple include in project on index.php file
 * if (file_exists( dirname(__FILE__) . '/class.jbdump.php')) { require_once dirname(__FILE__) . '/class.jbdump.php'; }
 * *
 * @package     JBDump
 * @version     1.4.4
 * @copyright   Copyright (c) 2009-2015 JBDump.org
 * @license     http://www.gnu.org/licenses/gpl.html GNU/GPL
 * @author      SmetDenis <admin@JBDump.org>, <admin@jbzoo.com>
 * @link        http://joomla-book.ru/projects/jbdump
 * @link        http://JBDump.org/
 * @link        http://code.google.com/intl/ru-RU/apis/chart/index.html
 */

// Check PHP version
!version_compare(PHP_VERSION, '5.3.10', '=>') or die('Your host needs to use PHP 5.3.10 or higher to run JBDump');

/**
 * Class JBDump
 */
class JBDump
{



    


}

