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
class ToolClasses extends Tool
{
    /**
     * Profiler render - table
     */
    protected function _profilerRenderChart()
    {
        ?>
        <div id="jbdump_profilter_chart_time" style="max-width: 1000px;margin:0 auto;text-align:left;"></div>
        <div id="jbdump_profilter_chart_memory" style="max-width: 1000px;margin:0 auto;text-align:left;"></div>
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript">
            google.load("visualization", "1", {packages: ["corechart"]});
            google.setOnLoadCallback(function drawChart() {
                //////////////////////////// time ////////////////////////////
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Label');
                data.addColumn('number', 'time, ms');
                data.addColumn('number', 'time delta, ms');
                data.addRows([
                    <?php
                    foreach ($this->_bufferInfo as $mark) {
                        echo '[\'' . $mark['label'] . '\', '
                                . self::_profilerFormatTime($mark['time']) . ', '
                                . self::_profilerFormatTime($mark['timeDiff']) . '],';
                    } ?>
                ]);

                var chart = new google.visualization.LineChart(document.getElementById('jbdump_profilter_chart_time'));
                chart.draw(data, {
                    'width' : 750,
                    'height': 400,
                    'title' : 'JBDump profiler by time'
                });

                //////////////////////////// memory ////////////////////////////
                var data = new google.visualization.DataTable();

                data.addColumn('string', 'Label');
                data.addColumn('number', 'memory, MB');
                data.addColumn('number', 'memory delta, MB');
                data.addRows([
                    <?php
                    foreach ($this->_bufferInfo as $mark) {
                        echo '[\'' . $mark['label'] . '\', '
                                . self::_profilerFormatMemory($mark['memory']) . ', '
                                . self::_profilerFormatMemory($mark['memoryDiff']) . '],';
                    } ?>
                ]);

                var chart = new google.visualization.LineChart(document.getElementById('jbdump_profilter_chart_memory'));
                chart.draw(data, {
                    'width' : 750,
                    'height': 400,
                    'title' : 'JBDump profiler by memory'
                });
            });
        </script>
        <?php
    }
}