<?php
$this->breadcrumbs=array(
	'Task Notes'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List TaskNote', 'url'=>array('index')),
	array('label'=>'Create TaskNote', 'url'=>array('create')),
	array('label'=>'View TaskNote', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage TaskNote', 'url'=>array('index')),
);
?>

<h1>Update TaskNote <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>