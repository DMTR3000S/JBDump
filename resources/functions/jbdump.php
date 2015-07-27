<?php

use SmetDenis\JBDump\Debuger;

/**
 * Alias for JBDump::i()->dump($var) with additions params
 * @param   mixed  $var   Variable
 * @param   string $name  Variable name
 * @param   bool   $isDie Die after dump
 * @return  JBDump
 */
function jbdump($var = 'JBDump::variable is no set', $isDie = true, $name = '...')
{
    $_this = Debuger::i();

    if ($var != 'JBDump::variable is no set') {

        if ($_this->isDebug()) {
            $_this->dump($var, $name);
            $isDie && die('JBDump_auto_die');
        }

    }

    return $_this;
}
