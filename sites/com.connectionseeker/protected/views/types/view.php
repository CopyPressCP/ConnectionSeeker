<?php
$this->breadcrumbs=array(
	'Types'=>array('index'),
	$model->typename,
);

$this->menu=array(
	array('label'=>'Permission', 'url'=>array('rights/assignment/view')),
	array('label'=>'Create Type', 'url'=>array('create')),
	array('label'=>'Update Type', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Type', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Type', 'url'=>array('index')),
	array('label'=>'Preferences', 'url'=>array('types/preference')),
	array('label'=>'Add Blacklist', 'url'=>array('blacklist/create')),
	array('label'=>'Manage Blacklist', 'url'=>array('blacklist/index')),
);
?>

<h1>View Types #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'type',
		'refid',
		'typename',
		'status',
		'outils',
	),
)); ?>
