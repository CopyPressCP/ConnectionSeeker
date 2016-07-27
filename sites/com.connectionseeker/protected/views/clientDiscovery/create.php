<?php
$this->breadcrumbs=array(
	'Client Discoveries'=>array('index'),
	'Create',
);

/*
$this->menu=array(
	array('label'=>'List ClientDiscovery', 'url'=>array('index')),
	array('label'=>'Manage ClientDiscovery', 'url'=>array('admin')),
);
*/
?>

<div id="innermenu">
    <?php $this->renderPartial('/clientDiscovery/_menu'); ?>
</div>
<h2>Create Email Task</h2>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>