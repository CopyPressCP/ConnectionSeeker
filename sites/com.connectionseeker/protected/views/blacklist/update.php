<?php
$this->breadcrumbs=array(
	'Blacklists'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Permission', 'url'=>array('/rights/assignment/view')),
	array('label'=>'Create Type', 'url'=>array('/types/create')),
	array('label'=>'Manage Type', 'url'=>array('/types/index')),
	array('label'=>'Add Blacklist', 'url'=>array('/blacklist/create')),
	array('label'=>'View Blacklist', 'url'=>array('/blacklist/view', 'id'=>$model->id)),
	array('label'=>'Manage Blacklist', 'url'=>array('/blacklist/index')),
);
?>

<h1>Update Blacklist <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>