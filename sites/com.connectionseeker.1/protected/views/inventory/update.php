<?php
$this->breadcrumbs=array(
	'Inventories'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Campaigns', 'url'=>array('campaign/index')),
	array('label'=>'Create Campaign', 'url'=>array('campaign/create')),
	array('label'=>'Inventories', 'url'=>array('inventory/index')),
	array('label'=>'Create Inventory', 'url'=>array('inventory/create')),
	array('label'=>'View Inventory', 'url'=>array('inventory/view', 'id'=>$model->id)),
	array('label'=>'Link Tasks', 'url'=>array('task/index')),
	array('label'=>'Upload', 'url'=>array('inventory/upload')),
);
?>

<h1>Update Inventory <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>