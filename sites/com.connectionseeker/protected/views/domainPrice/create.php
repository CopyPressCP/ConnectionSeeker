<?php
$this->breadcrumbs=array(
	'Domain Prices'=>array('index'),
	'Create',
);

/*
$this->menu=array(
	array('label'=>'List DomainPrice', 'url'=>array('index')),
	array('label'=>'Manage DomainPrice', 'url'=>array('admin')),
);
*/
?>

<h1>Create DomainPrice</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>