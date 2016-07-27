<?php
$this->breadcrumbs=array(
	'Onlines'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Online', 'url'=>array('index')),
	array('label'=>'Create Online', 'url'=>array('create')),
	array('label'=>'View Online', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Online', 'url'=>array('index')),
);
?>

<h1>Update Online <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>