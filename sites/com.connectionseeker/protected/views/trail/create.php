<?php
$this->breadcrumbs=array(
	'Trails'=>array('index'),
	'Create',
);

/*
$this->menu=array(
	array('label'=>'List Trail', 'url'=>array('index')),
	array('label'=>'Manage Trail', 'url'=>array('admin')),
);
*/
?>

<h1>Create Trail</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>