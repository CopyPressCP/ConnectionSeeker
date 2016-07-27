<?php
$this->breadcrumbs=array(
	'Ioblacklists'=>array('index'),
	'Create',
);

/*
$this->menu=array(
	array('label'=>'List Ioblacklist', 'url'=>array('index')),
	array('label'=>'Manage Ioblacklist', 'url'=>array('admin')),
);
*/
?>


<div id="innermenu">
    <?php $this->renderPartial('/ioblacklist/_menu'); ?>
</div>

<h2>Blacklist a Site</h2>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>