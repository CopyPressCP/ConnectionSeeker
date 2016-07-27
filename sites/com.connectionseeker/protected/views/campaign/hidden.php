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
    $clients = Client::model()->byuser()->findAll(array('order'=>'company ASC'));
    $visible = false;
} else {
    $clients = Client::model()->actived()->findAll(array('order'=>'company ASC'));
    $visible = true;
}

$isadmin = false;
if (isset($roles['Admin'])) {
    $isadmin = true;
}

$_adminroles = User::model()->with("rauthassignment")->csadmin()->findAll();
$csadmins = CHtml::listData($_adminroles, 'id', 'username');
natcasesort($csadmins);
$csadminstr = Utils::array2String(array("0" => '[Owner]') + $csadmins);

$hiddenarr = array("0"=>"Not Hidden", "1"=>"Hidden");
$hiddenstr = Utils::array2String($hiddenarr);


$doneoptions = array("0"=>"Not Finished","1"=>"Finished");

function formatol($_o, $_l){ return $_o."/".$_l; }
?>

<h1>Manage Campaigns</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<!-- search-form -->
<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
	'doneoptions'=>$doneoptions,
)); ?>
</div><!-- search-form -->

<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'campaign-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
        array(
            'name' => 'name',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->name), array("task/processing", "campaign_id" =>$data->id))',
        ),
		'domain',
        array(
            'name' => 'client_id',
            //'name' => 'rclient.company',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->rclient->company), array("client/view", "id" =>$data->client_id))',
            'filter' => CHtml::listData($clients,'id','company'),
            'visible' => $visible,
        ),

		'category_str',
        array(
            'name' => 'owner',
            'type' => 'raw',
            //'value' => isset($roles['Admin']) ? 'CHtml::dropDownList("owner[]", $data->owner, '.$csadminstr.')' : 'Utils::getValue(' . $csadminstr . ', $data->owner)',
            'value' => 'Utils::getValue(' . $csadminstr . ', $data->owner)',
            'visible' => isset($roles['Admin']) || isset($roles['TeamLead']),
            'filter' => $csadmins,
        ),
        array(
            'name' => 'ishidden',
            'type' => 'raw',
            //'value' => '$data->ishidden ? "Hidden" : "Not Hidden"',
            'value' => isset($roles['Admin']) ? 'CHtml::dropDownList("ishidden[]", $data->ishidden, '.$hiddenstr.')' : 'Utils::getValue(' . $hiddenstr . ', $data->ishidden)',
            'filter' => $hiddenarr,
        ),
        array(
            'name' => 'rcampaigntask.total_count',
            'type' => 'raw',
            'header' => 'O/L',
            'value' => 'formatol($data->rcampaigntask->total_count, $data->rcampaigntask->published_count)',
        ),
        /*
        array(
            'name' => 'rcampaigntask.total_count',
            'type' => 'raw',
            'header' => 'Ordered',
        ),
        array(
            'name' => 'bckw_percentage_done',
            'type' => 'raw',
            'value' => '($data->rcampaigntask->percentage_done)*10 ? round(1/$data->rcampaigntask->percentage_done,2) : 0',
        ),
		'rcampaigntask.total_count',
		'rcampaigntask.published_count',
		//'rcampaigntask.percentage_done',
        */
        array(
            //'name' => 'rcampaigntask.internal_done',
            'header' => 'Percentage Done - A',
            'name' => 'rct_internal_done',
            'type' => 'raw',
            'filter' => $doneoptions,
            'value' => '($data->rcampaigntask->internal_done)* 100 ."%"',
        ),
        array(
            //'name' => 'rcampaigntask.percentage_done',
            'name' => 'rct_percentage_done',
            'header' => 'Percentage Done - C',
            'type' => 'raw',
            'value' => '($data->rcampaigntask->percentage_done)* 100 ."%"',
            'filter' => $doneoptions,
        ),
        array(
            'name' => 'duedate',
            'type' => 'raw',
            //'value' => '$data->duedate ? date("M/d/Y",$data->duedate) : ""',
            'value' => '$data->duedate ? date("M/d/Y",strtotime($data->duedate)) : ""',
        ),
		/*
        'domain_id',
		'name',
		'category',
		'notes',
		'status',
		'created',
		'created_by',
		'modified',
		'modified_by',
		'rcampaigntask.total_count',
		'rcampaigntask.published_count',
		'rcampaigntask.percentage_done',
		*/
		array(
			'class'=>'CButtonColumn',
            'template'=>'',
            /*
            'template'=>'{download} {view} {update} {delete}',
            'buttons' => array(
                'download' => array(
                    'label' => 'Download Tasks',
                    //'visible' => '',
                    'imageUrl'=>Yii::app()->theme->baseUrl.'/css/gridview/download.png',
                    'url' => 'Yii::app()->createUrl("download/task", array("Task[campaign_id]"=>$data->id))',
                    'options' => array(
                        'class'=>'download',
                    ),
                ),
                'view' => array(
                    'visible' => "$isadmin",
                ),
                'update' => array(
                    'visible' => "$isadmin",
                ),
                'delete' => array(
                    'visible' => "$isadmin",
                ),
            ),
            'htmlOptions'=>array('nowrap'=>'nowrap'),
            */
		),
	),
)); ?>

<script type="text/javascript">
$(document).ready(function() {
    $.fn.yiiGridView.defaults.afterAjaxUpdate = function(){
        $("select[name^='owner'],select[name^='ishidden']").each(function() {
            $(this).unbind('click').change(function(){
                var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
                var gvoffset = $(this).parent().parent().prevAll().length;
                var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
                var thistd = $(this);

                $.ajax({
                    'type': 'GET',
                    'dataType': 'json',
                    'url': "<?php echo CHtml::normalizeUrl(array('/campaign/setattr'));?>",
                    'data': 'id='+currenttrid+"&attrname="+$(this).attr('name')+"&attrvalue="+this.value,
                    'success':function(data){
                        if (data.success){
                            $(thistd).css("background-color","yellow");
                        } else {
                            $(thistd).css("background-color","red");
                            alert(data.msg);
                        }
                    },
                    'complete':function(XHR,TS){XHR = null;}
                });
            });
        });
    }

    $.fn.yiiGridView.defaults.afterAjaxUpdate();
});
</script>