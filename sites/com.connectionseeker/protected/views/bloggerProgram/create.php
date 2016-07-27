<?php
$this->breadcrumbs=array(
	'Blogger Programs'=>array('index'),
	'Create',
);

/*
$this->menu=array(
	array('label'=>'List BloggerProgram', 'url'=>array('index')),
	array('label'=>'Manage BloggerProgram', 'url'=>array('admin')),
);
*/
?>

<div id="innermenu">
    <?php $this->renderPartial('/bloggerProgram/menu'); ?>
</div>

<h1>Create Blogger Program Domain</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>