<?php
$this->breadcrumbs=array(
	'Discoveries'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Discovery', 'url'=>array('index')),
	array('label'=>'Create Discovery', 'url'=>array('create')),
	array('label'=>'View Discovery', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Discovery', 'url'=>array('discovery/index')),
);
?>

<h1>Update Discovery <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>