<?php
$this->breadcrumbs=array(
	'Emails'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('email-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

$fttemplates = CHtml::listData(Template::model()->findAll(),'id','name');
$ftfromes = CHtml::listData(Mailer::model()->findAll(),'id','user_alias');
$ftusers = CHtml::listData(User::model()->findAll(),'id','username');
?>

<h1>Manage Emails</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<!-- search-form -->
<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
	'fttemplates'=>$fttemplates,
	'ftfromes'=>$ftfromes,
	'ftusers'=>$ftusers,
)); ?>
</div><!-- search-form -->

<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'email-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
        array(
            'name' => 'domain_id',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->rdomain->domain), array("domain/view", "id" =>$data->domain_id))',
        ),
        array(
            'name' => 'template_id',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->rtemplate->name), array("template/view", "id" =>$data->template_id))',
            'filter' => $fttemplates,
        ),
        array(
            'name' => 'from',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->rmailer->user_alias), array("mailer/view", "id" =>$data->from))',
            'filter' => $ftfromes,
        ),
		'to',
		'send_time',
		'created',
        array(
            'name' => 'created_by',
            'type' => 'raw',
            'value' => 'CHtml::encode($data->rcreatedby->username)',
            'filter' => $ftusers,
        ),

		/*
		'domain_id',
		'template_id',
		'from',
		'cc',
		'subject',
		'content',
		'status',
		'send_time',
		'created',
		'created_by',
		'modified',
		'modified_by',
		*/
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
