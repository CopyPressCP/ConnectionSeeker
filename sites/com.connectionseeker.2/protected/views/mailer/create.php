<?php
$this->breadcrumbs=array(
	'Mailers'=>array('index'),
	'Create',
);

/*
$this->menu=array(
	array('label'=>'List Mailer', 'url'=>array('index')),
	array('label'=>'Manage Mailer', 'url'=>array('admin')),
);
*/
?>

<div id="innermenu">
    <?php $this->renderPartial('/mailer/_menu'); ?>
</div>

<h2>Create Mailer</h2>

<?php echo $this->renderPartial('/mailer/_form', array('model'=>$model)); ?>