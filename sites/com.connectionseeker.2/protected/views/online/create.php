<?php
$this->breadcrumbs=array(
	'Onlines'=>array('index'),
	'Create',
);

/*
$this->menu=array(
	array('label'=>'List Online', 'url'=>array('index')),
	array('label'=>'Manage Online', 'url'=>array('admin')),
);
*/
?>

<h1>Create Online</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>