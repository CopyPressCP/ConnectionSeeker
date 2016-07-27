<?php
$this->breadcrumbs=array(
	'Blogger Program Prices'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List BloggerProgramPrice', 'url'=>array('index')),
	array('label'=>'Create BloggerProgramPrice', 'url'=>array('create')),
	array('label'=>'Update BloggerProgramPrice', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete BloggerProgramPrice', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage BloggerProgramPrice', 'url'=>array('index')),
);
?>

<h1>View BloggerProgramPrice #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'blogger_program_id',
		'domain_id',
		'domain',
		'price',
		'memo',
		'created',
		'created_by',
	),
)); ?>
