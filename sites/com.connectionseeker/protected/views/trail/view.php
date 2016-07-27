<?php
$this->breadcrumbs=array(
	'Trails'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Trail', 'url'=>array('index')),
	array('label'=>'Create Trail', 'url'=>array('create')),
	array('label'=>'Update Trail', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Trail', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Trail', 'url'=>array('index')),
);
?>

<h1>View Trail #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'old_value',
		'new_value',
		'description',
		'action',
		'model',
		'field',
		'user_id',
		'model_id',
		'created',
	),
)); ?>
