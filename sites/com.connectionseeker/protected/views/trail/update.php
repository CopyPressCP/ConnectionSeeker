<?php
$this->breadcrumbs=array(
	'Trails'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Trail', 'url'=>array('index')),
	array('label'=>'Create Trail', 'url'=>array('create')),
	array('label'=>'View Trail', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Trail', 'url'=>array('index')),
);
?>

<h1>Update Trail <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>