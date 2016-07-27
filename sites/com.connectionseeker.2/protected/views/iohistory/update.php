<?php
$this->breadcrumbs=array(
	'Iohistories'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Iohistory', 'url'=>array('index')),
	array('label'=>'Create Iohistory', 'url'=>array('create')),
	array('label'=>'View Iohistory', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Iohistory', 'url'=>array('index')),
);
?>

<h1>Update Iohistory <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>