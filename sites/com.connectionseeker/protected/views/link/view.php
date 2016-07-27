<?php
$this->breadcrumbs=array(
	'Links'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Link', 'url'=>array('index')),
	array('label'=>'Create Link', 'url'=>array('create')),
	array('label'=>'Update Link', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Link', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Link', 'url'=>array('index')),
);
?>

<h1>View Link #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'inventory_id',
		'sourceurl',
		'campaign_id',
		'targeturl',
		'targetdomain',
		'anchortext',
		'category_id',
		'tasktype_id',
		'status',
		'checked',
		'notes',
		'created',
		'created_by',
		'modified',
		'modified_by',
	),
)); ?>
