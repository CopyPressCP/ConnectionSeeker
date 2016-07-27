<?php
$this->breadcrumbs=array(
	'Automation'=>array('index'),
	'Update',
);
?>

<h2>Update Automation Rule#<?php echo $model->id; ?></h2>

<?php echo $this->renderPartial('/automation/_form', array('model'=>$model)); ?>