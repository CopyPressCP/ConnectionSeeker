<?php
$this->breadcrumbs=array(
	'Blogger Program Notes'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List BloggerProgramNote', 'url'=>array('index')),
	array('label'=>'Create BloggerProgramNote', 'url'=>array('create')),
	array('label'=>'Update BloggerProgramNote', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete BloggerProgramNote', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage BloggerProgramNote', 'url'=>array('index')),
);
?>

<h1>View BloggerProgramNote #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'blogger_program_id',
		'domain_id',
		'notes',
		'isprivate',
		'created',
		'created_by',
	),
)); ?>
