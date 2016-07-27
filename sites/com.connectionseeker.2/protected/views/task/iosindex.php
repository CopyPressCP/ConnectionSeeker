<?php
if ($model->iostatus) $iostatus = $model->iostatus;

$_iostatuses = Task::$iostatuses;
//print_r($_iostatuses);

$this->breadcrumbs=array(
	'Campaign Task'=>array(strtolower($_iostatuses[$iostatus])),
	$_iostatuses[$iostatus],
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('task-grid', {
		data: $(this).serialize()
	});
	return false;
});
");


$cs=Yii::app()->clientScript;
$cs->registerCoreScript( 'jquery.ui' );
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/css/gridview/styles.css');
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/css/gridview/jquery.yiigridview.js', CClientScript::POS_END);
$cs->registerCssFile((isset(Yii::app()->theme) ? Yii::app()->theme->baseUrl.'/css/gridview' : Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('zii.widgets.assets')).'/gridview') . '/styles.css');
Yii::app()->clientScript->registerCssFile(
    Yii::app()->clientScript->getCoreScriptUrl(). '/jui/css/base/jquery-ui.css'
);

$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.min.js', CClientScript::POS_END);
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.filter.min.js', CClientScript::POS_END);
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.css');
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.filter.css');


$types = Types::model()->bytype(array("linktask","channel"))->findAll();
$gtps = CHtml::listData($types, 'refid', 'typename', 'type');
//print_r($gtps);
$linktask = $gtps['linktask'];
$tasktypestr = Utils::array2String($linktask);

$channels = $gtps['channel'] ? $gtps['channel'] : array();
$channelstr = Utils::array2String($channels);

$tiers = CampaignTask::$tier;
$tierstr = Utils::array2String($tiers);

$pgstatus = Task::$pgstatus;
$pgstatusstr =  Utils::array2String($pgstatus);

$iostatuses = Task::$iostatuses;
$iostatusesstr =  Utils::array2String($iostatuses);


$isvisible = false;
if ($iostatus == 1 && !isset($roles['Marketer'])) {
    $isvisible = true;
}

$isadmin = 0;//false
if(isset($roles['Admin']) || isset($roles['Marketer'])){
    $isadmin = 1;//true
}

$ispublisher = 0;//false
if(isset($roles['Publisher']) || isset($roles['InternalOutreach']) || isset($roles['Outreach'])){
    $ispublisher = 1;//true
}

$ismarketer = 0;
if(isset($roles['Marketer'])){
    $ismarketer = 1;//true
}

$iolabel = $_iostatuses[$iostatus];
?>

<h1><?php echo $iolabel; ?> IOs</h1>
<div id="processing" style="float:left;width:220px;">&nbsp;</div>
<br />
<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<!-- search-form -->
<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('/task/_ios_search',array(
	'model'=>$model,
	'iostatus'=>$iostatus,
	'_channels'=>$channels,
	'roles'=>$roles,
	'tiers'=>$tiers,
)); ?>
</div><!-- search-form -->

<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'task-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
    'selectableRows' => '2',
	'columns'=>array(
        array(
            'name' => 'campaign_id',
            'type' => 'raw',
            'value' => 'CHtml::encode($data->rcampaign->name)',
            'filter' => isset($roles['Marketer']) ? CHtml::listData(Campaign::model()->byclient()->byduty()->findAll(),'id','name') : null,
        ),
        array(
            'name' => 'iostatus',
            'type' => 'raw',
            'value' => '$data->iostatus ? Utils::getValue(' . $iostatusesstr . ', $data->iostatus) : ""',
            'filter' => $iostatuses,
        ),
        array(
            'name' => 'anchortext',
            'visible' => isVisible('anchortext', $dparr),
        ),
        array(
            'name' => 'targeturl',
            'type' => 'raw',
            'value' => 'domain2URL($data->targeturl,true)',
            'visible' => isVisible('targeturl', $dparr),
        ),
        array(
            'name' => 'tierlevel',
            'type' => 'raw',
            'value' => 'Utils::getValue(' . $tierstr . ', $data->tierlevel)',
            'filter' => $tiers,
            'visible' => isVisible('tierlevel', $dparr),
        ),
        //Here you need pay attention to ($data->desired_domain_id ? "D:".$data->desired_domain_id : "C:".$data->channel_id)
        array(
            'name' => 'desired_domain',
            'type' => 'raw',
            'value' => '($data->desired_domain) ? domain2URL($data->desired_domain, true, array("target"=>"_blank")) : ""',
            'visible' => isVisible('desired_domain_id', $dparr),
        ),
        array(
            'name' => 'rewritten_title',
            'type' => 'raw',
            'value' => '($data->rewritten_title) ? CHtml::encode($data->rewritten_title) : ""',
            'visible' => isVisible('rewritten_title', $dparr),
        ),
        array(
            'name' => 'sourceurl',
            'type' => 'raw',
            'value' => '($data->sourceurl) ? domain2URL($data->sourceurl,true) : ""',
            'visible' => isVisible('sourceurl', $dparr),
        ),

        array(
            'name' => 'livedate',
            'type' => 'raw',
            'value' => '($data->livedate) ? $data->livedate : ""',
            'visible' => isVisible('livedate', $dparr),
        ),

        array(
            'name' => 'channel_id',
            'type' => 'raw',
            'value' => 'Utils::getValue(' . $channelstr . ', $data->channel_id)',
            'filter' => $channels,
            'visible' => isVisible('channel_id', $dparr) && !$ispublisher,
        ),
        /*
        array(
            'name' => 'client_request',
            'type' => 'raw',
            'value' => 'CHtml::link("Client Request", "javascript:void(0);", array("class"=>"clientrequest"))',
            'visible' => isVisible('client_request', $dparr),
        ),
        */
        array(
            'name' => 'duedate',
            'type' => 'raw',
            'value' => '$data->duedate ? date("M/d/Y",$data->duedate) : ""',
        ),
        array(
            'name' => 'iodate',
            'header' => 'Date '.$iolabel,
            'value' => '$data->iodate ? $data->iodate : "00-00-0000"',
        ),

		array(
			'class'=>'CButtonColumn',
            'template'=>'{note}',
            'buttons' => array(
                'note' => array(
                    'label' => 'Add Notes',
                    'imageUrl' => Yii::app()->theme->baseUrl.'/css/gridview/note.png',
                    'url' => 'Yii::app()->createUrl("task/view", array("id"=>$data->id))',
                    'options' => array('id'=>$this->evaluateExpression('$data->id', array('data'=>$data))),
                    'click' => "function(){
                        addNote(this);
                        return false;
                    }",
                ),
            ),
            'htmlOptions'=>array(
                'nowrap'=>'nowrap',
            )
        ),
	),
)); ?>

<div class="clear"></div>


<div id="hiddenContainer">
    <div id="noteboxdiv" style="display:none;">
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    $("#Task_channel_id").multiselect({noneSelectedText:'Select Channel',selectedList:5}).multiselectfilter();
    $('#processing').bind("ajaxSend", function() {
        $(this).html("&nbsp;");
        $(this).css('background-image', 'url(' + "<?php echo Yii::app()->theme->baseUrl; ?>" + '/images/loading.gif)');
        //$(this).show();
    }).bind("ajaxComplete", function() {
        $(this).css('background-image', '');
        $(this).css('color', 'red');
        //$(this).hide();
    });


    $.fn.yiiGridView.defaults.afterAjaxUpdate = function(){

        //issue: when the user click the pager(i mean change pager number), then the mailbox & notebox may not showing again.
        //cause we append the mailbox & notebox into the grid already when we click the addmail or addnote button,
        //so when we jump to another outreach page number, the mailbox & notebox may be removed due to the grid overwrited.
        $("div.pager > ul > li a, table.items thead tr.filters input, table.items thead tr.filters select").each(function(){
            $(this).click(function() {
                $("#noteboxdiv").hide();
                $("#noteboxdiv").appendTo($("#hiddenContainer"));
            });
            return true;
        });

        $("table.items #pageSize").change(function(){
            $("#noteboxdiv").hide();
            $("#noteboxdiv").appendTo($("#hiddenContainer"));
            return true;
        });

        //the following code for the note icon changing.
        //------------------------------------------------//
        var _ids = [];
        $('#task-grid > div.keys > span').each(function(i){
            _ids[i] = $(this).html();
        });
        $("#task-grid > table.items > tbody > tr").each(function(i){
            $(this).attr("id", "etr"+_ids[i]);//reset table.tr.id
        });
        $.ajax({
            'type': 'POST',
            'dataType': 'json',
            'url': "<?php echo Yii::app()->createUrl('/taskNote/icon');?>",
            'data': {'ids[]': _ids},
            'success':function(data){
                //alert(data.msg);
                if (data.success){
                    if (data.ids){
                        $.each(data.ids, function (v){
                            //alert(v);
                            $("#etr" +v+" > td:last > a.note > img").attr("src", "<?php echo Yii::app()->theme->baseUrl?>/css/gridview/notes.png");
                        });
                    }
                }
            },
            'complete':function(XHR,TS){XHR = null;}
        });
        //------------------------------------------------//
    }


    $.fn.yiiGridView.defaults.afterAjaxUpdate();
});

var lastclickid = 0;

function addNote(t) {
    var gvid = $(t).parent().parent().closest('.grid-view').attr('id');
    var gvoffset = $(t).parent().parent().prevAll().length;
    var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
    //var currentdomain = $(t).parent().parent().children("td:eq(1)").text();

    if (lastclickid == currenttrid || lastclickid == 0) {
        $("#noteboxdiv").toggle();
    } else {
        $("#noteboxdiv").show();
    }

    if ($("#noteboxdiv").is(":visible")) {

        $.ajax({
            'type': 'GET',
            //'dataType': 'json',
            'dataType': 'html',
            'url': "<?php echo Yii::app()->createUrl('/taskNote/note');?>",
            'data': 'task_id='+currenttrid+"&ajax=true",
            'success':function(data){
                $("#noteboxdiv").html(data);
            },
            'complete':function(XHR,TS){XHR = null;}
        });

        if ($("#"+currenttrid+"_dtr").length>0) {
            /*
            here you couldn't use the find("td"), coz it will search all of the posterity td elements,
            The .find() and .children() methods are similar,
            but .children() only travels a single level down the DOM tree.
            */
            $("#noteboxdiv").appendTo($("#"+currenttrid+"_dtr").children("td"));
        } else {
            var tdlength = $("table.items tr:first > th").length;
            var vartr = $('<tr><td colspan="'+tdlength+'"></td></tr>').attr({ 'id': currenttrid+"_dtr", 'class':"bltr"});
            //$("#noteboxdiv").appendTo(vartr.find("td"));
            $("#noteboxdiv").appendTo(vartr.children("td"));
            $(t).parent().parent().after(vartr);
            $('#'+gvid+' > div.keys > span:eq('+gvoffset+')').after("<span>"+currenttrid+"_dtr</span>");
        }

    } else {}

    lastclickid = currenttrid;
}

</script>