<?php
$this->breadcrumbs=array(
	'Backlinks'=>array('index'),
	'Create',
);

/*
$this->menu=array(
	array('label'=>'List Backlink', 'url'=>array('index')),
	array('label'=>'Manage Backlink', 'url'=>array('admin')),
);
*/
?>

<h1>Create Backlink</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>