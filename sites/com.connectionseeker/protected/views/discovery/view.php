<?php
$this->breadcrumbs=array(
	'Discoveries'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Discovery', 'url'=>array('index')),
	array('label'=>'Create Discovery', 'url'=>array('create')),
	array('label'=>'Update Discovery', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Discovery', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Discovery', 'url'=>array('discovery/index')),
);
?>

<h1>View Discovery #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'competitor_id',
		'domain_id',
		'fresh_called',
		'historic_called',
	),
)); ?>
