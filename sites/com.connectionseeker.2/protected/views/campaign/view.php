<?php
$this->breadcrumbs=array(
	'Campaigns'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'Campaigns', 'url'=>array('campaign/index')),
	array('label'=>'Create Campaign', 'url'=>array('campaign/create')),
	array('label'=>'Update Campaign', 'url'=>array('campaign/update', 'id'=>$model->id)),
	array('label'=>'Delete Campaign', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Inventories', 'url'=>array('inventory/index')),
	array('label'=>'Create Inventory', 'url'=>array('inventory/create')),
    array('label'=>'Link Tasks', 'url'=>array('task/index')),
);
?>

<h1>View Campaign #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'domain',
		'client_id',
		'domain_id',
		'category',
		'category_str',
		'notes',
		'status',
		'created',
		'created_by',
		'modified',
		'modified_by',
	),
)); ?>
