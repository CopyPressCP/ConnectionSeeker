<?php
$this->breadcrumbs=array(
	'Client Domains'=>array('index'),
	$model->domain=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Create Client', 'url'=>array('client/create')),
	array('label'=>'Manage Client', 'url'=>array('client/index')),
	array('label'=>'Create Domain', 'url'=>array('create')),
	array('label'=>'View Domain', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Domain', 'url'=>array('index')),
	array('label'=>'Create Keyword', 'url'=>array('clientDomainKeyword/create')),
	array('label'=>'Manage Keyword', 'url'=>array('clientDomainKeyword/index')),
	array('label'=>'Discovery', 'url'=>array('discovery/index')),
);
?>

<h1>Update Client Domain #<?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model, 'kymodel'=>$kymodel, 'cptmodel'=>$cptmodel)); ?>