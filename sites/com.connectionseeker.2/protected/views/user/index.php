<?php
$this->breadcrumbs=array(
	'Users'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('user-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

$types = Types::model()->bytype(array("channel"))->findAll();
$channels = CHtml::listData($types, 'refid', 'typename');
$channelstr = Utils::array2String($channels);

$status = array("0"=>"No","1"=>"Yes");

if (!isset($_REQUEST["User"]["status"])) {
    $model->status = 1;
}
?>

<h1>Manage Users</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<!-- search-form -->
<?php
/*
$this->renderPartial('_search',array(
	'model'=>$model,
));
*/
Yii::import('system.web.helpers.CHtml', true);

?>
<!-- search-form -->

<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'user-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
    'selectableRows' => '2',
	'columns'=>array(
        array(
            'id'=>'ids',
            'class'=>'CCheckBoxColumn',
        ),
		'id',
		'username',
		'email',
		'last_visit_time',
        array(
            'name' => 'created_by',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->createdby->username), array("user/view", "id" =>$data->created_by))',
            'filter' => CHtml::listData(User::model()->findAll(),'id','username'),
        ),
        array(
            'name' => 'client_id',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->rclient->company), array("client/view", "id" =>$data->client_id))',
            'filter' => CHtml::listData(Client::model()->findAll(),'id','company'),
        ),
        array(
            'name' => 'channel_id',
            'type' => 'raw',
            'value' => 'Utils::getValue(' . $channelstr . ', $data->channel_id)',
            'filter' => $channels,
        ),
		'rauthassignment.itemname',
		//'createdby.username',

        array(
            'name' => 'status',
            'type' => 'raw',
            'header' => 'Active',
            'value' => '$data->status ? "Yes" : "No"',
            'filter' => $status,
        ),
		/*
		'modified',
		'modified_by',
		'last_visit_time',
		*/
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
