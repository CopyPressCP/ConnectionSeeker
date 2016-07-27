<?php
$this->breadcrumbs=array(
	'Client Domains'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('client-domain-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

$ftclients = CHtml::listData(Client::model()->actived()->findAll(),'id','company');
$ftusers = CHtml::listData(User::model()->findAll(),'id','username');
$ftstatus = array('0'=>'Inactvie', '1'=>'Actvie');
?>

<h1>Manage Client Domains</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<!-- search-form -->
<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
	'ftclients'=>$ftclients,
	'ftusers'=>$ftusers,
	'ftstatus'=>$ftstatus,
)); ?>
<!-- search-form -->
</div><!-- search-form -->

<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'client-domain-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'domain',
		//'client_id',
        array(
            'name' => 'client_id',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->rclient->company), array("client/view", "id" =>$data->client_id))',
            'filter' => $ftclients,
        ),

        'created',
		//'created_by',
        array(
            'name' => 'created_by',
            'type' => 'raw',
            'value' => 'CHtml::encode($data->createdby->username)',
            'filter' => $ftusers,
        ),

        array(
            'name' => 'status',
            'type' => 'raw',
            'value' => '($data->status)? "Yes":"No"',
            'filter' => $ftstatus,
        ),
		//'status',
		'modified',
		/*
		'modified_by',
		*/
		array(
			'class'=>'CButtonColumn',
            'template'=>'{discovery} {view} {update} {delete}',
            'buttons' => array(
                'discovery' => array(
                    'label' => 'Discovery',
                    'imageUrl'=>Yii::app()->theme->baseUrl.'/css/gridview/discovery.png',
                    'url' => 'Yii::app()->createUrl("discovery/index", array("client_domain_id"=>$data->id))',                   
                    'options' => array(
                        'class'=>'discovery',
                    ),
                ),
            ),
            'htmlOptions'=>array('nowrap'=>'nowrap'),
		),
	),
)); ?>
