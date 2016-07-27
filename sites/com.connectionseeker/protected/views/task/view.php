<?php
$this->breadcrumbs=array(
	'Tasks'=>array('index'),
	$model->title,
);

$this->menu=array(
	array('label'=>'Campaigns', 'url'=>array('campaign/index')),
	array('label'=>'Create Campaign', 'url'=>array('campaign/create')),
	array('label'=>'Inventories', 'url'=>array('inventory/index')),
	array('label'=>'Create Inventory', 'url'=>array('inventory/create')),
	array('label'=>'Link Tasks', 'url'=>array('task/index')),
	array('label'=>'Upload', 'url'=>array('inventory/upload')),

	//array('label'=>'Update Task', 'url'=>array('update', 'id'=>$model->id)),
	//array('label'=>'Delete Task', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),

);
?>

<h1>View Task #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'inventory_id',
		'campaign_id',
		'domain',
		'domain_id',
		'anchortext',
		'targeturl',
		'sourceurl',
		'sourcedomain',
		'title',
		'tasktype',
		'taskstatus',
		'assignee',
		'optional_keywords',
		'mapping_id',
		'desired_domain_id',
		'desired_domain',
		'notes',
		'duedate',
		'content_article_id',
		'content_campaign_id',
		'content_category_id',
		'send2cpdate',
		'checkouted',
		'created',
		'created_by',
		'modified',
		'modified_by',
	),
)); ?>
