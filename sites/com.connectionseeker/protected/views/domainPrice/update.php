<?php
$this->breadcrumbs=array(
	'Domain Prices'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List DomainPrice', 'url'=>array('index')),
	array('label'=>'Create DomainPrice', 'url'=>array('create')),
	array('label'=>'View DomainPrice', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage DomainPrice', 'url'=>array('index')),
);
?>

<h1>Update DomainPrice <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>