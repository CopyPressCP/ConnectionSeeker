<?php
$this->breadcrumbs=array(
	'Iohistories'=>array('index'),
	'Create',
);

/*
$this->menu=array(
	array('label'=>'List Iohistory', 'url'=>array('index')),
	array('label'=>'Manage Iohistory', 'url'=>array('admin')),
);
*/
?>

<h1>Create Iohistory</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>