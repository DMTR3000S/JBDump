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
 * Class Profiler
 * @package SmetDenis\JBDump
 */
class Reflection
{


    /**
     * Get object methods
     * @param   object $object Backtrace
     * @return  array
     */
    protected function _getMethods($object)
    {
        if (is_string($object)) {
            $className = $object;
        } else {
            $className = get_class($object);
        }
        $methods = get_class_methods($className);

        if (self::$_config['sort']['methods']) {
            sort($methods);
        }
        return $methods;
    }

    /**
     * Get all info about class (object)
     * @param   string|object $data Object or class name
     * @return  JBDump
     */
    protected static function _getClass($data)
    {
        // check arg
        if (is_object($data)) {
            $className = get_class($data);
        } elseif (is_string($data)) {
            $className = $data;
        } else {
            return false;
        }

        if (!class_exists($className) && !interface_exists($className)) {
            return false;
        }

        // create ReflectionClass object
        $class = new ReflectionClass($data);

        // get basic class info
        $result['name'] = $class->name;
        $result['type'] = ($class->isInterface() ? 'interface' : 'class');
        if ($classComment = $class->getDocComment()) {
            $result['comment'] = $classComment;
        }
        if ($classPath = $class->getFileName()) {
            $result['path'] = $classPath . ' ' . $class->getStartLine() . '/' . $class->getEndLine();
        }
        if ($classExtName = $class->getExtensionName()) {
            $result['extension'] = $classExtName;
        }
        if ($class->isAbstract()) {
            $result['abstract'] = true;
        }
        if ($class->isFinal()) {
            $result['final'] = true;
        }

        // get all parents of class
        $class_tmp         = $class;
        $result['parents'] = array();
        while ($parent = $class_tmp->getParentClass()) {
            if (isset($parent->name)) {
                $result['parents'][] = $parent->name;
                $class_tmp           = $parent;
            }
        }
        if (count($result['parents']) == 0) {
            unset($result['parents']);
        }

        // reflecting class interfaces
        $interfaces = $class->getInterfaces();
        if (is_array($interfaces)) {
            foreach ($interfaces as $property) {
                $result['interfaces'][] = $property->name;
            }
        }

        // reflection class constants
        $constants = $class->getConstants();
        if (is_array($constants)) {
            foreach ($constants as $key => $property) {
                $result['constants'][$key] = $property;
            }
        }

        // reflecting class properties
        $properties = $class->getProperties();
        if (is_array($properties)) {
            foreach ($properties as $key => $property) {

                if ($property->isPublic()) {
                    $visible = "public";
                } elseif ($property->isProtected()) {
                    $visible = "protected";
                } elseif ($property->isPrivate()) {
                    $visible = "private";
                } else {
                    $visible = "public";
                }

                $propertyName = $property->getName();

                $result['properties'][$visible][$propertyName]['comment'] = $property->getDocComment();
                $result['properties'][$visible][$propertyName]['static']  = $property->isStatic();
                $result['properties'][$visible][$propertyName]['default'] = $property->isDefault();
                $result['properties'][$visible][$propertyName]['class']   = $property->class;
            }
        }

        // get source
        $source = null;
        if (isset($result['path']) && $result['path']) {
            $source = @file($class->getFileName());
            if (!empty($source)) {
                $result['source::source'] = implode('', $source);
            }
        }

        // reflecting class methods
        foreach ($class->getMethods() as $key => $method) {

            if ($method->isPublic()) {
                $visible = "public";
            } elseif ($method->isProtected()) {
                $visible = "protected";
            } elseif ($method->isPrivate()) {
                $visible = "protected";
            } else {
                $visible = "public";
            }

            $result['methods'][$visible][$method->name]['name'] = $method->getName();

            if ($method->isAbstract()) {
                $result['methods'][$visible][$method->name]['abstract'] = true;
            }
            if ($method->isFinal()) {
                $result['methods'][$visible][$method->name]['final'] = true;
            }
            if ($method->isInternal()) {
                $result['methods'][$visible][$method->name]['internal'] = true;
            }
            if ($method->isStatic()) {
                $result['methods'][$visible][$method->name]['static'] = true;
            }
            if ($method->isConstructor()) {
                $result['methods'][$visible][$method->name]['constructor'] = true;
            }
            if ($method->isDestructor()) {
                $result['methods'][$visible][$method->name]['destructor'] = true;
            }
            $result['methods'][$visible][$method->name]['declaringClass'] = $method->getDeclaringClass()->name;

            if ($comment = $method->getDocComment()) {
                $result['methods'][$visible][$method->name]['comment'] = $comment;
            }

            $startLine = $method->getStartLine();
            $endLine   = $method->getEndLine();
            if ($startLine && $source) {
                $from    = (int)($startLine - 1);
                $to      = (int)($endLine - $startLine + 1);
                $slice   = array_slice($source, $from, $to);
                $phpCode = implode('', $slice);

                $result['methods'][$visible][$method->name]['source::source'] = $phpCode;
            }

            if ($params = self::_getParams($method->getParameters(), $method->isInternal())) {
                $result['methods'][$visible][$method->name]['parameters'] = $params;
            }
        }

        // get all methods
        $result['all_methods'] = get_class_methods($className);
        sort($result['all_methods']);

        // sorting properties and methods
        if (isset($result['properties']['protected'])) {
            ksort($result['properties']['protected']);
        }
        if (isset($result['properties']['private'])) {
            ksort($result['properties']['private']);
        }
        if (isset($result['properties']['public'])) {
            ksort($result['properties']['public']);
        }
        if (isset($result['methods']['protected'])) {
            ksort($result['methods']['protected']);
        }
        if (isset($result['methods']['private'])) {
            ksort($result['methods']['private']);
        }
        if (isset($result['methods']['public'])) {
            ksort($result['methods']['public']);
        }

        return $result;
    }

    /**
     * Get function/method params info
     * @param      $params Array of ReflectionParameter
     * @param bool $isInternal
     * @return array
     */
    protected static function _getParams($params, $isInternal = true)
    {

        if (!is_array($params)) {
            $params = array($params);
        }

        $result = array();
        foreach ($params as $param) {
            $optional  = $param->isOptional();
            $paramName = (!$optional ? '*' : '') . $param->name;

            $result[$paramName]['name'] = $param->getName();
            if ($optional && !$isInternal) {
                $result[$paramName]['default'] = $param->getDefaultValue();
            }
            if ($param->allowsNull()) {
                $result[$paramName]['null'] = true;
            }
            if ($param->isArray()) {
                $result[$paramName]['array'] = true;
            }
            if ($param->isPassedByReference()) {
                $result[$paramName]['reference'] = true;
            }
        }

        return $result;
    }

    /**
     * Get all info about function
     * @param   string|function $functionName Function or function name
     * @return  array|bool
     */
    protected static function _getFunction($functionName)
    {
        if (is_string($functionName) && !function_exists($functionName)) {
            return false;

        } elseif (empty($functionName)) {

            return false;
        }

        // create ReflectionFunction instance
        $func = new ReflectionFunction($functionName);

        // get basic function info
        $result         = array();
        $result['name'] = $func->getName();
        $result['type'] = $func->isInternal() ? 'internal' : 'user-defined';

        if (method_exists($func, 'getNamespaceName') && $namespace = $func->getNamespaceName()) {
            $result['namespace'] = $namespace;
        }
        if ($func->isDeprecated()) {
            $result['deprecated'] = true;
        }
        if ($static = $func->getStaticVariables()) {
            $result['static'] = $static;
        }
        if ($reference = $func->returnsReference()) {
            $result['reference'] = $reference;
        }
        if ($path = $func->getFileName()) {
            $result['path'] = $path . ' ' . $func->getStartLine() . '/' . $func->getEndLine();
        }
        if ($parameters = $func->getParameters()) {
            $result['parameters'] = self::_getParams($parameters, $func->isInternal());
        }

        // get function source
        if (isset($result['path']) && $result['path']) {
            $result['comment'] = $func->getDocComment();

            $startLine = $func->getStartLine();
            $endLine   = $func->getEndLine();
            $source    = @file($func->getFileName());

            if ($startLine && $source) {

                $from  = (int)($startLine - 1);
                $to    = (int)($endLine - $startLine + 1);
                $slice = array_slice($source, $from, $to);

                $result['source::source'] = implode('', $slice);

            }
        }

        return $result;
    }


    /**
     * Get all info about function
     * @param string|function $extensionName Function or function name
     * @return array|bool
     */
    protected static function _getExtension($extensionName)
    {
        if (!extension_loaded($extensionName)) {
            return false;
        }

        $ext    = new ReflectionExtension($extensionName);
        $result = array();

        $result['name']    = $ext->name;
        $result['version'] = $ext->getVersion();
        if ($constants = $ext->getConstants()) {
            $result['constants'] = $constants;
        }
        if ($classesName = $ext->getClassNames()) {
            $result['classesName'] = $classesName;
        }
        if ($functions = $ext->getFunctions()) {
            $result['functions'] = $functions;
        }
        if ($dependencies = $ext->getDependencies()) {
            $result['dependencies'] = $dependencies;
        }
        if ($INIEntries = $ext->getINIEntries()) {
            $result['INIEntries'] = $INIEntries;
        }

        $functions = $ext->getFunctions();
        if (is_array($functions) && count($functions) > 0) {
            $result['functions'] = array();
            foreach ($functions as $function) {
                $funcName                       = $function->getName();
                $result['functions'][$funcName] = self::_getFunction($funcName);
            }
        }

        return $result;
    }


}