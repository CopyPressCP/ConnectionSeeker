<?php
$this->breadcrumbs=array(
	'Iohistories'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Iohistory', 'url'=>array('index')),
	array('label'=>'Create Iohistory', 'url'=>array('create')),
	array('label'=>'Update Iohistory', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Iohistory', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Iohistory', 'url'=>array('index')),
);
?>

<h1>View Iohistory #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'task_id',
		'iostatus',
		'timeline',
		'role',
		'created',
		'created_by',
	),
)); ?>
