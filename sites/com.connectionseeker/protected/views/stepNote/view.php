<?php
$this->breadcrumbs=array(
	'Step Notes'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List StepNote', 'url'=>array('index')),
	array('label'=>'Create StepNote', 'url'=>array('create')),
	array('label'=>'Update StepNote', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete StepNote', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage StepNote', 'url'=>array('index')),
);
?>

<h1>View StepNote #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'task_id',
		'notes',
		'type',
		'created',
		'created_by',
	),
)); ?>
