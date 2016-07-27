<?php
$this->breadcrumbs=array(
	'Domain Keywords'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('client-domain-keyword-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

$ftclients = CHtml::listData(Client::model()->actived()->findAll(),'id','company');
$ftusers = CHtml::listData(User::model()->findAll(),'id','username');
?>

<h1>Manage Client Domain Keywords</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<!-- search-form -->
<?php $this->renderPartial('_search',array(
	'model'=>$model,
	'ftclients'=>$ftclients,
	'ftusers'=>$ftusers,
)); ?>
<!-- search-form -->
<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'client-domain-keyword-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'keyword',
        array(
            'name' => 'client_id',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->rclient->company), array("client/view", "id" =>$data->client_id))',
            'filter' => $ftclients,
        ),
		'created',
        array(
            'name' => 'created_by',
            'type' => 'raw',
            'value' => 'CHtml::encode($data->rcreatedby->username)',
            'filter' => $ftusers,
        ),
		/*
		'domain_id',
		'client_id',
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
