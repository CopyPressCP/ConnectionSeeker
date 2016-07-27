<?php
$this->breadcrumbs=array(
	'Step Notes'=>array('index'),
	'Create',
);

/*
$this->menu=array(
	array('label'=>'List StepNote', 'url'=>array('index')),
	array('label'=>'Manage StepNote', 'url'=>array('admin')),
);
*/
?>

<h1>Create StepNote</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>