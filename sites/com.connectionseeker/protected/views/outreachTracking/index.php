<?php
$this->breadcrumbs=array(
	'Outreach Trackings'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('outreach-tracking-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

$touchedstatus = Domain::$status;
$statusstr = Utils::array2String($touchedstatus);
?>

<h1>Manage Outreach Trackings</h1>

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
	'id'=>'outreach-tracking-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'domain_id',
		'domain',
        array(
            'name' => 'before_value',
            'type' => 'raw',
            //'value' => 'CHtml::encode($data->before_value)',
            'value' => 'CHtml::encode(Utils::getValue(' . $statusstr . ', $data->before_value))',
            //'filter' => CHtml::activeDropDownList($model, 'before_value', $touchedstatus, array('multiple' =>'true','id'=>'Domain_status_filter','style'=>'width:160px')),
            'filter' => $touchedstatus,
        ),
        array(
            'name' => 'after_value',
            'type' => 'raw',
            'value' => 'CHtml::encode(Utils::getValue(' . $statusstr . ', $data->after_value))',
            'filter' => $touchedstatus,
        ),
        /*
		'before_value',
		'after_value',
		'created_by',
        */
		'created',
        array(
            'name' => 'created_by',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->rcreatedby->username), array("user/view", "id" =>$data->created_by))',
            'filter' => CHtml::listData(User::model()->findAll(),'id','username'),
        ),
		array(
			'class'=>'CButtonColumn',
            'template'=>'',
		),
	),
)); ?>
