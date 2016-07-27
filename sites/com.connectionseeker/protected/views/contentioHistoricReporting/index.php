<?php
$this->breadcrumbs=array(
	'Contentio Historic Reportings'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('contentio-historic-reporting-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

$clients = Client::model()->actived()->findAll(array('order'=>'company ASC'));
?>

<h1>Manage Contentio Historic Reportings</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<!-- search-form -->
<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'contentio-historic-reporting-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'task_id',
        array(
            'header' => 'Client',
            //'name' => 'rcampaign.rclient.company',
            'name' => 'client_id',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->rcampaign->rclient->company), array("client/view", "id" =>$data->rcampaign->client_id))',
            'filter' => CHtml::listData($clients,'id','company'),
        ),
        array(
            'header' => 'Campaign',
            'name' => 'campaign_name',
            'type' => 'raw',
            'value' => 'CHtml::encode($data->rcampaign->name)',
        ),
		'date_step0',
		'date_step1',
		'date_step2',
		'date_step3',
		'date_step4',
		'date_step5',
		'business_days',
		'pro_duration_days',
		/*
		'id',
		'campaign_id',
		'channel_id',
		'tierlevel',
		'time2step0',
		'time2step1',
		'time2step2',
		'time2step3',
		'time2step4',
		'time2step5',
		array(
			'class'=>'CButtonColumn',
		),
		*/
	),
)); ?>
