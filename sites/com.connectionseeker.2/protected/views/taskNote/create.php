<?php
$this->breadcrumbs=array(
	'Task Notes'=>array('index'),
	'Create',
);

/*
$this->menu=array(
	array('label'=>'List TaskNote', 'url'=>array('index')),
	array('label'=>'Manage TaskNote', 'url'=>array('admin')),
);
*/
?>

<h1>Create TaskNote</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>