<?php
$this->breadcrumbs=array(
	'Client Domain Keywords'=>array('index'),
	'Create',
);

/*
$this->menu=array(
	array('label'=>'List Keyword', 'url'=>array('index')),
	array('label'=>'Manage Keyword', 'url'=>array('admin')),
);
*/
?>

<h1>Create Keyword</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>