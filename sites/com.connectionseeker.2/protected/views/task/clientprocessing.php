<?php
$this->breadcrumbs=array(
	'Campaign Task'=>array('processing'),
	'Processing',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('task-grid', {
		data: $(this).serialize()
	});
	return false;
});
");


$cs=Yii::app()->clientScript;
$cs->registerCoreScript( 'jquery.ui' );
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/css/gridview/styles.css');
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/css/gridview/jquery.yiigridview.js', CClientScript::POS_END);
$cs->registerCssFile((isset(Yii::app()->theme) ? Yii::app()->theme->baseUrl.'/css/gridview' : Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('zii.widgets.assets')).'/gridview') . '/styles.css');
Yii::app()->clientScript->registerCssFile(
    Yii::app()->clientScript->getCoreScriptUrl(). '/jui/css/base/jquery-ui.css'
);

$types = Types::model()->bytype(array("linktask","channel"))->findAll();
$gtps = CHtml::listData($types, 'refid', 'typename', 'type');
//print_r($gtps);
$linktask = $gtps['linktask'];
$tasktypestr = Utils::array2String($linktask);

$tiers = CampaignTask::$tier;
$tierstr = Utils::array2String($tiers);

$pgstatus = Task::$pgstatus;
$pgstatusstr =  Utils::array2String($pgstatus);

$carts = Cart::model()->findAllByAttributes(array('client_domain_id'=>$cmpmodel->domain_id));
$cartdomains = CHtml::listData($carts, 'domain_id', 'domain');
$desiredstr =  Utils::array2String($cartdomains);
?>

<h1>Campaign Tasks (<?php echo $cmpmodel->name; ?>)</h1>
<div id="processing" style="float:left;width:220px;">&nbsp;</div>
<br />
<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>


<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'task-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
    'selectableRows' => '2',
	'columns'=>array(
        array(
            'name' => 'progressstatus',
            'type' => 'raw',
            'value' => 'Utils::getValue(' . $pgstatusstr . ', $data->progressstatus)',
            'filter' => $pgstatus,
        ),
        'anchortext',
		array(
			'name' => 'targeturl',
			'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->targeturl),$data->targeturl, array("target"=>"_blank"))',
            'htmlOptions'=>array(
                'style'=>'word-wrap: break-word;',
            ),
		),
        array(
            'name' => 'tierlevel',
            'type' => 'raw',
            'value' => 'Utils::getValue(' . $tierstr . ', $data->tierlevel)',
            'filter' => $tiers,
        ),
        array(
            'name' => 'desired_domain_id',
            'type' => 'raw',
            //'value' => '$data->desired_domain_id ? Utils::getValue(' . $desiredstr . ', $data->desired_domain_id, true) : ""',
            'value' => '$data->desired_domain_id ? domain2URL(Utils::getValue(' . $desiredstr . ', $data->desired_domain_id, true), true, array("target"=>"_blank")) : ""',
            //'filter' => $cartdomains,
        ),
		array(
			'name' => 'sourceurl',
			'type' => 'raw',
            'value' => '($data->progressstatus !=4) ? "" : CHtml::link(CHtml::encode($data->sourceurl),$data->sourceurl, array("target"=>"_blank"))',
            'htmlOptions'=>array(
                'style'=>'word-wrap: break-word;',
            ),
		),
        array(
            'name' => 'target_stype',
            'type' => 'raw',
            'value' => 'CHtml::encode($data->target_stype)',
        ),
        'rsummary.googlepr',
        'rsummary.alexarank',
        'rsummary.mozrank',
        'rewritten_title',
        'livedate',
        /*
        'sourceurl',
        'targeturl',
        */
        array(
            'name' => 'client_request',
            'type' => 'raw',
            'value' => 'CHtml::link("Client Request", "javascript:void(0);", array("class"=>"clientrequest"))',
            'visible' => isVisible('client_request', $dparr),
        ),
	),
)); ?>

<div class="clear"></div>

<?php
$this->renderPartial('_clientrequest',array(
	'model'=>$model,
));
?>

<style>
/*
@media screen and (-webkit-min-device-pixel-ratio:0)
{
  .grid-view table.items{
    table-layout:fixed;
  }
}
*/
</style>

<script type="text/javascript">
$(document).ready(function() {
    $.fn.yiiGridView.defaults.afterAjaxUpdate = function(){

        $("a.clientrequest").each(function() {
            $(this).click(function(){
                var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
                var gvoffset = $(this).parent().parent().prevAll().length;
                var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
                $("#crtaskid").val(currenttrid);
                //alert($("#crtaskid").val());
                $( "#cr-dialog-form" ).dialog( "open" );
            });
        });
    }
});
</script>