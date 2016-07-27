<?php
$cs=Yii::app()->clientScript;
$cs->registerCoreScript( 'jquery.ui' );
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/css/gridview/styles.css');
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/css/gridview/jquery.yiigridview.js', CClientScript::POS_END);
$cs->registerCssFile((isset(Yii::app()->theme) ? Yii::app()->theme->baseUrl.'/css/gridview' : Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('zii.widgets.assets')).'/gridview') . '/styles.css');
Yii::app()->clientScript->registerCssFile(
    Yii::app()->clientScript->getCoreScriptUrl(). '/jui/css/base/jquery-ui.css'
);


$types = Types::model()->bytype(array("channel"))->findAll();
$gtps = CHtml::listData($types, 'refid', 'typename', 'type');

$channels = $gtps['channel'] ? $gtps['channel'] : array();
natcasesort($channels);
$channelstr = Utils::array2String($channels);

$iostatuses = Task::$iostatuses;
$iostatusesstr =  Utils::array2String($iostatuses);
//$iolabel = $iostatuses[$iostatus];

/*
$isvisible = false;
if ($iostatus == 1 && !isset($roles['Marketer'])) {
    $isvisible = true;
}
$isadmin = isset($roles['Admin']) ? 1 : 0;
$ispublisher = isset($roles['Publisher']) ? 1 : 0;
$isoutreacher = (isset($roles['InternalOutreach']) || isset($roles['Outreach'])) ? 1 : 0;
$ismarketer = isset($roles['Marketer']) ? 1 : 0;
*/
if (!$currentaction) $currentaction = "published";
$reasonstr = Utils::array2String($reasons);
function getArrValue($arr, $key, $key2d, $isreturn = false){
    $rtn = "";
    if ($arr && $arr[$key]) {
        if ($arr[$key] && $arr[$key][$key2d]) {
            $rtn = $arr[$key][$key2d];
        }
    }
    if ($key2d == 'reason' && empty($rtn)) {
        $rtn = "Non Left";
    }

    if ($isreturn) {
        return $rtn;
    } else {
        echo $rtn;
    }
}

//print_r($reasons);
?>


<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'task-grid',
	'dataProvider'=>$model->search(),
    'enableSorting' => false,
	'columns'=>array(
        array(
            'name' => 'iostatus',
            'type' => 'raw',
            'value' => '$data->iostatus ? Utils::getValue(' . $iostatusesstr . ', $data->iostatus) : ""',
        ),
        array(
            'name' => 'iodate',
            //'header' => 'Date '.$iolabel,
            'header' => 'Date ',
            'value' => '$data->iodate ? $data->iodate : "00-00-0000"',
        ),
        /*
        array(
            'name' => 'id',
            'header' => 'Task ID',
        ),
        array(
            'name' => 'channel_id',
            'type' => 'raw',
            'value' => 'Utils::getValue(' . $channelstr . ', $data->channel_id)',
        ),
        array(
            'name' => 'rcampaign.name',
            'header' => 'Campaign',
            'type' => 'raw',
            'value' => '$data->rcampaign->name',
        ),
        array(
            'name' => 'id',
            'type' => 'raw',
            'header' => 'Reason',
            'value' => 'CHtml::encode(getArrValue(' . $reasonstr . ', $data->id, "reason"))',
            'visible' => ($currentaction == 'denied'),
        ),
        array(
            'name' => 'id',
            'type' => 'raw',
            'header' => 'Denied By',
            'value' => 'CHtml::encode(getArrValue(' . $reasonstr . ', $data->id, "createdby"))',
            'visible' => ($currentaction == 'denied'),
        ),
        */
        //'rcampaign.rclient.name',
        //'rcampaign.name',
        array(
            'name' => 'rcampaign.rclient.name',
            'header' => 'Client',
            'type' => 'raw',
            'value' => '$data->rcampaign->rclient->name',
        ),

        'anchortext',
        array(
            'name' => 'targeturl',
            'type' => 'raw',
            'value' => 'domain2URL($data->targeturl,true)',
        ),
        array(
            'name' => 'sourceurl',
            'type' => 'raw',
            'value' => 'domain2URL($data->sourceurl,true)',
        ),
        //'livedate',
	),
)); ?>

<div class="clear"></div>
