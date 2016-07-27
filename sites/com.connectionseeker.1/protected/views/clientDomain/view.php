<?php
$this->breadcrumbs=array(
	'Client Domains'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List ClientDomain', 'url'=>array('index')),
	array('label'=>'Create ClientDomain', 'url'=>array('create')),
	array('label'=>'Update ClientDomain', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete ClientDomain', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage ClientDomain', 'url'=>array('index')),
	array('label'=>'Discovery', 'url'=>array('discovery/index')),
);
?>

<h1>View ClientDomain #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'domain',
		'client_id',
		'created',
		'created_by',
		'modified',
		'modified_by',
	),
)); ?>
