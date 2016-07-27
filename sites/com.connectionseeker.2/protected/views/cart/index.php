<?php
$this->breadcrumbs=array(
	'Carts'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('cart-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

$roles = Yii::app()->authManager->getRoles(Yii::app()->user->id);
if(isset($roles['Marketer'])) {
    $ftclients = Client::model()->byuser()->findAll();
} else {
    $ftclients = Client::model()->actived()->findAll();
}
$ftclients = CHtml::listData($ftclients,'id','company');

$status = Cart::$dstatus;
$statusstr = Utils::array2String($status);
?>

<h1>Manage Carts</h1>

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
	'roles'=>$roles,
)); ?>
</div><!-- search-form -->

<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'cart-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
        array(
            'name' => 'client_id',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->rclient->company), array("client/view", "id" =>$data->client_id))',
            'filter' => $ftclients,
        ),
		'client_domain',
		'domain',
		/*
		'client_domain_id',
		'domain_id',
		*/
		'created',
        array(
            'name' => 'created_by',
            'type' => 'raw',
            'value' => 'CHtml::encode($data->rcreatedby->username)',
            'filter' => CHtml::listData(User::model()->actived()->findAll(),'id','username'),
        ),
        array(
            'name' => 'status',
            'value' => 'CHtml::encode(Utils::getValue(' . $statusstr . ', $data->status))',
            //'value' => 'CHtml::dropDownList("status", $data->status, '.$statusstr.')',
            'filter' => $status,
        ),
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
