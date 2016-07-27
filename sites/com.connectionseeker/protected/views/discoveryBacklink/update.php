<?php
$this->breadcrumbs=array(
	'Discovery Backlinks'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List DiscoveryBacklink', 'url'=>array('index')),
	array('label'=>'Create DiscoveryBacklink', 'url'=>array('create')),
	array('label'=>'View DiscoveryBacklink', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage DiscoveryBacklink', 'url'=>array('index')),
);
?>

<h1>Update DiscoveryBacklink <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>