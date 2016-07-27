<?php
$this->breadcrumbs=array(
	'Blogger Program Prices'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List BloggerProgramPrice', 'url'=>array('index')),
	array('label'=>'Create BloggerProgramPrice', 'url'=>array('create')),
	array('label'=>'View BloggerProgramPrice', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage BloggerProgramPrice', 'url'=>array('index')),
);
?>

<h1>Update BloggerProgramPrice <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>