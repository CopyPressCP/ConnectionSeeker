<?php
$_iostatuses = Task::$iostatuses;
//print_r($_iostatuses);

$this->breadcrumbs=array(
	'IOs'=>array(strtolower($_iostatuses[$iostatus])),
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

$cs->registerScriptFile(Yii::app()->baseUrl . '/js/raty/jquery.raty.min.js', CClientScript::POS_HEAD);


$types = Types::model()->bytype(array("linktask","channel"))->findAll();
$gtps = CHtml::listData($types, 'refid', 'typename', 'type');
//print_r($gtps);
$linktask = $gtps['linktask'];
$tasktypestr = Utils::array2String($linktask);

$channels = $gtps['channel'] ? $gtps['channel'] : array();
natcasesort($channels);
$channelstr = Utils::array2String($channels);

$tiers = CampaignTask::$tier;
$tierstr = Utils::array2String($tiers);

$iostatuses = Task::$iostatuses;
$iostatusesstr =  Utils::array2String($iostatuses);


$iolabel = $_iostatuses[$iostatus];
?>

<h1>Rating Reporting</h1>
<div id="processing" style="float:left;width:220px;">&nbsp;</div>
<br />
<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'task-grid',
	'dataProvider'=>$model->with('rrating')->haverating()->search(),
	'filter'=>$model,
    'selectableRows' => '2',
	'columns'=>array(
        array(
            'name' => 'id',
            'header' => 'Task ID',
        ),
        array(
            'name' => 'campaign_id',
            'type' => 'raw',
            'value' => 'CHtml::encode($data->rcampaign->name)',
            'filter' => isset($roles['Marketer']) ? CHtml::listData(Campaign::model()->byclient()->byduty()->findAll(),'id','name') : null,
        ),
        'anchortext',
        array(
            'name' => 'targeturl',
            'type' => 'raw',
            'value' => 'domain2URL($data->targeturl,true)',
        ),
        //Here you need pay attention to ($data->desired_domain_id ? "D:".$data->desired_domain_id : "C:".$data->channel_id)
        array(
            'name' => 'desired_domain',
            'type' => 'raw',
            'value' => 'domain2URL($data->desired_domain, true, array("target"=>"_blank"))',
        ),
        'rewritten_title',
        /*
        array(
            'name' => 'sourceurl',
            'type' => 'raw',
            'value' => 'domain2URL($data->sourceurl,true)',
        ),
        array(
            'name' => 'livedate',
            'type' => 'raw',
        ),
        */
        array(
            'name' => 'channel_id',
            'type' => 'raw',
            'value' => 'Utils::getValue(' . $channelstr . ', $data->channel_id)',
            'filter' => $channels,
        ),
        array(
            'name' => 'rrating.rating',
            'type' => 'raw',
            'header' => 'Stars',
        ),
        array(
            'name' => 'rrating.created',
            'type' => 'raw',
            'header' => 'Date Feedback Left',
        ),

		array(
			'class'=>'CButtonColumn',
            'template'=>'{rating}',
            'buttons' => array(
                'rating' => array(
                    'label' => 'Add Content Rating',
                    'imageUrl' => Yii::app()->theme->baseUrl.'/css/gridview/star-off.png',
                    'url' => 'Yii::app()->createUrl("task/view", array("id"=>$data->id))',
                    'options' => array('id'=>$this->evaluateExpression('$data->id', array('data'=>$data))),
                    //'visible' => "('$isadmin' || '$isoutreacher')",
                    'click' => "function(){
                        addRating(this);
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
    <div id="ratingboxdiv" style="display:none;margin:5px 10px;"></div>
</div>

<style>
#ratinghint {
    background-color: #F8F8F8;
    border-radius: 5px;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
    padding: 2px 10px;
    display: inline-block;
    font-size: 1.8em;
    height: 27px;
    vertical-align: middle;
    width: 135px;
    color:red;
}
</style>

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
                $("#ratingboxdiv").hide();
                $("#ratingboxdiv").appendTo($("#hiddenContainer"));
            });
            return true;
        });

        $("table.items #pageSize").change(function(){
            $("#ratingboxdiv").hide();
            $("#ratingboxdiv").appendTo($("#hiddenContainer"));
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
            'url': "<?php echo Yii::app()->createUrl('/taskRating/icon');?>",
            'data': {'ids[]': _ids},
            'success':function(data){
                //alert(data.msg);
                if (data.success){
                    if (data.ids){
                        $.each(data.ids, function (v){
                            //alert(v);
                            $("#etr" +v+" > td:last > a.rating > img").attr("src", "<?php echo Yii::app()->theme->baseUrl?>/css/gridview/star-on.png");
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

function addRating(t) {
    //http://wbotelhos.com/raty/

    var gvid = $(t).parent().parent().closest('.grid-view').attr('id');
    var gvoffset = $(t).parent().parent().prevAll().length;
    var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
    //var currentdomain = $(t).parent().parent().children("td:eq(1)").text();

    $("#noteboxdiv").hide();
    if (lastclickid == currenttrid || lastclickid == 0) {
        $("#ratingboxdiv").toggle();
    } else {
        $("#ratingboxdiv").show();
    }

    if ($("#ratingboxdiv").is(":visible")) {

        $.ajax({
            'type': 'GET',
            //'dataType': 'json',
            'dataType': 'html',
            'url': "<?php echo Yii::app()->createUrl('/taskRating/rating');?>",
            'data': 'task_id='+currenttrid+"&ajax=true",
            'success':function(data){
                $("#ratingboxdiv").html(data);
            },
            'complete':function(XHR,TS){XHR = null;}
        });

        if ($("#"+currenttrid+"_dtr").length>0) {
            /*
            here you couldn't use the find("td"), coz it will search all of the posterity td elements,
            The .find() and .children() methods are similar,
            but .children() only travels a single level down the DOM tree.
            */
            $("#ratingboxdiv").appendTo($("#"+currenttrid+"_dtr").children("td"));
        } else {
            var tdlength = $("table.items tr:first > th").length;
            var vartr = $('<tr><td colspan="'+tdlength+'"></td></tr>').attr({ 'id': currenttrid+"_dtr", 'class':"bltr"});
            //$("#ratingboxdiv").appendTo(vartr.find("td"));
            $("#ratingboxdiv").appendTo(vartr.children("td"));
            $(t).parent().parent().after(vartr);
            $('#'+gvid+' > div.keys > span:eq('+gvoffset+')').after("<span>"+currenttrid+"_dtr</span>");
        }

    } else {}

    lastclickid = currenttrid;
}


</script>