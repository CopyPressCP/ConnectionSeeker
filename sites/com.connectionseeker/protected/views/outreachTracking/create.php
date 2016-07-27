<?php
$this->breadcrumbs=array(
	'Outreach Trackings'=>array('index'),
	'Create',
);

/*
$this->menu=array(
	array('label'=>'List OutreachTracking', 'url'=>array('index')),
	array('label'=>'Manage OutreachTracking', 'url'=>array('admin')),
);
*/
?>

<h1>Create OutreachTracking</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>