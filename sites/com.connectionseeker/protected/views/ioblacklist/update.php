<?php
$this->breadcrumbs=array(
	'Ioblacklists'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Ioblacklist', 'url'=>array('index')),
	array('label'=>'Create Ioblacklist', 'url'=>array('create')),
	array('label'=>'View Ioblacklist', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Ioblacklist', 'url'=>array('index')),
);
?>

<div id="innermenu">
    <?php $this->renderPartial('/ioblacklist/_menu'); ?>
</div>

<h2>Blacklist Site #<?php echo $model->id; ?></h2>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>