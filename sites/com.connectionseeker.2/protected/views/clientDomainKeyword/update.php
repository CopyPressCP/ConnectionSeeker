<?php
$this->breadcrumbs=array(
	'Domain Keywords'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Create Client', 'url'=>array('client/create')),
	array('label'=>'Manage Client', 'url'=>array('client/index')),
    array('label'=>'Create Keyword', 'url'=>array('create')),
	array('label'=>'View Keyword', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Keyword', 'url'=>array('index')),
	array('label'=>'Discovery', 'url'=>array('discovery/index')),
);
?>

<h1>Update ClientDomainKeyword <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>