<?php
$this->breadcrumbs=array(
	'Inventories'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'Campaigns', 'url'=>array('campaign/index')),
	array('label'=>'Create Campaign', 'url'=>array('campaign/create')),
	array('label'=>'Inventories', 'url'=>array('inventory/index')),
	array('label'=>'Create Inventory', 'url'=>array('inventory/create')),
	array('label'=>'Update Inventory', 'url'=>array('inventory/update', 'id'=>$model->id)),
	array('label'=>'Delete Inventory', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
    array('label'=>'Link Tasks', 'url'=>array('task/index')),
	array('label'=>'Upload', 'url'=>array('inventory/upload')),
);
?>

<h1>View Inventory #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'domain',
		'domain_id',
		'category',
		'category_str',
		'channel_id',
		'ext_backend_acct',
		'link_on_homepage',
		'notes',
		'status',
		'created',
		'created_by',
		'modified',
		'modified_by',
	),
)); ?>
