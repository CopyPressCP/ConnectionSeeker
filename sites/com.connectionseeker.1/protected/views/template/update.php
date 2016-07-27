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

<h1>Update Template <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>