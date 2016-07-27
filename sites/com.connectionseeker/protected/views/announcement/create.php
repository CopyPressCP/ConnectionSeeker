<?php
$this->breadcrumbs=array(
	'Announcements'=>array('index'),
	'Create',
);

/*
$this->menu=array(
	array('label'=>'List Announcement', 'url'=>array('index')),
	array('label'=>'Manage Announcement', 'url'=>array('admin')),
);
*/
?>

<h1>Create Announcement</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>