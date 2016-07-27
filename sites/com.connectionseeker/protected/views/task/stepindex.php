<?php
$contentios = array("0"=>"Ideation","1"=>"Idea Approval","2"=>"Place Order",
                    "3"=>"Ordered","4"=>"Content Approval","5"=>"Delivered");
$currentlable = "ALL";
if (isset($content_step)) $currentlable = $contentios[$content_step];
$this->breadcrumbs=array(
	'Content Step'=>array("index"),
	$currentlable,
);
$contentiosstr =  Utils::array2String($contentios);

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

$_themebaseurl = Yii::app()->theme->baseUrl;

$cs=Yii::app()->clientScript;
$cs->registerCoreScript( 'jquery.ui' );
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/css/gridview/styles.css');
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/css/gridview/jquery.yiigridview.js', CClientScript::POS_END);
$cs->registerCssFile((isset(Yii::app()->theme) ? Yii::app()->theme->baseUrl.'/css/gridview' : Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('zii.widgets.assets')).'/gridview') . '/styles.css');
Yii::app()->clientScript->registerCssFile(
    Yii::app()->clientScript->getCoreScriptUrl(). '/jui/css/base/jquery-ui.css'
);

$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/char.count.js', CClientScript::POS_END);
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.min.js', CClientScript::POS_END);
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.filter.min.js', CClientScript::POS_END);
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.css');
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.filter.css');

$types = Types::model()->bytype(array("channel",'category'))->findAll();
$gtps = CHtml::listData($types, 'refid', 'typename', 'type');
$channels = $gtps['channel'] ? $gtps['channel'] : array();
natcasesort($channels);
$channelstr = Utils::array2String($channels);

$categories = $gtps['category'] ? $gtps['category'] : array();
$categorystr = Utils::array2String(array("0" => '[Website Category]') + $categories);

$_editors = User::model()->with("rauthassignment")->editor()->findAll();
$cseditors = CHtml::listData($_editors, 'id', 'username');
natcasesort($cseditors);
$cseditorstr = Utils::array2String(array("0" => '[Editor]') + $cseditors);

$tiers = CampaignTask::$tier;
$tierstr = Utils::array2String($tiers);

$isvisible = false;
if ($iostatus == 1 && !isset($roles['Marketer'])) {
    $isvisible = true;
}

$iostatuses = Task::$iostatuses;
$iostatusesstr =  Utils::array2String($iostatuses);

$isadmin = isset($roles['Admin']) ? 1 : 0;
$ispublisher = isset($roles['Publisher']) ? 1 : 0;
$isoutreacher = (isset($roles['InternalOutreach']) || isset($roles['Outreach'])) ? 1 : 0;
$ismarketer = isset($roles['Marketer']) ? 1 : 0;

//##$notetypes = array("1" => 'Ideation', "2"=>"Writer Note", "3"=>"Extra Writer Note");
$notetypes = array("1" => 'Ideation', "2"=>"Client Comment", "3"=>"Extra Writer Note");

//This function you need pay attention to!!!!
function genCategorStr($idstr, $catstr, $allstr){
    if (empty($idstr) || empty($catstr)) {
        return $allstr;//it was passed from str, but here it will become an array; 
    } else {
        $_ids = explode("|", $idstr);
        array_pop($_ids);
        array_shift($_ids);
        $_catestr = explode(", ", $catstr);
        if (count($_ids) == count($_catestr)) {
            $categories = array_combine($_ids, $_catestr);
            return array("0" => '[Website Category]') + $categories;//Here will return the array, not the string!!!!
        } else {
            return $allstr;
        }
    }
}

$clientapproves = array("0" => 'No', "1"=>"Yes");

function editable($isoutreacher,$um_chl_id,$curr_chl_id) {
    if ($isoutreacher) {
        if ($um_chl_id == $curr_chl_id) {
            return true;
        }
        return false;
    }

    return true;
}

if($ismarketer) {
    $clients = Client::model()->byuser()->findAll(array('order'=>'company ASC'));
} else {
    $clients = Client::model()->actived()->findAll(array('order'=>'company ASC'));
}
?>

<h1>Content - <?php echo $currentlable;?></h1>
<div id="processing" style="float:left;width:220px;">&nbsp;</div>
<br />
<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<!-- search-form -->
<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php
$this->renderPartial('/task/_ios_search',array(
	'model'=>$model,
	'_channels'=>$channels,
	'roles'=>$roles,
	'tiers'=>$tiers,
	'clients'=>$clients,
	'iostatuses'=>$iostatuses,
	'content_step'=>$content_step,
));
?>
</div><!-- search-form -->

<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'task-grid',
	'dataProvider'=>$model->contentio()->search(),
	'filter'=>$model,
    'selectableRows' => '2',
	'columns'=>array(
        array(
            'id'=>'ids',
            'class'=>'CCheckBoxColumn',
        ),
        array(
            'name' => 'id',
            'header' => 'Task ID',
        ),
        array(
            //'name' => 'rcampaign.rclient.name',
            'name' => 'client_id',
            'value' => '$data->rcampaign->rclient->name ." (". $data->rcampaign->rclient->company .")"',
            'header' => 'Client',
            'filter' => CHtml::listData($clients,'id','company'),
        ),
        array(
            'name' => 'campaign_id',
            'type' => 'raw',
            'value' => 'CHtml::encode($data->rcampaign->name)." - ".($data->rcampaign->rcampaigntask->internal_done)* 100 ."%"',
            'filter' => isset($roles['Marketer']) ? CHtml::listData(Campaign::model()->byclient()->byduty()->findAll(),'id','name') : null,
        ),

        array(
            'header' => 'Channel',
            'name' => 'channel_id',
            'type' => 'raw',
            'value' => 'Utils::getValue(' . $channelstr . ', $data->channel_id)',
            'filter' => $channels,
        ),

        array(
            'name' => 'desired_domain',
            'type' => 'raw',
            'value' => 'domain2URL($data->desired_domain, true, array("target"=>"_blank"))',
        ),

        array(
            'name' => 'rewritten_title',
            'type' => 'raw',
        ),

        array(
            'name' => 'content_step',
            'type' => 'raw',
            'value' => '$data->content_step ? Utils::getValue(' . $contentiosstr . ', $data->content_step) : "Ideation"',
            'filter' => $contentios,
        ),
        array(
            'name' => 'iostatus',
            'type' => 'raw',
            'value' => '$data->iostatus ? Utils::getValue(' . $iostatusesstr . ', $data->iostatus) : ""',
            'filter' => $iostatuses,
        ),
        array(
            'name' => 'targeturl',
            'type' => 'raw',
            'value' => 'domain2URL($data->targeturl,true)',
        ),
        //##'anchortext',
        array(
            'name' => 'anchortext',
            'type' => 'raw',
        ),
        /*
        array(
            'header' => 'Other/Domain',
            'name' => 'rstep.step_domain',
            'type' => 'raw',
            'value' => '$data->rstep->step_domain',
        ),
        array(
            'name' => 'campaign_approval_type',
            'type' => 'raw',
            'header' => 'CA',
            'value' => 'stripos($data->rcampaign->approval_type, "CA") === false ? "No" : "Yes"',
            'filter' => $clientapproves,
        ),
        array(
            'name' => 'campaign_approval_type',
            'type' => 'raw',
            'header' => 'Client Approval',
            'value' => '($data->rcampaign->approval_type=="TA") ? "Yes" : "No"',
            'filter' => $clientapproves,
        ),
        */
        array(
            'header' => 'Date',
            'name' => 'step_date',
            'type' => 'raw',
        ),
        //'step_date',
		array(
			'class'=>'CButtonColumn',
            'template'=>'{note} {dlhtml} {dldoc}',
            'buttons' => array(

                'note' => array(
                    'label' => 'Add Notes',
                    'imageUrl' => $_themebaseurl.'/css/gridview/note.png',
                    'url' => 'Yii::app()->createUrl("task/view", array("id"=>$data->id))',
                    'visible' => 'editable('.$isoutreacher.','.$um_chl_id.',$data->channel_id)',
                    'options' => array('id'=>$this->evaluateExpression('$data->id', array('data'=>$data)),'name'=>'addiosnote','class'=>'addiosnote',),
                ),

                'dlhtml' => array(
                    'label' => 'Download article as html format',
                    //#'visible' => '($data->tasktype == 1 && $data->rcontent->length && $data->content_step == 5)',
                    'visible' => '($data->rcontent->length && ($data->content_step==4 || $data->content_step==5) )',
                    'imageUrl'=>Yii::app()->theme->baseUrl.'/css/gridview/dlhtml16.png',
                    'url' => 'Yii::app()->createUrl("download/content", array("id"=>$data->id,"format"=>"html"))',
                    'options' => array(
                        'name'=>'dlhtml','class'=>'dlhtml',
                    ),
                ),
                'dldoc' => array(
                    'label' => 'Download article as word format',
                    'visible' => '($data->rcontent->length && ($data->content_step==4 || $data->content_step==5) )',
                    'imageUrl'=>Yii::app()->theme->baseUrl.'/css/gridview/dlword16.png',
                    'url' => 'Yii::app()->createUrl("download/content", array("id"=>$data->id,"format"=>"doc"))',
                    'options' => array(
                        'name'=>'dldoc','class'=>'dldoc',
                    ),
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
    <div id="noteboxdiv" style="display:none;"></div>
    <div id="writernoteboxdiv" style="display:none;"></div>
    <div id="extranoteboxdiv" style="display:none;"></div>
    <div id="ideationnoteboxdiv" style="display:none;margin:5px 10px;"></div>
</div>
<div class="clear"></div>

<script type="text/javascript">
function parseIdFromUrl(s){
    s = s.match(/&id=\d+/g);
    s = String(s);

    return s = s.replace(/&id=/,"");
}

function downloadStep(taskids){
    var url = "<?php echo Yii::app()->createUrl('/contentStep/download');?>";
    url = url + "&Task[id]=" + taskids;
    window.location.href = url;
}

function hideAllBox(){
    $("#noteboxdiv").hide();
    $("#noteboxdiv").appendTo($("#hiddenContainer"));
    return true;
}

var current_step = "<?php echo $content_step;?>";
$(document).ready(function() {
    $("#Task_channel_id").multiselect({noneSelectedText:'Select Channel',selectedList:5}).multiselectfilter();
    $("#Task_iostatus").multiselect({noneSelectedText:'Select IO Status',selectedList:5}).multiselectfilter();

    $('#processing').bind("ajaxSend", function() {
        $(this).html("&nbsp;");
        $(this).css('background-image', 'url(' + "<?php echo Yii::app()->theme->baseUrl; ?>" + '/images/loading.gif)');
    }).bind("ajaxComplete", function() {
        $(this).css('background-image', '');
        $(this).css('color', 'red');
    });

    $("#downloadIO").click(function(){
        var url = "<?php echo Yii::app()->createUrl('/contentStep/download');?>";
        var rparent = $('#taskSearchForm input[type=hidden][name=r]').parent();
        var rself = rparent.html();
        $('#taskSearchForm input[type=hidden][name=r]').remove();
        url = url + "&" + $('#taskSearchForm').serialize();
        rparent.html(rself);
        window.location.href = url;
    });

    $.fn.yiiGridView.defaults.afterAjaxUpdate = function(){
        var lastclickid = 0;

        $("a[name^='addiosnote']").each(function() {
            $(this).unbind('click').click(function(){

                var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
                var gvoffset = $(this).parent().parent().prevAll().length;
                var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);

                var currdiv = "#noteboxdiv";
                var _url = "<?php echo Yii::app()->createUrl('/taskNote/note');?>";
                var _data = 'task_id='+currenttrid+"&ajax=true";

                if (lastclickid == currenttrid || lastclickid == 0) {
                    $(currdiv).toggle();
                } else {
                    $(currdiv).show();
                }

                if ($(currdiv).is(":visible")) {

                    $.ajax({
                        'type': 'GET',
                        //'dataType': 'json',
                        'dataType': 'html',
                        'url': _url,
                        'data': _data,
                        'success':function(data){
                            $(currdiv).html(data);
                        },
                        'complete':function(XHR,TS){XHR = null;}
                    });

                    if ($("#"+currenttrid+"_dtr").length>0) {
                        /*
                        here you couldn't use the find("td"), coz it will search all of the posterity td elements,
                        The .find() and .children() methods are similar,
                        but .children() only travels a single level down the DOM tree.
                        */
                        $(currdiv).appendTo($("#"+currenttrid+"_dtr").children("td"));
                    } else {
                        var tdlength = $("table.items tr:first > th").length;
                        var vartr = $('<tr><td colspan="'+tdlength+'"></td></tr>').attr({ 'id': currenttrid+"_dtr", 'class':"bltr"});
                        //$("#noteboxdiv").appendTo(vartr.find("td"));
                        $(currdiv).appendTo(vartr.children("td"));
                        $(this).parent().parent().after(vartr);
                        $('#'+gvid+' > div.keys > span:eq('+gvoffset+')').after("<span>"+currenttrid+"_dtr</span>");
                    }

                } else {}

                lastclickid = currenttrid;

                return false;
            });
        });

        $("a[name^='dlhtml'],a[name^='dltxt'],a[name^='dldoc']").each(function() {
            $(this).unbind('click').click(function(){
                //download content from copypress
                //alert("download it");
                window.location.href = $(this).attr('href');
                return false;
            });
        });

        //issue: when the user click the pager(i mean change pager number), then the mailbox & notebox may not showing again.
        //cause we append the mailbox & notebox into the grid already when we click the addmail or addnote button,
        //so when we jump to another outreach page number, the mailbox & notebox may be removed due to the grid overwrited.
        $("div.pager > ul > li a, table.items thead tr.filters input, table.items thead tr.filters select").each(function(){
            $(this).click(function() {
                hideAllBox();
            });
            return true;
        });

        $("table.items #pageSize").change(function(){
            hideAllBox();
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
                            $("#etr" +v+" > td:last > a.addiosnote > img").attr("src", "<?php echo $_themebaseurl?>/css/gridview/notes.png");
                        });
                    }

                    if (data.freshnote) {
                        $.each(data.freshnote, function (v){
                            $("#etr" +v+" > td:last > a.addiosnote > img").attr("src", "<?php echo $_themebaseurl?>/css/gridview/freshnote.png");
                        });
                        //alert($.inArray(v, data.freshnote));
                    }
                }
            },
            'complete':function(XHR,TS){XHR = null;}
        });

        //------------------------------------------------//
    }


    $.fn.yiiGridView.defaults.afterAjaxUpdate();
});


function stripURL2Domain(url){
    url = url.replace(/^(www\.)/i, "");
    url = url.replace(/^(ht|f)tp(s?)\:\/\/(www\.){0,1}/i, "");
    url = url.replace(/(\/[\s\S]*)/i, "");

    return url;
}
</script>

<style>
span.warning{color:#600;font-size: 0.9em; font-weight: bold;}	
span.exceeded{color:#e00;font-size: 0.9em; font-weight: bold;}	
</style>
