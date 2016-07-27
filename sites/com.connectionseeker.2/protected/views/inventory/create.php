<?php
$this->breadcrumbs=array(
	'Inventories'=>array('index'),
	'Create',
);

/*
$this->menu=array(
	array('label'=>'List Inventory', 'url'=>array('index')),
	array('label'=>'Manage Inventory', 'url'=>array('admin')),
);
*/
?>

<h1>Create Inventory</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>