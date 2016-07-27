<?php
$this->breadcrumbs=array(
	'Io Historic Reportings'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('io-historic-reporting-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

$types = Types::model()->bytype(array("channel"))->findAll();
$gtps = CHtml::listData($types, 'refid', 'typename', 'type');
$channels = $gtps['channel'] ? $gtps['channel'] : array();
natcasesort($channels);
$channelstr = Utils::array2String($channels);

$tiers = CampaignTask::$tier;
$tierstr = Utils::array2String($tiers);
?>

<h1>Manage Io Historic Reportings</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.<br />
<b style="color:red;">You can use "&lt; &lt;" to search BETWEEN conditon, e.g.: you can type "2013-03-02 &lt;&lt; 2013-05-30" in Date Current, of coz, you can enter "1 &lt;&lt;3" into Time 2 Compelted.</b>
</p>

<!-- search-form -->
<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_ioh_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'io-historic-reporting-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		//'id',
		'task_id',
        array(
            'name' => 'campaign_id',
            'type' => 'raw',
            'value' => 'CHtml::encode($data->rcampaign->name)',
        ),
        array(
            'name'   => 'rcampaign.rclient.name',
            'type'   => 'raw',
            'header' => 'Client',
            'value'  => '$data->rcampaign->rclient->name',
        ),
        array(
            'name' => 'channel_id',
            'type' => 'raw',
            'value' => 'Utils::getValue(' . $channelstr . ', $data->channel_id)',
            'filter' => $channels,
        ),
        array(
            'name' => 'tierlevel',
            'type' => 'raw',
            'value' => 'Utils::getValue(' . $tierstr . ', $data->tierlevel)',
            'filter' => $tiers,
        ),
        //'rcampaign.rclient.name',
		'date_initial',
		'date_current',
		'date_accepted',
		'date_pending',
		'date_approved',
		'date_preqa',
		'date_inrepair',
		'date_completed',

        array(
            'name' => 'time2current',
            'value' => '($data->time2current>0) ? round($data->time2current/86400,2) : 0',
        ),
        array(
            'name' => 'time2accepted',
            'value' => '($data->time2accepted>0) ? round($data->time2accepted/86400,2) : 0',
        ),
        array(
            'name' => 'time2pending',
            'value' => '($data->time2pending>0) ? round($data->time2pending/86400,2) : 0',
        ),
        array(
            'name' => 'time2approved',
            'value' => '($data->time2approved>0) ? round($data->time2approved/86400,2) : 0',
        ),
        array(
            'name' => 'time2preqa',
            'value' => '($data->time2preqa>0) ? round($data->time2preqa/86400,2) : 0',
        ),
        array(
            'name' => 'time2inrepair',
            'value' => '($data->time2inrepair>0) ? round($data->time2inrepair/86400,2) : 0',
        ),
        array(
            'name' => 'time2completed',
            'value' => '($data->time2completed>0) ? round($data->time2completed/86400,2) : 0',
        ),
		/*
		'date_denied',
		'time2current',
		'time2accepted',
		'time2approved',
		'time2pending',
		'time2completed',
		'time2denied',
		array(
			'class'=>'CButtonColumn',
		),
		*/
	),
)); ?>


<script type="text/javascript">
$(document).ready(function() {
    $("#downloadIOH").click(function(){
        var url = "<?php echo Yii::app()->createUrl('/download/iohistoric');?>";
        var rparent = $('#ioSearchForm input[type=hidden][name=r]').parent();
        var rself = rparent.html();
        $('#ioSearchForm input[type=hidden][name=r]').remove();
        url = url + "&" + $('#ioSearchForm').serialize();
        rparent.html(rself);
        window.location.href = url;
    });

});
</script>
