<?php
$this->breadcrumbs=array(
	'Mailers'=>array('index'),
	$model->id,
);

$this->menu=array(
    array('label'=>'Create Mailer', 'url'=>array('create')),
	array('label'=>'Update Mailer', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Mailer', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Mailer', 'url'=>array('index')),
	array('label'=>'Create Template', 'url'=>array('/template/create')),
	array('label'=>'Manage Template', 'url'=>array('/template/index')),
);
?>

<h1>View Mailer #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'user_alias',
		'smtp_host',
		'smtp_port',
		'pop3_host',
		'pop3_port',
		'password',
		'username',
		'display_name',
		'email_from',
		'reply_to',
		'status',
	),
)); ?>
