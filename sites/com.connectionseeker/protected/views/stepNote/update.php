<?php
$this->breadcrumbs=array(
	'Step Notes'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List StepNote', 'url'=>array('index')),
	array('label'=>'Create StepNote', 'url'=>array('create')),
	array('label'=>'View StepNote', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage StepNote', 'url'=>array('index')),
);
?>

<h1>Update StepNote <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>