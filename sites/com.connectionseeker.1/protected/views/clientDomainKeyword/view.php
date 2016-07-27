<?php
$this->breadcrumbs=array(
	'Client Domain Keywords'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'Create Client', 'url'=>array('client/create')),
	array('label'=>'Manage Client', 'url'=>array('client/index')),
	array('label'=>'Create Domain', 'url'=>array('clientDomain/create')),
	array('label'=>'Manage Domain', 'url'=>array('clientDomain/index')),
	array('label'=>'Create Keyword', 'url'=>array('create')),
	array('label'=>'Update Keyword', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Keyword', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Keyword', 'url'=>array('index')),
	array('label'=>'Discovery', 'url'=>array('discovery/index')),
);
?>

<h1>View ClientDomainKeyword #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'keyword',
		'domain_id',
		'client_id',
		'created',
		'created_by',
		'modified',
		'modified_by',
	),
)); ?>
