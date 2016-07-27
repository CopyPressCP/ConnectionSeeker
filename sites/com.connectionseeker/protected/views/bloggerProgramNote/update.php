<?php
$this->breadcrumbs=array(
	'Blogger Program Notes'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List BloggerProgramNote', 'url'=>array('index')),
	array('label'=>'Create BloggerProgramNote', 'url'=>array('create')),
	array('label'=>'View BloggerProgramNote', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage BloggerProgramNote', 'url'=>array('index')),
);
?>

<h1>Update BloggerProgramNote <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>