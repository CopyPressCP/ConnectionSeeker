<?php
$this->breadcrumbs=array(
	'Blacklists'=>array('index'),
	'Create',
);

/*
$this->menu=array(
	array('label'=>'List Blacklist', 'url'=>array('index')),
	array('label'=>'Manage Blacklist', 'url'=>array('admin')),
);
*/
?>

<h1>Create Blacklist</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>