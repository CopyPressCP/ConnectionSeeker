<?php
$this->breadcrumbs=array(
	'Onlines'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Online', 'url'=>array('index')),
	array('label'=>'Create Online', 'url'=>array('create')),
	array('label'=>'Update Online', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Online', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Online', 'url'=>array('index')),
);
?>

<h1>View Online #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'user_id',
		'date_tracked',
		'total_online',
		'login_time',
		'session_online',
		'last_operation_time',
	),
)); ?>
