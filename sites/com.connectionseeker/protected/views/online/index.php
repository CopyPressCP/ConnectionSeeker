<?php
$this->breadcrumbs=array(
	'Onlines'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('online-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Onlines</h1>

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
	'id'=>'online-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
        array(
            'name' => 'user_id',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->rcreatedby->username), array("user/view", "id" =>$data->user_id))',
            'filter' => CHtml::listData(User::model()->findAll(),'id','username'),
        ),
		'date_tracked',
        array(
            'name' => 'login_time',
            'type' => 'raw',
            'value' => 'date("Y-m-d H:i:s", $data->login_time)',
            //'value'=>'Yii::app()->dateFormatter->format("y-M-d H:m:s",$data->login_time)',
        ),
        array(
            'name' => 'total_online',
            'type' => 'raw',
            'value' => 'gmdate("H:i:s", $data->total_online)',
            //'value' => 'printf( "%d hours %d minutes",floor($data->total_online/3600),floor($data->total_online/60)%60)',
        ),
		/*
        array(
            'name' => 'session_online',
            'type' => 'raw',
            'value' => 'gmdate("H:i:s", $data->session_online)',
            //'value' => 'printf( "%d hours %d minutes",floor($data->session_online/3600),floor($data->session_online/60)%60)',
        ),
		'total_online',
		'session_online',
		'login_time',
		'user_id',
		'last_operation_time',
		*/
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
