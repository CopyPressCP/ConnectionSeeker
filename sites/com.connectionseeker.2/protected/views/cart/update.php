<?php
$this->breadcrumbs=array(
	'Carts'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Cart', 'url'=>array('index')),
	array('label'=>'Create Cart', 'url'=>array('create')),
	array('label'=>'View Cart', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Cart', 'url'=>array('index')),
);
?>

<h1>Update Cart <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>