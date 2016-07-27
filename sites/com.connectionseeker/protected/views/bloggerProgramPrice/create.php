<?php
$this->breadcrumbs=array(
	'Blogger Program Prices'=>array('index'),
	'Create',
);

/*
$this->menu=array(
	array('label'=>'List BloggerProgramPrice', 'url'=>array('index')),
	array('label'=>'Manage BloggerProgramPrice', 'url'=>array('admin')),
);
*/
?>

<h1>Create BloggerProgramPrice</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>