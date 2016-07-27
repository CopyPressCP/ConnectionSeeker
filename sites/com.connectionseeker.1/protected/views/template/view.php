<?php
$this->breadcrumbs=array(
	'Templates'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'Create Mailer', 'url'=>array('/mailer/create')),
	array('label'=>'Manage Mailer', 'url'=>array('/mailer/index')),
	array('label'=>'Create Template', 'url'=>array('create')),
	array('label'=>'Update Template', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Template', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Template', 'url'=>array('index')),
);
?>

<h1>View Template #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'subject',
		array('name' => 'content','value' => nl2br($model->content), 'type' => 'html'),
		array('name' => 'notes','value' => nl2br($model->notes), 'type' => 'html'),
		'created',
		'created_by',
		'modified',
		'modified_by',
	),
)); ?>
