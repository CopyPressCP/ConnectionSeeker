<?php
$this->breadcrumbs=array(
	'Emails'=>array('index'),
	'Report',
);

$cs=Yii::app()->clientScript;
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/css/gridview/styles.css');


//$fttemplates = CHtml::listData(Template::model()->findAll(),'id','subject');
$lc = count($report);
?>

<h1>Emails Report</h1>

<div id="report-grid" class="grid-view">
<div class="summary">Displaying 1-<?php echo $lc;?> of <?php echo $lc;?> result(s).</div>
<table class="items">
<thead>
<tr>
<th id="report-grid_c0">TPL#ID</th>
<th id="report-grid_c1">Subject</th>
<?php foreach($last7 as $v) {?>
<th id="report-grid_c2"><?php echo $v;?></th>
<?php }?>
<th id="report-grid_c3">Last 7 days</th>
<th id="report-grid_c4">Last 30 days</th>
<th id="report-grid_c5">Lifetime</th>
</tr>
</thead>
<tbody>
<?php
if ($report) {
    $i = 0;
    foreach ($report as $k => $v) {
        $class = ($i % 2) ? "odd" : "even"; 
?>
<tr class="<?php echo $class;?>">
<td><?php echo $k;?></td>
<td><?php echo $v['subject'];?></td>

<?php foreach($last7 as $_k => $_v) {
    echo "<td nowrap='nowrap'>";
    if ($v['days'] && in_array($_k, array_keys($v['days']))) {
        $internalsend = "0";
        $oprate = "0%";
        $clcrate = "0%";
        $_day = $v['days'][$_k];
        if (in_array("open", array_keys($_day)) || in_array("click", array_keys($_day)) 
                                                || in_array("internalsend", array_keys($_day))) {
            //$events = array_push(, );
            foreach ($events as $e) {
                if (in_array($e, array("open","click")) && $_day[$e]) {
                    if ($e == "open") {
                        $oprate = round(($_day[$e] * 100) / $_day['total'], 1) . "%";
                    } else {
                        $clcrate = round(($_day[$e] * 100) / $_day['total'], 1) . "%";
                    }
                }
            }
            if ($_day["internalsend"]) {
                $internalsend = $_day["internalsend"];
            }
            echo $internalsend . " | " . $oprate . " | ". $clcrate;
        } else {
            echo "0 | 0% | 0%";
        }

    } else {
        echo "0 | 0% | 0%";
    }

    echo "</td>";
}?>

<td nowrap="nowrap">
<?php
echo ($v['total7internalsend']) ? $v['total7internalsend'] . " | " : "0 | ";
if ($total7) {
    echo ($v['total7open']) ? round(($v['total7open'] * 100) / $total7, 1) ."% | " : "0% | ";
    echo ($v['total7click']) ? round(($v['total7click'] * 100) / $total7, 1) ."% "  : "0%";
} else {
    echo "0% | 0%";
}
?>
</td>
<td nowrap="nowrap">
<?php
echo ($v['total30internalsend']) ? $v['total30internalsend'] . " | " : "0 | ";

if ($v['total30']) {
    echo ($v['total30open']) ? round(($v['total30open'] * 100) / $v['total30'], 1) ."% | " : "0% | ";
    echo ($v['total30click']) ? round(($v['total30click'] * 100) / $v['total30'], 1) ."% "  : "0%";
} else {
    echo "0% | 0%";
}
?>
</td>
<td nowrap="nowrap">
<?php
echo ($v['totalinternalsend']) ? $v['totalinternalsend'] . " | " : "0 | ";
if ($v['total']) {
    echo ($v['totalopen']) ? round(($v['totalopen'] * 100) / $v['total'], 1) ."% | " : "0% | ";
    echo ($v['totalclick']) ? round(($v['totalclick'] * 100) / $v['total'], 1) ."%"  : "0%";
} else {
    echo "0% | 0%";
}
?>
</td>
</tr>
<?php
        $i++;
    }
}
?>
</tbody>
</table>
</div>