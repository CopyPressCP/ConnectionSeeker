<?php
$this->breadcrumbs=array(
	'Ioblacklists'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Ioblacklist', 'url'=>array('index')),
	array('label'=>'Create Ioblacklist', 'url'=>array('create')),
	array('label'=>'Update Ioblacklist', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Ioblacklist', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Ioblacklist', 'url'=>array('index')),
);
?>

<h1>View Ioblacklist #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'domain_id',
		'domain',
		'isallclient',
		'clients',
		'clients_str',
		'notes',
		'isblacklist',
		'created',
		'created_by',
		'modified',
		'modified_by',
	),
)); ?>
