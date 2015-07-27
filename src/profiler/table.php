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
 * Class ToolClasses
 * @package SmetDenis\JBDump
 */
class ProfilerTable extends Tool
{
    /**
     * Profiler render - table
     */
    protected function _profilerRenderTable()
    {
        $this->_initAssets();
        ?>
        <div id="jbdump_profile_chart_table" style="max-width: 1000px;margin:0 auto;text-align:left;"></div>
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript">
            google.load('visualization', '1', {packages: ['table']});
            google.setOnLoadCallback(function () {

                var data = new google.visualization.DataTable();

                data.addColumn('number', '#');
                data.addColumn('string', 'label');
                data.addColumn('string', 'file');
                data.addColumn('number', 'time, ms');
                data.addColumn('number', 'time delta, ms');
                data.addColumn('number', 'memory, MB');
                data.addColumn('number', 'memory delta, MB');

                data.addRows(<?php echo count($this->_bufferInfo);?>);

                <?php
                $i = 0;
                foreach ($this->_bufferInfo as $key=> $mark) : ?>
                data.setCell(<?php echo $key;?>, 0, <?php echo ++$i;?>);
                data.setCell(<?php echo $key;?>, 1, '<?php echo $mark['label'];?>');
                data.setCell(<?php echo $key;?>, 2, '<?php echo $mark['trace'];?>');
                data.setCell(<?php echo $key;?>, 3, <?php echo self::_profilerFormatTime($mark['time']);?>);
                data.setCell(<?php echo $key;?>, 4, <?php echo self::_profilerFormatTime($mark['timeDiff']);?>);
                data.setCell(<?php echo $key;?>, 5, <?php echo self::_profilerFormatMemory($mark['memory']);?>);
                data.setCell(<?php echo $key;?>, 6, "<?php echo self::_profilerFormatMemory($mark['memoryDiff']);?>");
                <?php endforeach; ?>

                var formatter = new google.visualization.TableBarFormat({width: 120});
                formatter.format(data, 4);
                formatter.format(data, 6);

                var table = new google.visualization.Table(document.getElementById('jbdump_profile_chart_table'));
                table.draw(data, {
                    allowHtml    : true,
                    showRowNumber: false
                });
            });
        </script>
        <?php
    }
}
