<?php
$this->breadcrumbs=array(
	'Emails'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Outreach', 'url'=>array('domain/index&touched=true')),
	array('label'=>'Create Domain', 'url'=>array('domain/create')),
	array('label'=>'Manage Domain', 'url'=>array('domain/index')),
	array('label'=>'View Email', 'url'=>array('email/view', 'id'=>$model->id)),
	array('label'=>'Manage Email', 'url'=>array('email/index')),
);
?>

<h1>Update Email <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>