<?php
$this->breadcrumbs=array(
	'Inventories'=>array('inventory/index'),
	'Create',
);

$cs=Yii::app()->clientScript;
$cs->registerCoreScript( 'jquery.ui' );
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/css/gridview/styles.css');
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/css/gridview/jquery.yiigridview.js', CClientScript::POS_END);
$cs->registerCssFile((isset(Yii::app()->theme) ? Yii::app()->theme->baseUrl.'/css/gridview' : Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('zii.widgets.assets')).'/gridview') . '/styles.css');

if ($model->inventory_ids) 
    $ivtids = explode(",", $model->inventory_ids);
else 
    throw new CHttpException(404, 'Please choose inventory domain first.');

$ivtmodel = Inventory::model()->with("rdomain")->findAllByPK($ivtids);

$types = Types::model()->findAll("type='site' OR type='channel'");
$gtps = CHtml::listData($types, 'refid', 'typename', 'type');
$_stypes = $gtps['site'] ? $gtps['site'] : array();
$_channels = $gtps['channel'] ? $gtps['channel'] : array();


if (isset($model->tasktype) && $model->tasktype == 1) {
    $cs->registerScriptFile(Yii::app()->baseUrl . '/js/xheditor/xheditor-1.1.12-en.min.js', CClientScript::POS_HEAD);

    if (!$model->style_guide) {
        $model->style_guide = $styleguide;
    }
    $_partial = '_addpresale';
} else {
    $_partial = '_addother';
}

$this->renderPartial($_partial, array(
    'model'=>$model,
    '_stypes'=>$_stypes,
    '_channels'=>$_channels,
    'ivtmodel'=>$ivtmodel,
    '_kws'=>$_kws,
    '_urls'=>$_urls,
));
?>


<style>
ol li {
    margin:2px 0px 2px -10px;
    width:200px;
    padding:0px 18px 0px 0px;
}

table.items td input.clonebtn {
    background-color: #FFFFD0;
    border: 1px solid #8F8377;
    color: #000000;
    font-size: 14px;
    height:20px;
    width:20px;
    padding:0px;
}

table.items input, table.items textarea{
    border: 2px solid #CCCCCC;
    border-radius: 10px;
	-moz-border-radius: 10px;
	-webkit-border-radius: 10px;
    font-size: 1.1em;
    height: 18px;
    padding: 9px;
    width: 180px;
}

table.items select{
    border: 2px solid #CCCCCC;
    border-radius: 10px;
	-moz-border-radius: 10px;
	-webkit-border-radius: 10px;
    height: 38px;
    padding:7px 8px;
}

td dl dd {
    width: 200px;
    margin: 0px;
}

span.hintTitle{
    font-size:13px;
    font-weight:bold;
    color:red;
}
</style>

<script type="text/javascript">
function copytr(t){
    var currtr = $(t).parent().parent();
    currtr.clone().insertAfter(currtr);

    return true;
}

function removetr(t){
    var currtr = $(t).parent().parent();

    //alert(currtr.parent().children().size());
    if(currtr.parent().children().size()<=2){
        alert("Keep one row at least");
    }else{
        currtr.remove();
    }
}
</script>
