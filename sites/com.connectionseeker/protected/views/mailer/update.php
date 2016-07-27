<?php
$this->breadcrumbs=array(
	'Mailers'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Create Mailer', 'url'=>array('create')),
	array('label'=>'View Mailer', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Mailer', 'url'=>array('index')),
	array('label'=>'Create Template', 'url'=>array('/template/create')),
	array('label'=>'Manage Template', 'url'=>array('/template/index')),
);
?>

<div id="innermenu">
    <?php $this->renderPartial('/mailer/_menu'); ?>
</div>

<h2>Update Mailer <?php echo $model->id; ?></h2>

<?php echo $this->renderPartial('/mailer/_form', array('model'=>$model)); ?>