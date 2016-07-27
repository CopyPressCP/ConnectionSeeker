<?php
$this->breadcrumbs=array(
	'Client Discoveries'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Clone',
);

$this->menu=array(
	array('label'=>'List ClientDiscovery', 'url'=>array('index')),
	array('label'=>'Create ClientDiscovery', 'url'=>array('create')),
	array('label'=>'View ClientDiscovery', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage ClientDiscovery', 'url'=>array('index')),
);
?>
<div id="innermenu">
    <?php $this->renderPartial('/clientDiscovery/_menu'); ?>
</div>

<h2>Clone Email Task From #<?php echo $model->id; ?></h2>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>