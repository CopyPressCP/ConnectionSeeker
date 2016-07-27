<?php
$this->breadcrumbs=array(
	'Client Discoveries'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('client-discovery-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

$roles = Yii::app()->authManager->getRoles(Yii::app()->user->id);
if(isset($roles['Marketer'])) {
    $clients = Client::model()->byuser()->findAll();
} else {
    $clients = Client::model()->actived()->findAll();
}

$pgsteps = ClientDiscovery::$steps;
$pgstepsstr = Utils::array2String($pgsteps);

$status = array("0"=>"No","1"=>"Yes");
if (!isset($_REQUEST["ClientDiscovery"]["status"])) {
    $model->status = 1;
}

$queueobj = json_decode($queueobj);
?>

<div id="innermenu">
    <?php $this->renderPartial('/clientDiscovery/_menu'); ?>
</div>

<h2>Manage Email Tasks</h2>
<h3>Count Time(<?php echo $queueobj->querytime;?>) Total Queue(<?php echo $queueobj->total;?>) Total Potential:(<?php echo $queueobj->total_potential;?>)</h3>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div>

<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'client-discovery-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
        array(
            'name' => 'client_id',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->rclient->company), array("client/view", "id" =>$data->client_id))',
            'filter' => $ftclients,
            'filter' => CHtml::listData($clients,'id','company'),
        ),
		'domain',
		'competitora',
		'competitorb',
        array(
            'name' => 'progress',
            'type' => 'raw',
            'value' => 'CHtml::encode(Utils::getValue(' . $pgstepsstr . ', $data->progress))',
            'filter' => $pgsteps,
        ),
        array(
            'name' => 'status',
            'type' => 'raw',
            'header' => 'Active',
            'value' => '$data->status ? "Yes" : "No"',
            'filter' => $status,
        ),
		'created',
        array(
            'name' => 'created_by',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->rcreatedby->username), array("user/view", "id" =>$data->created_by))',
            'filter' => CHtml::listData(User::model()->findAll(),'id','username'),
        ),
		/*
		'progress',
		'client_id',
		'domain_id',
		'competitora_id',
		'competitorb_id',
		'created_by',
		'modified',
		'modified_by',
		*/
		array(
			'class'=>'CButtonColumn',
            'template'=>'{emcompared} {emlqueue} {emlsent} {view} {update} {delete}',
            'buttons' => array(
                'emcompared' => array(
                    'label' => 'Sites Compared',
                    'imageUrl' => Yii::app()->theme->baseUrl.'/css/gridview/discovery.png',
                    'url' => 'Yii::app()->createUrl("discoveryBacklink/compared", array("DiscoveryBackdomain[discovery_id]"=>$data->id))',
                    'options' => array(
                        'name'=>'emcompared',
                    ),
                ),
                'emlqueue' => array(
                    'label' => 'Email Queue',
                    'imageUrl' => Yii::app()->theme->baseUrl.'/css/gridview/emlqueue.png',
                    //'visible' => '($data->status == 1) && ($data->complete_with_automation == 1)',
                    'visible' => '($data->complete_with_automation == 1)',
                    'url' => 'Yii::app()->createUrl("clientDiscovery/queue", array("id"=>$data->id))',
                    'options' => array(
                        'name'=>'emlqueue',
                    ),
                ),

                'emlsent' => array(
                    'label' => 'Email Sent',
                    'imageUrl' => Yii::app()->theme->baseUrl.'/css/gridview/emlsent.png',
                    //'visible' => '($data->status == 1) && ($data->complete_with_automation == 1)',
                    'visible' => '($data->complete_with_automation == 1)',
                    'url' => 'Yii::app()->createUrl("automation/sent", array("client_discovery_id"=>$data->id, "type"=>"client_discovery_id"))',
                    'options' => array(
                        'name'=>'emlsent',
                    ),
                ),
            ),
            'htmlOptions'=>array(
                'nowrap'=>'nowrap',
            ),
		),
	),
)); ?>
