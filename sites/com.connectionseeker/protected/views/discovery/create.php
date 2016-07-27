<?php
$this->breadcrumbs=array(
	'Discoveries'=>array('index'),
	'Create',
);

/*
$this->menu=array(
	array('label'=>'List Discovery', 'url'=>array('index')),
	array('label'=>'Manage Discovery', 'url'=>array('admin')),
);
*/
?>

<h1>Create Discovery</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>