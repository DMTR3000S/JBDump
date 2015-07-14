<?php


/**
 * Class array2php
 */
class JBDump_array2php
{

    const LE  = PHP_EOL;
    const TAB = "    ";

    /**
     * @param      $array
     * @param null $varName
     * @return string
     */
    public static function toString($array, $varName = null, $shift = 0)
    {
        $self     = new self();
        $rendered = $self->_render($array, 0);

        if ($shift > 0) {
            $rendered = explode(self::LE, $rendered);

            foreach ($rendered as $key => $line) {
                $rendered[$key] = $self->_getIndent($shift) . $line;
            }

            $rendered[0] = ltrim($rendered[0]);
            $rendered    = implode(self::LE, $rendered);
        }

        if ($varName) {
            return PHP_EOL . $self->_getIndent($shift) . "\$" . $varName . ' = ' . $rendered . ";" . PHP_EOL . " " . self::TAB;
        }

        return $rendered;
    }

    /**
     * @param     $array
     * @param int $depth
     * @return string
     */
    protected function _render($array, $depth = 0)
    {
        $isObject = false;

        if ($depth >= 10) {
            return 'null /* MAX DEEP REACHED! */';
        }

        if (is_object($array)) {
            $isObject = get_class($array);
            $array    = (array)$array;
        }

        if (!is_array($array)) {
            return 'null /* undefined var */';
        }

        if (empty($array)) {
            return $isObject ? '(object)array( /* Object: "' . $isObject . '" */)' : 'array()';
        }

        $string = 'array( ' . self::LE;
        if ($isObject) {
            $string = '(object)array( ' . self::LE . $this->_getIndent($depth + 1) . '/* Object: "' . $isObject . '" */ ' . self::LE;
        }

        $depth++;
        foreach ($array as $key => $val) {
            $string .= $this->_getIndent($depth) . $this->_quoteWrap($key) . ' => ';

            if (is_array($val) || is_object($val)) {
                $string .= $this->_render($val, $depth) . ',' . self::LE;
            } else {
                $string .= $this->_quoteWrap($val) . ',' . self::LE;
            }
        }

        $depth--;
        $string .= $this->_getIndent($depth) . ')';

        return $string;
    }

    /**
     * @param $depth
     * @return string
     */
    protected function _getIndent($depth)
    {
        return str_repeat(self::TAB, $depth);
    }

    /**
     * @param $var
     * @return string
     */
    protected function _quoteWrap($var)
    {
        $type = strtolower(gettype($var));

        switch ($type) {
            case 'string':
                return "'" . str_replace("'", "\\'", $var) . "'";

            case 'null':
                return "null";

            case 'boolean':
                return $var ? 'TRUE' : 'FALSE';

            case 'object':
                return '"{ Object: ' . get_class($var) . ' }"';

            //TODO: handle other variable types.. ( objects? )
            case 'integer':
            case 'double':
            default :
                return $var;
        }
    }
}
