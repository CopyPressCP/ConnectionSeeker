<?php
$this->breadcrumbs=array(
	'Carts'=>array('index'),
	'Create',
);

/*
$this->menu=array(
	array('label'=>'List Cart', 'url'=>array('index')),
	array('label'=>'Manage Cart', 'url'=>array('admin')),
);
*/
?>

<h1>Create Cart</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>