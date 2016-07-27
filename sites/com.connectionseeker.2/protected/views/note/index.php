<?php
$this->breadcrumbs=array(
	'Notes'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('note-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Notes</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<!-- search-form -->
<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none;padding:0px;margin:0px;">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'note-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
        array(
            'name' => 'domain_id',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->rdomain->domain),"http://www.".$data->rdomain->domain, array("target"=>"_blank"))',
        ),
		'notes',
		'created',
        array(
            'name' => 'created_by',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->rcreatedby->username), array("user/view", "id" =>$data->created_by))',
            'filter' => CHtml::listData(User::model()->findAll(),'id','username'),
        ),
		//'created_by',
		//'domain_id',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
