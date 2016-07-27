<?php
$this->breadcrumbs=array(
	'Blacklists'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'Permission', 'url'=>array('rights/assignment/view')),
	array('label'=>'Create Type', 'url'=>array('types/create')),
	array('label'=>'Manage Type', 'url'=>array('types/index')),
	array('label'=>'Add Blacklist', 'url'=>array('create')),
	array('label'=>'Update Blacklist', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Blacklist', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Blacklist', 'url'=>array('index')),
);
?>

<h1>View Blacklist #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'type',
		'blackvalue',
		'channel_id',
		'notes',
		'created',
		'created_by',
		'modified',
		'modified_by',
	),
)); ?>
