<?php
$this->breadcrumbs=array(
	'Outreach Trackings'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List OutreachTracking', 'url'=>array('index')),
	array('label'=>'Create OutreachTracking', 'url'=>array('create')),
	array('label'=>'Update OutreachTracking', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete OutreachTracking', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage OutreachTracking', 'url'=>array('index')),
);
?>

<h1>View OutreachTracking #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'domain_id',
		'domain',
		'before_value',
		'after_value',
		'created',
		'created_by',
	),
)); ?>
