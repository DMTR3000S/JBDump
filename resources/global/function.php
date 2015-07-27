<?php

if (!function_exists('jbdump')) {

    /**
     * Alias for JBDump::dump($var) with additions params
     * @return \SmetDenis\JBDump\JBDump
     */
    function jbdump()
    {
        $debuger = JBDump::i();

        call_user_func(
            array('SmetDenis\\JBDump\\JBDump', 'dump'),
            func_get_args()
        );

        return $debuger;
    }

}

/**
 * @param      $var
 * @param bool $isDie
 */
function dump($var, $isDie = true)
{
    print_r($var);

    if ($isDie) {
        die;
    }
}