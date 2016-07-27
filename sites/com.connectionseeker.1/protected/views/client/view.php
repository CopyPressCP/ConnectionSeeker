<?php
$this->breadcrumbs=array(
	'Clients'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'Create Client', 'url'=>array('create')),
	array('label'=>'Update Client', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Client', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Client', 'url'=>array('index')),
	array('label'=>'Manage Domain', 'url'=>array('clientDomain/index')),
	array('label'=>'Create Keyword', 'url'=>array('clientDomainKeyword/create')),
	array('label'=>'Manage Keyword', 'url'=>array('clientDomainKeyword/index')),
	array('label'=>'Discovery', 'url'=>array('discovery/index')),
);
?>

<h1>View Client #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'company',
		'user_id',
		'name',
		'contact_name',
		'email',
		'telephone',
		'cellphone',
		'note',
		'assignee',
		'status',
		'created',
		'created_by',
		'modified',
		'modified_by',
		'last_visit_time',
		'last_visit_ip',
	),
)); ?>
