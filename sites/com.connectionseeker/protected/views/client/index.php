<?php
$this->breadcrumbs=array(
	'Clients'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('client-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Clients</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<!-- search-form -->
<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'client-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'company',
		'name',
		'contact_name',
		'email',
		'telephone',
		/*
		'cellphone',
		'use_historic_index',
		'note',
		'assignee',
		'status',
		'created',
		'created_by',
		'modified',
		'modified_by',
		'last_visit_time',
		'last_visit_ip',
		*/
		array(
			'class'=>'CButtonColumn',
            'template'=>'{discovery} {view} {update} {delete}',
            'buttons' => array(
                'discovery' => array(
                    'label' => 'Discovery',
                    'imageUrl'=>Yii::app()->theme->baseUrl.'/css/gridview/discovery.png',
                    'url' => 'Yii::app()->createUrl("discovery/index", array("client_id"=>$data->id))',                   
                    'options' => array(
                        'class'=>'discovery',
                    ),
                ),
            ),
            'htmlOptions'=>array('nowrap'=>'nowrap'),
		),
	),
)); ?>
