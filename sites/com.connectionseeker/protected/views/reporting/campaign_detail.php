<?php
$this->breadcrumbs=array(
	'Campaigns'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('campaign-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

$roles = Yii::app()->authManager->getRoles(Yii::app()->user->id);
if(isset($roles['Marketer'])) {
    $clients = Client::model()->byuser()->findAll();
    $visible = false;
} else {
    $clients = Client::model()->actived()->findAll();
    $visible = true;
}

$isadmin = false;
if (isset($roles['Admin'])) {
    $isadmin = true;
}

$types = Types::model()->bytype(array("channel"))->findAll();
$gtps = CHtml::listData($types, 'refid', 'typename', 'type');
$channels = $gtps['channel'];
natcasesort($channels);
$channelstr = Utils::array2String($channels);

$i = 0;
?>

<h1>Manage <?php echo $cmpmodel->rcampaign->name; ?></h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'campaign-grid',
	//'dataProvider'=>$model->search(),
	'dataProvider'=>$summaries,
	'filter'=>$model,
	'columns'=>array(
		//'id',
        array(
            'name' => 'channel_id',
            'header' => 'Channel ID',
            'type' => 'raw',
            //'value' => 'CHtml::link(CHtml::encode($data->id), array("task/processing", "campaign_id" =>$data->id))',
            'value' => '$data["channel_id"]',
            'footer' => 'Total',
        ),
        array(
            //'name' => 'channel_name',
            'name' => 'channel_id',
            'header' => 'Channel Name',
            'type' => 'raw',
            //'value' => 'empty($data["channel_id"]) ? "No Channel" : Utils::getValue(' . $channelstr . ', $data["channel_id"])',
            'value' => 'empty($data["channel_id"]) ? "No Channel" : CHtml::link(Utils::getValue(' . $channelstr . ', $data["channel_id"], true), array("ios/", "Task[campaign_id]" => '.$cmpmodel->campaign_id.', "Task[channel_id][]" => $data["channel_id"]))',
            'filter' => $channels,
            'footer' => "Ordered: (".$cmpmodel->total_count.")",
        ),
        array(
            'name' => 'total_count',//we just use id for rewriting person total
            'header' => 'Total',
            'value' => '$data["total_count"]',
            'type' => 'raw',
            'filter' => false,
        ),
        array(
            'name' => 'init_count',
            'header' => 'Initial',
            'type' => 'raw',
            'value' => '$data["init_count"]',
            'footer' => $others["init_count"],
            'filter' => false,
        ),
        array(
            'name' => 'current_count',
            'header' => 'Current',
            'type' => 'raw',
            'value' => '$data["current_count"]',
            'footer' => $others["current_count"],
            'filter' => false,
       ),
        array(
            'name' => 'accepted_count',
            'header' => 'Accepted',
            'type' => 'raw',
            'value' => '$data["accepted_count"]',
            'footer' => $others["accepted_count"],
            'filter' => false,
        ),
        array(
            'name' => 'pending_count',
            'header' => 'Pending',
            'type' => 'raw',
            'value' => '$data["pending_count"]',
            'footer' => $others["pending_count"],
            'filter' => false,
        ),
        array(
            'name' => 'approved_count',
            'header' => 'Approved',
            'type' => 'raw',
            'value' => '$data["approved_count"]',
            'footer' => $cmpmodel->approved_count,
            'filter' => false,
        ),
        array(
            'header' => 'Pre QA',
            'name' => 'qa_count',
            'type' => 'raw',
            'value' => '$data["qa_count"]',
            'footer' => $cmpmodel->qa_count,
            'filter' => false,
        ),
        array(
            'header' => 'In Repair',
            'name' => 'inrepair_count',
            'type' => 'raw',
            'value' => '$data["inrepair_count"]',
            'footer' => $cmpmodel->inrepair_count,
            'filter' => false,
        ),
        array(
            'header' => 'Post QA',
            'name' => 'published_count',
            'type' => 'raw',
            'value' => '$data["published_count"]',
            'footer' => $cmpmodel->published_count,
            'filter' => false,
        ),
	),
)); ?>
