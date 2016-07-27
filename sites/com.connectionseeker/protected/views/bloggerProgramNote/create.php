<?php
$this->breadcrumbs=array(
	'Blogger Program Notes'=>array('index'),
	'Create',
);

/*
$this->menu=array(
	array('label'=>'List BloggerProgramNote', 'url'=>array('index')),
	array('label'=>'Manage BloggerProgramNote', 'url'=>array('admin')),
);
*/
?>

<h1>Create BloggerProgramNote</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>