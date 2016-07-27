<?php
$this->breadcrumbs=array(
	'Discovery Backlinks'=>array('index'),
	'Create',
);

/*
$this->menu=array(
	array('label'=>'List DiscoveryBacklink', 'url'=>array('index')),
	array('label'=>'Manage DiscoveryBacklink', 'url'=>array('admin')),
);
*/
?>

<h1>Create DiscoveryBacklink</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>