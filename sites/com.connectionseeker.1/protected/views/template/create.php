<?php
$this->breadcrumbs=array(
	'Templates'=>array('index'),
	'Create',
);
?>

<h1>Create Template</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>