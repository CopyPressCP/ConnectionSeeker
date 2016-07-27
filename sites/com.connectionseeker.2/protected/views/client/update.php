<?php
$this->breadcrumbs=array(
	'Clients'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Create Client', 'url'=>array('create')),
	array('label'=>'View Client', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Delete Client', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Client', 'url'=>array('index')),
	array('label'=>'Manage Domain', 'url'=>array('clientDomain/index')),
	array('label'=>'Create Keyword', 'url'=>array('clientDomainKeyword/create')),
	array('label'=>'Manage Keyword', 'url'=>array('clientDomainKeyword/index')),
	array('label'=>'Discovery', 'url'=>array('discovery/index')),
);
?>

<h1>Update Client #<?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model, 'domodel'=>$domodel)); ?>