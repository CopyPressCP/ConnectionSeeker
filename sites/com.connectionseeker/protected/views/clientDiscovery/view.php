<?php
$this->breadcrumbs=array(
	'Client Discoveries'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List ClientDiscovery', 'url'=>array('index')),
	array('label'=>'Create ClientDiscovery', 'url'=>array('create')),
	array('label'=>'Update ClientDiscovery', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete ClientDiscovery', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage ClientDiscovery', 'url'=>array('index')),
);
?>

<h1>View ClientDiscovery #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'client_id',
		'domain_id',
		'domain',
		'competitora_id',
		'competitora',
		'competitorb_id',
		'competitorb',
		'created',
		'created_by',
		'modified',
		'modified_by',
	),
)); ?>
