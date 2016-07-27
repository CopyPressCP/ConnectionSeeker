<?php
$this->breadcrumbs=array(
	'Types'=>array('index'),
	$model->typename=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Permission', 'url'=>array('rights/assignment/view')),
	array('label'=>'Create Type', 'url'=>array('create')),
	array('label'=>'View Type', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Type', 'url'=>array('index')),
	array('label'=>'Add Blacklist', 'url'=>array('blacklist/create')),
	array('label'=>'Manage Blacklist', 'url'=>array('blacklist/index')),
);
?>

<h1>Update Types <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>