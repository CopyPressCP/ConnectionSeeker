<?php
$this->breadcrumbs=array(
	'Links'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('link-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

$campaigns = CHtml::listData(Campaign::model()->findAll(), 'id', 'name');
?>

<h1>Manage Links</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<!-- search-form -->
<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
	'campaigns'=>$campaigns,
)); ?>
<!-- search-form -->
</div>

<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'link-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
        array(
            'name' => 'id',
            'type' => 'raw',
            'value' => '$data->id',
            'htmlOptions' => array('style'=>'width:35px;','nowrap'=>'nowrap'),
        ),
		'sourceurl',
        array(
            'name' => 'campaign_id',
            'type' => 'raw',
            'value' => 'CHtml::encode($data->rcampaign->name)',
            'filter' => $campaigns,
        ),
		'targeturl',
		'anchortext',
		/*
        'rcampaign.name',
		'campaign_id',
		'inventory_id',
		'targetdomain',
		'category_id',
		'tasktype_id',
		'status',
		'checked',
		'notes',
		'created',
		'created_by',
		'modified',
		'modified_by',
		*/
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
