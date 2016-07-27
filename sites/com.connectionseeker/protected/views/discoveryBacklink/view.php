<?php
$this->breadcrumbs=array(
	'Discovery Backlinks'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List DiscoveryBacklink', 'url'=>array('index')),
	array('label'=>'Create DiscoveryBacklink', 'url'=>array('create')),
	array('label'=>'Update DiscoveryBacklink', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete DiscoveryBacklink', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage DiscoveryBacklink', 'url'=>array('index')),
);
?>

<h1>View DiscoveryBacklink #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'competitor_id',
		'discovery_id',
		'domain_id',
		'fresh_called',
		'historic_called',
		'url',
		'domain',
		'googlepr',
		'acrank',
		'anchortext',
		'date',
		'flagredirect',
		'flagframe',
		'flagnofollow',
		'flagimages',
		'flagdeleted',
		'flagalttext',
		'flagmention',
		'targeturl',
	),
)); ?>
