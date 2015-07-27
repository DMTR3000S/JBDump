<?php

namespace SmetDenis\JBDump;

/**
 * Class Data
 * @package SmetDenis\JBDump
 */
class Data extends \ArrayObject
{
    /**
     * The data properties.
     *
     * @type array
     * @since  1.0
     */
    private $properties = array();

    /**
     * Class constructor.
     * Check and store incoming data.
     *
     * @param array $properties
     * @since  1.0
     */
    public function __construct($properties = array())
    {
        parent::__construct($properties);

        // Check the properties
        if (count((array)$properties) !== 0) {
            $this->bind($properties);
        }
    }

    /**
     * @param  string $property
     * @param  mixed  $value
     */
    public function set($property, $value)
    {
        $this->properties[$property] = $value;
    }

    /**
     * @param  string $property
     * @param  mixed  $default
     * @return mixed
     */
    public function get($property, $default = null)
    {
        return isset($this->properties[$property]) ? $this->properties[$property] : $default;
    }

    /**
     * @param  string $property
     * @return bool
     */
    public function has($property)
    {
        return isset($this->properties[$property]) || array_key_exists($property, $this->properties);
    }

    /**
     * @param  string $property
     * @return void
     */
    public function remove($property)
    {
        if (array_key_exists($property, $this->properties)) {
            unset($this->properties[$property]);
        }
    }

    /**
     * Bind properties.
     * @param  array $properties
     * @return $this
     */
    public function bind($properties)
    {
        foreach ($properties as $property => $value) {
            $this->set($property, $value);
        }

        return $this;
    }

    /**
     * Count array properties.
     *
     * @return int
     */
    public function count()
    {
        return count($this->properties);
    }

    /**
     * @param  array $properties
     * @return Data
     */
    public static function _($properties)
    {
        return new static($properties);
    }

    /**
     * Find a key in the data recursively
     *
     * This method finds the given key, searching also in any array or
     * object that's nested under the current data object.
     *
     * Example:
     * <code>
     * $data->find('parentkey.subkey');
     * </code>
     *
     * @param string $key       The key to search for. Can be composed using $separator as the key/subkey separator
     * @param mixed  $default   The default value
     * @param string $separator The separator to use when searching for subkeys. Default is '.'
     *
     * @return mixed The searched value
     *
     * @since 1.0.0
     */
    public function find($key, $default = null, $separator = '.')
    {
        $key   = (string)$key;
        $value = $this->get($key);
        // check if key exists in array
        if ($value !== null) {
            return $value;
        }
        // explode search key and init search data
        $parts = explode($separator, $key);
        $data  = $this;
        foreach ($parts as $part) {
            // handle ArrayObject and Array
            if (($data instanceof \ArrayObject || is_array($data)) && isset($data[$part])) {
                if ($data[$part] === null) {
                    return $default;
                }
                $data =& $data[$part];
                continue;
            }
            // handle object
            if (is_object($data) && isset($data->$part)) {
                if ($data->$part === null) {
                    return $default;
                }
                $data =& $data->$part;
                continue;
            }
            return $default;
        }
        // return existing value
        return $data;
    }

    /**
     * Find a value also in nested arrays/objects
     *
     * @param mixed $needle The value to search for
     *
     * @return string The key of that value
     *
     * @since 1.0.0
     */
    public function searchRecursive($needle)
    {
        $aIt = new \RecursiveArrayIterator($this);
        $it  = new \RecursiveIteratorIterator($aIt);
        while ($it->valid()) {
            if ($it->current() == $needle) {
                return $aIt->key();
            }
            $it->next();
        }
        return false;
    }

    /**
     * Return flattened array copy. Keys are <b>NOT</b> preserved.
     *
     * @return array The flattened array copy
     *
     * @since 1.0.0
     */
    public function flattenRecursive()
    {
        $flat = array();
        foreach (new \RecursiveIteratorIterator(new \RecursiveArrayIterator($this)) as $value) {
            $flat[] = $value;
        }
        return $flat;
    }
}
