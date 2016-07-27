<?php
$this->breadcrumbs=array(
	'Domain Prices'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List DomainPrice', 'url'=>array('index')),
	array('label'=>'Create DomainPrice', 'url'=>array('create')),
	array('label'=>'Update DomainPrice', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete DomainPrice', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage DomainPrice', 'url'=>array('index')),
);
?>

<h1>View DomainPrice #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'domain_id',
		'domain',
		'price',
		'memo',
		'created',
		'created_by',
	),
)); ?>
