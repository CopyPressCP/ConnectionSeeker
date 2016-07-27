<?php
$this->breadcrumbs=array(
	'Backlinks'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Backlink', 'url'=>array('index')),
	array('label'=>'Create Backlink', 'url'=>array('create')),
	array('label'=>'Update Backlink', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Backlink', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Backlink', 'url'=>array('index')),
);
?>

<h1>View Backlink #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'competitor_id',
		'domain_id',
		'fresh_called',
		'historic_called',
		'url',
		'domain',
		'googlepr',
		'acrank',
		'anchortext',
		'date',
		'flagredirect',
		'flagframe',
		'flagnofollow',
		'flagimages',
		'flagdeleted',
		'flagalttext',
		'flagmention',
		'targeturl',
	),
)); ?>
