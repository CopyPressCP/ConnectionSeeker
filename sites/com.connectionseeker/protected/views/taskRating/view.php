<?php
$this->breadcrumbs=array(
	'Task Ratings'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List TaskRating', 'url'=>array('index')),
	array('label'=>'Create TaskRating', 'url'=>array('create')),
	array('label'=>'Update TaskRating', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete TaskRating', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage TaskRating', 'url'=>array('index')),
);
?>

<h1>View TaskRating #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'task_id',
		'rating',
		'notes',
		'created',
		'created_by',
	),
)); ?>
