<?php
$this->breadcrumbs=array(
	'Templates'=>array('index'),
	'Create',
);
?>

<div id="innermenu">
    <?php $this->renderPartial('/template/_menu'); ?>
</div>

<h2>Create Template</h2>

<?php echo $this->renderPartial('/template/_form', array('model'=>$model)); ?>