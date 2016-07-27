<?php
$this->breadcrumbs=array(
	'Mailers'=>array('index'),
	'Create',
);

/*
$this->menu=array(
	array('label'=>'List Mailer', 'url'=>array('index')),
	array('label'=>'Manage Mailer', 'url'=>array('admin')),
);
*/
?>

<h1>Create Mailer</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>