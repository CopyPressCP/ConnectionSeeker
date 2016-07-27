<?php
$this->breadcrumbs=array(
	'Blogger Programs'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Blogger Program', 'url'=>array('index')),
	array('label'=>'Create Blogger Program', 'url'=>array('create')),
	array('label'=>'View Blogger Program', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Blogger Program', 'url'=>array('index')),
);
?>

<div id="innermenu">
    <?php $this->renderPartial('/bloggerProgram/menu'); ?>
</div>

<h1>Update Blogger Program Domain <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>