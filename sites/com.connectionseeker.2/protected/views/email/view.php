<?php
$this->breadcrumbs=array(
	'Emails'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Email', 'url'=>array('index')),
	array('label'=>'Create Email', 'url'=>array('create')),
	array('label'=>'Update Email', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Email', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Email', 'url'=>array('index')),
);
?>

<h1>View Email #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'domain_id',
		'template_id',
		'from',
		'to',
		'cc',
		'subject',
        array(
            'name' => 'content',
            'type' => 'raw',
        ),
		//'content',
		'status',
		'send_time',
		'created',
		'created_by',
		'modified',
		'modified_by',
		'mid',
	),
)); ?>
