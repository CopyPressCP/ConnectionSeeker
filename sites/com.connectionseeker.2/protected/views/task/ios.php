<?php
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

$nextvalue = 3;
if ($iostatus == 2) {
    $nextvalue = 21;
} elseif ($iostatus == 21) {
    $nextvalue = 3;
}

if ($iostatus == 1) {
    $denyvalue = 4;
} else {
    $denyvalue = 1;
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
            'id'=>'ids',
            'class'=>'CCheckBoxColumn',
            'visible' => ($iostatus == 1),
        ),
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
            'value' => '($data->iostatus != 2) || '."$ismarketer".'? domain2URL($data->desired_domain, true, array("target"=>"_blank")) : CHtml::textField("desired_domain[]", $data->desired_domain)',
            'visible' => isVisible('desired_domain_id', $dparr),
        ),
        array(
            'name' => 'rewritten_title',
            'type' => 'raw',
            'value' => '($data->iostatus != 2) || '."$ismarketer".' ? CHtml::encode($data->rewritten_title) : CHtml::textField("rewritten_title[]", $data->rewritten_title)',
            //'value' => 'in_array($data->iostatus, array(1,3,4,21,5)) ? CHtml::encode($data->rewritten_title) : CHtml::textField("rewritten_title[]", $data->rewritten_title)',
            'visible' => isVisible('rewritten_title', $dparr),
        ),
        array(
            'name' => 'sourceurl',
            'type' => 'raw',
            //'value' => '($data->iostatus == 4 || $data->iostatus == 21 || $data->iostatus == 5) ? CHtml::encode($data->sourceurl) : CHtml::textField("sourceurl[]", $data->sourceurl)',
            'value' => 'in_array($data->iostatus, array(4,21,5)) || '."$ismarketer".' ? domain2URL($data->sourceurl,true) : CHtml::textField("sourceurl[]", $data->sourceurl)',
            'visible' => isVisible('sourceurl', $dparr) && ($iostatus != 1 && $iostatus != 2),
        ),
        array(
            'name' => 'publication_pending',
            'type' => 'raw',
            'value' => 'CHtml::checkBox("publication_pending[]", $data->publication_pending)',
            'visible' => isVisible('publication_pending', $dparr) && ($iostatus == 3),
        ),

        array(
            'name' => 'livedate',
            'type' => 'raw',
            'value' => 'in_array($data->iostatus, array(4,21,5)) || '."$ismarketer".' ? $data->livedate : CHtml::textField("livedate[]", $data->livedate, array("id"=>"livedate_".$data->id, "readOnly"=>"readOnly"))',
            //'visible' => isVisible('livedate', $dparr) && ($iostatus == 3),
            'visible' => isVisible('livedate', $dparr) && ($iostatus == 3 || $iostatus == 5),
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
            'template'=>'{note} {accept} {approve} {rewind} {denywithreason} {deny}',
            'buttons' => array(
                'accept' => array(
                    'label' => 'Accept Or Resolved',
                    'imageUrl' => Yii::app()->theme->baseUrl.'/css/gridview/accept.png',
                    'visible' => "'$isvisible' ||  ($isadmin && " . '($data->iostatus == 4))',
                    'url' => 'Yii::app()->createUrl("task/setattr", array("id"=>$data->id,"attrname"=>"iostatus","attrvalue"=>2))',
                    'options' => array(
                        'name'=>'ioaccept',
                    ),
                ),
                'approve' => array(
                    'label' => 'Approve',
                    'imageUrl' => Yii::app()->theme->baseUrl.'/css/gridview/approve.png',
                    'visible' => "(($ispublisher && " . '$data->iostatus == 2) || ($data->iostatus == 21 && '.$isadmin.'))',
                    //'visible' => "($isadmin && " . 'in_array($data->iostatus, array(2,21))) || '."($ispublisher && ".'in_array($data->iostatus, array(2,21)))',
                    'url' => 'Yii::app()->createUrl("task/setattr", array("id"=>$data->id,"attrname"=>"iostatus","attrvalue"=>'.$nextvalue.'))',
                    'options' => array(
                        'name'=>'ioaccept',
                    ),
                ),

                'rewind' => array(
                    'label' => 'Rewind',
                    'imageUrl' => Yii::app()->theme->baseUrl.'/css/gridview/rewind.png',
                    'visible' => '($data->iostatus == 3)',
                    'url' => 'Yii::app()->createUrl("task/setattr", array("id"=>$data->id,"attrname"=>"iostatus","attrvalue"=>2))',
                    'options' => array(
                        'name'=>'ioaccept',
                    ),
                ),

                'denywithreason' => array(
                    'label' => 'Deny With Reason',
                    'imageUrl' => Yii::app()->theme->baseUrl.'/css/gridview/deny2reason.png',
                    'url' => 'Yii::app()->createUrl("task/setattr", array("id"=>$data->id,"attrname"=>"iostatus","attrvalue"=>'.$denyvalue.'))',
                    'visible' => "'$isvisible'",
                    'options' => array(
                        'name'=>'ioreason',
                    ),
                ),
                'deny' => array(
                    'label' => 'Deny the entire row',
                    'imageUrl' => Yii::app()->theme->baseUrl.'/css/gridview/deny.png',
                    //'visible' => "'$isvisible' || ($isadmin && " . '($data->iostatus == 2 || $data->iostatus == 21))',
                    'visible' => "'$isvisible' || (($ispublisher && " . '$data->iostatus == 2) || ($data->iostatus == 21 && '.$isadmin.'))',
                    'url' => 'Yii::app()->createUrl("task/setattr", array("id"=>$data->id,"attrname"=>"iostatus","attrvalue"=>'.$denyvalue.'))',
                    'options' => array(
                        'name'=>'iodeny',
                    ),
                ),
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

<!-- bulk-io-form -->
<?php
if ($iostatus == 1 && $isvisible) {
    $this->renderPartial('/task/_bulkoprios',array(
        'model'=>$model,
        'roles'=>$roles,
        'iostatus'=>$iostatus,
    ));
}
?>
<!-- bulk-io-form -->


<?php
$this->renderPartial('/task/_denyreason',array(
	'model'=>$model,
));
?>

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

    $("#downloadIO").click(function(){
        //var url = "<?php echo Yii::app()->createUrl('/download/ios');?>";
        var url = "<?php echo Yii::app()->createUrl('/ios/download');?>";
        var rparent = $('#taskSearchForm input[type=hidden][name=r]').parent();
        var rself = rparent.html();
        $('#taskSearchForm input[type=hidden][name=r]').remove();
        //alert($('#inventorySearchForm').serialize());
        url = url + "&" + $('#taskSearchForm').serialize();
        rparent.html(rself);
        window.location.href = url;
    });

    $.fn.yiiGridView.defaults.afterAjaxUpdate = function(){
        //$("a[name^='ioaccept'],a[name^='ioreason'],a[name^='iodeny']").each(function() {
        $("a[name^='ioaccept'],a[name^='iodeny']").each(function() {
            $(this).unbind('click').click(function(){
                if(!confirm('Are you sure you want to '+$(this).attr("title")+'?')) return false;
                //###!!!move the notebox back to the hidden container. incase it was removed when the user deny/accept.!!
                $("#noteboxdiv").hide();
                $("#noteboxdiv").appendTo($("#hiddenContainer"));

                var newhref = $(this).attr('href');
                var thishref = $(this);
                $.ajax({
                    'type': 'GET',
                    'dataType': 'json',
                    'url': newhref,
                    'success':function(data){
                        //donothing for now;
                        if (data.success){
                            //alert(data.msg);
                            //thishref.remove();
                            $.fn.yiiGridView.update('task-grid');
                        } else {
                            alert(data.msg);
                        }
                    },
                    'complete':function(XHR,TS){XHR = null;}
                });
                //$(this).attr('href',"javascript:void(0);");

                return false;
            });
        });


        $("input[name^='publication_pending']").each(function() {
            $(this).unbind('click').click(function(){
                var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
                var gvoffset = $(this).parent().parent().prevAll().length;
                var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
                var thistd = $(this);

                $.ajax({
                    'type': 'GET',
                    'dataType': 'json',
                    'url': "<?php echo CHtml::normalizeUrl(array('/task/setattr'));?>",
                    'data': 'id='+currenttrid+"&attrname="+$(this).attr('name')+"&attrvalue="+this.value,
                    'success':function(data){
                        //donothing for now;
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

        var __desired_domains = [];
        $("input[name^='desired_domain']").each(function(){
            __desired_domains.push(this.value);
        });

        $("input[name^='desired_domain'],input[name^='sourceurl'],input[name^='rewritten_title']").each(function() {

            $(this).unbind('blur').blur(function(){
                var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
                var gvoffset = $(this).parent().parent().prevAll().length;
                var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
                var thistd = $(this);

                //##########turn it off, remove follwing: start###############//
                if ($(thistd).attr('name') == "desired_domain[]"){
                    this.value = stripURL2Domain(this.value);
                    var pressdel = false;

                    if (this.value == "" && __desired_domains[gvoffset] != ""){
                        //alert(pressdel);
                        if(!confirm("Are you sure you want to remove this")) {
                            this.value = __desired_domains[gvoffset];
                            //alert(gvoffset);
                            $(thistd).css("background-color","#66ff00");
                            return false;
                        }
                    }

                    /*
                    $(thistd).keydown(function(e){
                        if (e.keyCode == 46 || e.keyCode == 8){
                            pressdel = true;
                        } else {
                            pressdel = false;
                        }
                    });
                    if (pressdel && this.value == ""){
                        alert(pressdel);
                        if(!confirm("Are you sure you want to remove this")) return false;
                    }
                    */

                    if (this.value && !isValidDomain(this.value)){
                        //alert(this.value);
                        $(thistd).css("background-color","red");
                        alert("Please enter a valid domain here.");
                        this.value = __desired_domains[gvoffset];
                        return ;
                    }
                }

                //##########turn it off, remove above: end###############//
                /*
                var _attrvalue = this.value;
                if ($(thistd).attr('name') == "rewritten_title[]") {
                    _attrvalue = encodeURI(this.value);
                }
                */
                var _attrvalue = encodeURIComponent(this.value);
                //I will reference this article for the url encode:http://unixpapa.com/js/querystring.html

                $.ajax({
                    'type': 'GET',
                    'dataType': 'json',
                    'url': "<?php echo CHtml::normalizeUrl(array('/task/setattr'));?>",
                    'data': 'id='+currenttrid+"&attrname="+$(this).attr('name')+"&attrvalue="+_attrvalue,
                    'success':function(data){
                        //donothing for now;
                        if ($(thistd).attr('name') == "desired_domain[]"){
                            __desired_domains[gvoffset] = data.desired_domain;
                            //alert(data.desired_domain);
                            //alert(__desired_domains);
                        }
                        if (data.success){
                            $(thistd).css("background-color","yellow");
                            if ($(thistd).attr('name') == "sourceurl[]" && data.desired_domain){
                                var ddelem = $(thistd).parent().parent().find('input[name^="desired_domain"]');
                                var _data_dd = data.desired_domain;
                                if (ddelem.size()){
                                    if (!ddelem.val()) {
                                        ddelem.val(_data_dd);
                                        ddelem.blur();
                                        /*
                                        ddelem.focus();
                                        $(thistd).focus();
                                        //then same as ddelem.blur();
                                        */
                                    } else {
                                        if (ddelem.val() != _data_dd)
                                            alert("The old desired domain not equal the domain of post url, is that ok?");
                                    }
                                } else {
                                    // if we choose the order of the columns, we need change here also
                                    $(thistd).parent().prev().prev().html(_data_dd);
                                    $.ajax({
                                        'type': 'GET',
                                        'dataType': 'json',
                                        'url': "<?php echo Yii::app()->createUrl('/task/setattr');?>",
                                        'data': 'id='+currenttrid+"&attrname=desired_domain[]&attrvalue="+_data_dd,
                                        'success':function(data){
                                        },
                                        'complete':function(XHR,TS){XHR = null;}
                                    });
                                }
                            }
                        } else {
                            //####################################
                            /*
                            if ($(thistd).attr('name') == "sourceurl[]"){
                            }
                            */
                            //####################################

                            $(thistd).css("background-color","red");
                            alert(data.msg);
                        }
                    },
                    'complete':function(XHR,TS){XHR = null;}
                });
            });

        });

        $("input[name^='livedate']").each(function() {
            $(this).datepicker({ dateFormat: "yy-mm-dd" });

            $(this).keydown(function(e){
                if (e.keyCode == 46 || e.keyCode == 8) {
                    //Delete and backspace clear text 
                    $(this).val(null); //Clear text
                    $(this).datepicker("hide"); //Hide the datepicker calendar if displayed
                    //$(this).blur(); //aka "unfocus"
                    $(this).change(); //aka "unfocus"
                }

                //Prevent user from manually entering in a date - have to use the datepicker box
                e.preventDefault();
            });

            $(this).unbind('click').change(function(){
                var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
                var gvoffset = $(this).parent().parent().prevAll().length;
                var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
                var thistd = $(this);

                $.ajax({
                    'type': 'GET',
                    'dataType': 'json',
                    'url': "<?php echo CHtml::normalizeUrl(array('/task/setattr'));?>",
                    'data': 'id='+currenttrid+"&attrname="+$(this).attr('name')+"&attrvalue="+this.value,
                    'success':function(data){
                        //donothing for now;
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

function stripURL2Domain(url){
    url = url.replace(/^(www\.)/i, "");
    url = url.replace(/^(ht|f)tp(s?)\:\/\/(www\.){0,1}/i, "");
    url = url.replace(/(\/[\s\S]*)/i, "");

    return url;
}

function isValidDomain(domain){
    var rx = /^([a-z0-9]([\-a-z0-9]*[a-z0-9])?\.)+((a[cdefgilmnoqrstuwxz]|aero|arpa)|(b[abdefghijmnorstvwyz]|biz)|(c[acdfghiklmnorsuvxyz]|cat|com|coop)|d[ejkmoz]|(e[ceghrstu]|edu)|f[ijkmor]|(g[abdefghilmnpqrstuwy]|gov)|h[kmnrtu]|(i[delmnoqrst]|info|int)|(j[emop]|jobs)|k[eghimnprwyz]|l[abcikrstuvy]|(m[acdghklmnopqrstuvwxyz]|mil|mobi|museum)|(n[acefgilopruz]|name|net)|(om|org)|(p[aefghklmnrstwy]|pro)|qa|r[eouw]|s[abcdeghijklmnortvyz]|(t[cdfghjklmnoprtvwz]|travel)|u[agkmsyz]|v[aceginu]|w[fs]|y[etu]|z[amw])$/i;

    return rx.test(domain) ? true : false;
}

</script>