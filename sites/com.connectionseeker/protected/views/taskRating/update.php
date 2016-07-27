<?php
$this->breadcrumbs=array(
	'Task Ratings'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List TaskRating', 'url'=>array('index')),
	array('label'=>'Create TaskRating', 'url'=>array('create')),
	array('label'=>'View TaskRating', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage TaskRating', 'url'=>array('index')),
);
?>

<h1>Update TaskRating <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>