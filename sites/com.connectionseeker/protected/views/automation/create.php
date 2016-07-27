<?php
$this->breadcrumbs=array(
	'Automation'=>array('index'),
	'Create',
);
?>

<h2>Create Automation Rule</h2>

<?php echo $this->renderPartial('/automation/_form', array('model'=>$model)); ?>