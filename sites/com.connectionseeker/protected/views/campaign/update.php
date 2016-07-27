<?php
$this->breadcrumbs=array(
	'Campaigns'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Campaigns', 'url'=>array('campaign/index')),
	array('label'=>'Create Campaign', 'url'=>array('campaign/create')),
	array('label'=>'View Campaign', 'url'=>array('campaign/view', 'id'=>$model->id)),
	array('label'=>'Inventories', 'url'=>array('inventory/index')),
	array('label'=>'Create Inventory', 'url'=>array('inventory/create')),
	array('label'=>'Link Tasks', 'url'=>array('task/index')),
);
?>

<h1>Update Campaign <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model, 'ctmodel'=>$ctmodel)); ?>