<?php
$this->breadcrumbs=array(
	'Blogger Programs'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List BloggerProgram', 'url'=>array('index')),
	array('label'=>'Create BloggerProgram', 'url'=>array('create')),
	array('label'=>'Update BloggerProgram', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete BloggerProgram', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage BloggerProgram', 'url'=>array('index')),
);
?>

<div id="innermenu">
    <?php $this->renderPartial('/bloggerProgram/menu'); ?>
</div>

<h1>View Blogger Program #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'domain',
		'domain_id',
		'first_name',
		'last_name',
		'mozauthority',
		'category',
		'category_str',
		'cms_username',
		'syndication',
		'contact_email',
		'per_word_rate',
		'activeprogram',
		'activeprogram_str',
		'status',
		'isdelete',
	),
)); ?>
