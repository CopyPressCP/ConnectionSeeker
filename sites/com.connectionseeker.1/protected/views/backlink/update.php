<?php
$this->breadcrumbs=array(
	'Backlinks'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Backlink', 'url'=>array('index')),
	array('label'=>'Create Backlink', 'url'=>array('create')),
	array('label'=>'View Backlink', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Backlink', 'url'=>array('index')),
);
?>

<h1>Update Backlink <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>