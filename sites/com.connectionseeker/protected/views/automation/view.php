<?php
$this->breadcrumbs=array(
	'Automations'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List Automation', 'url'=>array('index')),
	array('label'=>'Create Automation', 'url'=>array('create')),
	array('label'=>'Update Automation', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Automation', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Automation', 'url'=>array('index')),
);
?>

<h1>View Automation #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'category',
		'touched_status',
		'mailer',
		'template',
		'current_domain_id',
		'current_mailer_id',
		'current_template_id',
		'total_sent',
		'total',
		'frequency',
		'sortby',
		'created',
		'created_by',
	),
)); ?>
