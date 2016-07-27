<?php
$this->breadcrumbs=array(
	'Outreach Trackings'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List OutreachTracking', 'url'=>array('index')),
	array('label'=>'Create OutreachTracking', 'url'=>array('create')),
	array('label'=>'View OutreachTracking', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage OutreachTracking', 'url'=>array('index')),
);
?>

<h1>Update OutreachTracking <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>