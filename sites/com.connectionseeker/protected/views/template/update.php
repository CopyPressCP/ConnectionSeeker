<?php
$this->breadcrumbs=array(
	'Templates'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Create Mailer', 'url'=>array('/mailer/create')),
	array('label'=>'Manage Mailer', 'url'=>array('/mailer/index')),
	array('label'=>'Create Template', 'url'=>array('create')),
	array('label'=>'View Template', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Template', 'url'=>array('index')),
);
?>

<div id="innermenu">
    <?php $this->renderPartial('/template/_menu'); ?>
</div>

<h2>Update Template <?php echo $model->id; ?></h2>

<?php echo $this->renderPartial('/template/_form', array('model'=>$model)); ?>