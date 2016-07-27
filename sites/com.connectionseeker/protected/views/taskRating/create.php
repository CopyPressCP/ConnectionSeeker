<?php
$this->breadcrumbs=array(
	'Task Ratings'=>array('index'),
	'Create',
);

/*
$this->menu=array(
	array('label'=>'List TaskRating', 'url'=>array('index')),
	array('label'=>'Manage TaskRating', 'url'=>array('admin')),
);
*/
?>

<h1>Create TaskRating</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>