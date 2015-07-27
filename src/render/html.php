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
 * Class RenderHtml
 * @package SmetDenis\JBDump
 */
class RenderHtml
{
    /**
     * Current depth in current dumped object or array
     * @var integer
     */
    protected $_currentDepth = 0;

    /**
     * @param string $data
     * @return string
     */
    protected function _htmlChars($data)
    {
        /*
         * // experimental
        if (function_exists('mb_detect_encoding')) {
            $encoding = mb_detect_encoding($data);
            if ($encoding == 'ASCII') {
                $encoding = 'cp1251';
            }
        }
        */

        $encoding = 'UTF-8';
        if (version_compare(PHP_VERSION, '5.4', '>=')) {
            $flags = ENT_QUOTES | ENT_XML1 | ENT_SUBSTITUTE;
        } else {
            $flags = ENT_QUOTES;
        }

        $data = (string)$data;
        // $data = iconv('WINDOWS-1251', 'UTF-8//TRANSLIT', $data);

        return htmlspecialchars($data, $flags, $encoding, true);
    }

    /**
     * Dump render - HTML
     * @param mixed  $data
     * @param string $varname
     * @param array  $params
     */
    protected function _dumpRenderHtml($data, $varname = '...', $params = array())
    {
        $this->_currentDepth = 0;
        $this->_initAssets();

        if (isset($params['trace'])) {
            $this->_trace = $params['trace'];
        } else {
            $this->_trace = debug_backtrace();
        }

        $text = $this->_getSourceFunction($this->_trace);
        $path = $this->_getSourcePath($this->_trace);
        ?>
        <div id="jbdump">
            <ul class="jbnode">
                <?php $this->_dump($data, $varname); ?>
                <li class="jbfooter">
                    <div class="jbversion">
                        <a href="<?php echo $this->_site; ?>" target="_blank">JBDump v<?php echo self::VERSION; ?></a>
                    </div>
                    <?php if (self::$_config['showCall']) : ?>
                        <div class="jbpath"><?php echo $text . ' ' . $path; ?></div>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
        <?php
    }

    /**
     * Check is current level is expanded
     */
    protected function _isExpandedLevel()
    {
        return $this->_currentDepth <= self::$_config['dump']['expandLevel'];
    }


    /**
     * Maps type variable to a function
     * @param   mixed  $data Mixed data for dump
     * @param   string $name Variable name
     * @return  JBDump
     */
    protected function _dump($data, $name = '...')
    {
        $varType = strtolower(getType($data));

        $advType = false;
        if ($varType == 'string' && preg_match('#(.*)::(.*)#', $name, $matches)) {
            $matches[2] = trim(strToLower($matches[2]));
            if ($this->_strlen($matches[2]) > 0) {
                $advType = $matches[2];
            }
            $name = $matches[1];
        }

        if ($this->_strlen($name) > 80) {
            $name = substr($name, 0, 80) . '...';
        }

        if ($varType == 'null') {
            $this->_null($name);

        } elseif ($varType == 'boolean') {
            $this->_boolean($data, $name, $advType);

        } elseif ($varType == 'integer') {
            $this->_integer($data, $name, $advType);

        } elseif ($varType == 'double') {
            $this->_float($data, $name, $advType);

        } elseif ($varType == 'string') {
            $this->_string($data, $name, $advType);

        } elseif ($varType == 'array') {
            if ($this->_currentDepth <= self::$_config['dump']['maxDepth']) {
                $this->_currentDepth++;
                $this->_array($data, $name, $advType);
                $this->_currentDepth--;
            } else {
                $this->_maxDepth($data, $name, $advType);
            }

        } elseif ($varType == 'object') {
            if ($this->_currentDepth <= self::$_config['dump']['maxDepth']) {
                $this->_currentDepth++;

                if (get_class($data) == 'Closure') {
                    $this->_closure($data, $name, $advType);
                } else {
                    $this->_object($data, $name, $advType);
                }

                $this->_currentDepth--;
            } else {
                $this->_maxDepth($data, $name);
            }

        } elseif ($varType == 'resource') {
            $this->_resource($data, $name, $advType);

        } else {
            $this->_undefined($data, $name, $advType);
        }

        return $this;
    }

    /**
     * Render HTML for object and array
     * @param   array|object $data       Variablevalue
     * @param   bool         $isExpanded Flag is current block expanded
     * @return  void
     */
    protected function _vars($data, $isExpanded = false)
    {
        $_is_object = is_object($data);

        ?>
        <div class="jbnest" style="<?php echo $isExpanded ? 'display:block' : 'display:none'; ?>">
            <ul class="jbnode">
                <?php
                $keys = ($_is_object) ? array_keys(@get_object_vars($data)) : array_keys($data);

                // sorting
                if (self::$_config['sort']['object'] && $_is_object) {
                    sort($keys);
                } elseif (self::$_config['sort']['array']) {
                    sort($keys);
                }

                // get entries
                foreach ($keys as $key) {
                    $value = null;
                    if ($_is_object) {
                        $value = $data->$key;
                    } else {
                        if (array_key_exists($key, $data)) {
                            $value = $data[$key];
                        }
                    }

                    $this->_dump($value, $key);
                }

                // get methods
                if ($_is_object && self::$_config['dump']['showMethods']) {
                    $methods = $this->_getMethods($data);
                    $this->_dump($methods, '&lt;! methods of "' . get_class($data) . '" !&gt;');
                }
                ?>
            </ul>
        </div>
        <?php
    }

    /**
     * Render HTML for NULL type
     * @param   string $name Variable name
     * @return  void
     */
    protected function _null($name)
    {
        ?>
        <li class="jbchild">
            <div class="jbelement">
                <span class="jbname"><?php echo $name; ?></span>
                (<span class="jbtype jbtype-null">NULL</span>)
            </div>
        </li>
        <?php
    }

    /**
     * Render HTML for Boolean type
     * @param   bool   $data Variable
     * @param   string $name Variable name
     * @return  void
     */
    protected function _boolean($data, $name)
    {
        $data = $data ? 'TRUE' : 'FALSE';
        $this->_renderNode('Boolean', $name, '<span style="color:00e;">' . $data . '</span>');
    }

    /**
     * Render HTML for Integer type
     * @param   integer $data Variable
     * @param   string  $name Variable name
     * @return  void
     */
    protected function _integer($data, $name)
    {
        $this->_renderNode('Integer', $name, (int)$data);
    }

    /**
     * Render HTML for float (double) type
     * @param   float  $data Variable
     * @param   string $name Variable name
     * @return  void
     */
    protected function _float($data, $name)
    {
        $this->_renderNode('Float', $name, (float)$data);
    }

    /**
     * Render HTML for resource type
     * @param   resource $data Variable
     * @param   string   $name Variable name
     * @return  void
     */
    protected function _resource($data, $name)
    {
        $data = get_resource_type($data);
        $this->_renderNode('Resource', $name, $data);
    }


    /**
     * Render HTML for string type
     * @param   string $data    Variable
     * @param   string $name    Variable name
     * @param   string $advType String type (parse mode)
     * @return  void
     */
    protected function _string($data, $name, $advType = '')
    {
        $dataLength = $this->_strlen($data);

        $_extra = false;
        if ($advType == 'html') {
            $_extra = true;
            $_      = 'HTML Code';

            $data = '<pre class="jbpreview">' . $data . '</pre>';

        } elseif ($advType == 'source') {

            $data = trim($data);
            if ($data && strpos($data, '<?') !== 0) {
                $_      = 'PHP Code';
                $_extra = true;
                $data   = "<?php" . PHP_EOL . PHP_EOL . $data;
                $data   = '<pre class="jbpreview">' . highlight_string($data, true) . '</pre>';
            } else {
                $_    = '// code not found';
                $data = null;
            }

        } else {
            $_ = $data;

            if (!(
                strpos($data, "\r") === false &&
                strpos($data, "\n") === false &&
                strpos($data, "  ") === false &&
                strpos($data, "\t") === false
            )
            ) {
                $_extra = true;
            } else {
                $_extra = false;
            }

            if ($this->_strlen($data)) {
                if ($this->_strlen($data) > self::$_config['dump']['stringLength']) {
                    if (function_exists('mb_substr')) {
                        $_ = mb_substr($data, 0, self::$_config['dump']['stringLength'] - 3) . '...';
                    } else {
                        $_ = substr($data, 0, self::$_config['dump']['stringLength'] - 3) . '...';
                    }

                    $_extra = true;
                }

                $_    = $this->_htmlChars($_);
                $data = '<textarea readonly="readonly" class="jbpreview">' . $this->_htmlChars($data) . '</textarea>';
            }
        }
        ?>
        <li class="jbchild">
            <div
                class="jbelement <?php echo $_extra ? ' jbexpand' : ''; ?>" <?php if ($_extra) { ?> onClick="jbdump.toggle(this);"<?php } ?>>
                <span class="jbname"><?php echo $name; ?></span>
                (<span class="jbtype jbtype-string">String</span>, <?php echo $dataLength; ?>)
                <span class="jbvalue"><?php echo $_; ?></span>
            </div>
            <?php if ($_extra) { ?>
                <div class="jbnest" style="display:none;">
                    <ul class="jbnode">
                        <li class="jbchild"><?php echo $data; ?></li>
                    </ul>
                </div>
            <?php } ?>
        </li>
        <?php

    }

    /**
     * Render HTML for array type
     * @param   array  $data Variable
     * @param   string $name Variable name
     * @return  void
     */
    protected function _array(array $data, $name)
    {
        $isExpanded = $this->_isExpandedLevel();

        ?>
        <li class="jbchild">
            <div
                class="jbelement<?php echo count($data) > 0 ? ' jbexpand' : ''; ?> <?= $isExpanded ? 'jbopened' : ''; ?>"
                <?php if (count($data) > 0) { ?> onClick="jbdump.toggle(this);"<?php } ?>>
                <span class="jbname"><?php echo $name; ?></span> (<span
                    class="jbtype jbtype-array">Array</span>, <?php echo count($data); ?>)
            </div>
            <?php if (count($data)) {
                $this->_vars($data, $isExpanded);
            } ?>
        </li>
        <?php
    }

    /**
     * Render HTML for object type
     * @param   object $data Variable
     * @param   string $name Variable name
     * @return  void
     */
    protected function _object($data, $name)
    {
        $count      = count(@get_object_vars($data));
        $isExpand   = $count > 0 || self::$_config['dump']['showMethods'];
        $isExpanded = $this->_isExpandedLevel();

        ?>
        <li class="jbchild">
        <div class="jbelement<?php echo $isExpand ? ' jbexpand' : ''; ?> <?= $isExpanded ? 'jbopened' : ''; ?>"
            <?php if ($isExpand) { ?> onClick="jbdump.toggle(this);"<?php } ?>>
            <span class="jbname"><?php echo $name; ?></span>
            (<span class="jbtype jbtype-object"><?php echo get_class($data); ?></span>, <?php echo $count; ?>)
        </div>
        <?php if ($isExpand) {
        $this->_vars($data, $isExpanded);
    } ?>
        <?php
    }

    /**
     * Render HTML for closure type
     * @param   object $data Variable
     * @param   string $name Variable name
     * @return  void
     */
    protected function _closure($data, $name)
    {
        $isExpanded = $this->_isExpandedLevel();

        ?>
        <li class="jbchild">
            <div
                class="jbelement<?php echo count($data) > 0 ? ' jbexpand' : ''; ?> <?= $isExpanded ? 'jbopened' : ''; ?>"
                <?php if (count($data) > 0) { ?> onClick="jbdump.toggle(this);"<?php } ?>>
                <span class="jbname"><?php echo $name; ?></span> (<span class="jbtype jbtype-closure">Closure</span>)
                <span class="jbvalue"><?php echo get_class($data); ?></span>
            </div>
            <?php $this->_vars($this->_getFunction($data), $isExpanded); ?>
        </li>
        <?php
    }

    /**
     * Render HTML for max depth message
     * @param $var
     * @param $name
     * @return void
     */
    protected function _maxDepth($var, $name)
    {
        unset($var);
        $this->_renderNode('max depth', $name, '(<span style="color:red">!</span>) Max depth');
    }

    /**
     * Render HTML for undefined variable
     * @param   mixed  $var  Variable
     * @param   string $name Variable name
     * @return  void
     */
    protected function _undefined($var, $name)
    {
        $this->_renderNode('undefined', $name, '(<span style="color:red">!</span>) getType = ' . gettype($var));
    }

    /**
     * Render HTML for undefined variable
     * @param   string $type Variable type
     * @param   mixed  $data Variable
     * @param   string $name Variable name
     * @return  void
     */
    protected function _renderNode($type, $name, $data)
    {
        $typeAlias = str_replace(' ', '-', strtolower($type));
        ?>
        <li class="jbchild">
            <div class="jbelement">
                <span class="jbname"><?php echo $name; ?></span>
                (<span class="jbtype jbtype-<?php echo $typeAlias; ?>"><?php echo $type; ?></span>)
                <span class="jbvalue"><?php echo $data; ?></span>
            </div>
        </li>
        <?php
    }


}
