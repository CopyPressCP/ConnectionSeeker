<?php
$this->breadcrumbs=array(
	'Client Domains'=>array('index'),
	'Create',
);

/*
$this->menu=array(
	array('label'=>'List ClientDomain', 'url'=>array('index')),
	array('label'=>'Manage ClientDomain', 'url'=>array('admin')),
);
*/
?>

<h1>Create Client Domain</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model, 'kymodel'=>$kymodel, 'cptmodel'=>$cptmodel)); ?>