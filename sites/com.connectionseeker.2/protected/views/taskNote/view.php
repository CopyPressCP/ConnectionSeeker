<?php
$this->breadcrumbs=array(
	'Task Notes'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List TaskNote', 'url'=>array('index')),
	array('label'=>'Create TaskNote', 'url'=>array('create')),
	array('label'=>'Update TaskNote', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete TaskNote', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage TaskNote', 'url'=>array('index')),
);
?>

<h1>View TaskNote #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'task_id',
		'notes',
		'created',
		'created_by',
	),
)); ?>
