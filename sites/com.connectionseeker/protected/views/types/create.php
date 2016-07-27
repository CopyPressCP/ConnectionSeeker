<?php
$this->breadcrumbs=array(
	'Types'=>array('index'),
	'Create',
);

/*
$this->menu=array(
	array('label'=>'List Types', 'url'=>array('index')),
	array('label'=>'Manage Types', 'url'=>array('admin')),
);
*/
?>

<h1>Create Types</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>