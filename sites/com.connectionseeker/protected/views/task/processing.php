<?php
$this->breadcrumbs=array(
	'Campaign Task'=>array('processing'),
	'Processing',
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

$taskstatus = Task::$status;
$taskstatusstr =  Utils::array2String($taskstatus);

$iostatuses = Task::$iostatuses;
$iostatusesstr =  Utils::array2String($iostatuses);

$carts = Cart::model()->findAllByAttributes(array('client_domain_id'=>$cmpmodel->domain_id));
$cartdomains = CHtml::listData($carts, 'domain_id', 'domain');
$cartdomainstatus = CHtml::listData($carts, 'domain_id', 'status');

/*
function add2prefix(&$av, $ak, $prefix) {
    $av = "$prefix:$av";
}
//array_flip(array_walk(array_flip($channels), 'add2prefix', "C"));
*/
$precartdomains = array();
if ($cartdomains) {
    $_dstatus = Cart::$dstatus;
    foreach($cartdomains as $ck => $cv) {
        $_cds = $cartdomainstatus[$ck];
        if ($_cds == 0) {
            $precartdomains["D:".$ck] = $cv;
        } else {
            $precartdomains["D:".$ck] = $cv . " (" . $_dstatus[$_cds] . ")";
            //$cartdomainstatus[$ck] = $_dstatus[$_cds];
        }
    }
}
//print_r($cartdomainstatus);

$prechannels = array();
if ($channels) {
    foreach($channels as $ck => $cv) {
        $prechannels["C:".$ck] = $cv;
    }
}

$desiredarr = array();
$desiredarr[''] = " ----------------- ";
$desiredarr['Channels'] = $prechannels;
$desiredarr['Carts'] = $precartdomains;

$desiredstr =  Utils::array2String($desiredarr);
//print_r($desiredarr);
//echo $desiredstr;
//die();


//if (isset($_REQUEST['dpm']) && isset($roles['Admin'])) {
if (isset($roles['Admin'])) {
    $dpm = isset($_REQUEST['dpm']) ? $_REQUEST['dpm'] : 5;
    $dparr = Utils::taskDisplayMode($dpm);
    $isadmin = true;
} else {
    $dparr = Utils::taskDisplayMode();
    $isadmin = false;
}

$_themebaseurl = Yii::app()->theme->baseUrl;
$rebuildimg = $_themebaseurl."/css/gridview/star-on.png";
if (isset($roles['Admin'])) {
?>
<div id="innermenu">
    <?php $this->renderPartial('_menu'); ?>
</div>
<?php }?>

<h2>Campaign Tasks (<?php echo $cmpmodel->name; ?>)</h2>
<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>
<div id="processing" style="float:left;width:220px;">&nbsp;</div>
<br />

<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'task-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
    'selectableRows' => '2',
    //'selectableRows' => '0',
	'columns'=>array(
        array(
            'id'=>'ids',
            'class'=>'application.extensions.lkgrid.LinkmeCheckBoxColumn',
            'displayRow'=>'($data->tasktype == 1 && ($data->rcontent->length || !$data->content_article_id))',
            'expressCBHtmlOptions'=>array(
                //'usage'=>$this->evaluateExpression('$data->content_article_id ? "download" : "send"',array('data'=>$data)),
                'usage'=>'$data->content_article_id ? "download" : "send"',
            ),
        ),
        array(
            'name' => 'id',
            'header' => 'RB/ID',
            'type' => 'html',
            'value'=> '($data->iostatus==5&&$data->rebuild&&'.isset($roles['Admin']).')?"$data->id - " . CHtml::link(CHtml::image("'.$rebuildimg.'"), "#"):"$data->id"',
            'htmlOptions'=>array('nowrap'=>'nowrap',),
            //'value'=> '($data->iostatus==5&&$data->rebuild)?CHtml::link(CHtml::image("'.$rebuildimg.'"), "#"):"$data->id"',
            //'visible' => isset($roles['Admin']),
            //'filter' => false,
        ),
        array(
            'name' => 'iostatus',
            'type' => 'raw',
            'value' => '$data->iostatus ? Utils::getValue(' . $iostatusesstr . ', $data->iostatus) : ""',
            'filter' => $iostatuses,
        ),
        array(
            'header' => 'Content IO',
            'name' => 'content_step',
            'type' => 'raw',
            'value' => '$data->content_step ? "Yes" : "No"',
            //'value' => '$data->content_step ? $data->content_step : ""',
        ),
        array(
            'name' => 'tasktype',
            'type' => 'raw',
            'value' => 'CHtml::dropDownList("tasktype[]", $data->tasktype, '.$tasktypestr.')',
            'filter' => $linktask,
            'visible' => isVisible('tasktype', $dparr),
        ),

        array(
            'name' => 'tierlevel',
            'type' => 'raw',
            'value' => 'Utils::getValue(' . $tierstr . ', $data->tierlevel)',
            //'value' => ($isadmin) ? 'CHtml::dropDownList("tierlevel[]", $data->tierlevel, '.$tierstr.')' : 'Utils::getValue(' . $tierstr . ', $data->tierlevel)',
            'filter' => $tiers,
            'visible' => isVisible('tierlevel', $dparr),
        ),
        array(
            'name' => 'anchortext',
            'type' => 'raw',
            'value' => 'CHtml::textField("anchortext[]", $data->anchortext)',
            'visible' => isVisible('anchortext', $dparr),
        ),
        array(
            'name' => 'targeturl',
            'visible' => isVisible('targeturl', $dparr),
        ),
        //Here you need pay attention to ($data->desired_domain_id ? "D:".$data->desired_domain_id : "C:".$data->channel_id)
        array(
            'name' => 'desired_domain_id',
            'type' => 'raw',
            'value' => 'CHtml::dropDownList("desired_domain_id[]", $data->desired_domain_id ? "D:".$data->desired_domain_id : "C:".$data->channel_id, '.$desiredstr.')',
            'filter' => $desiredarr,
            'visible' => isVisible('desired_domain_id', $dparr),
        ),
        array(
            'name' => 'channel_id',
            'type' => 'raw',
            'value' => 'Utils::getValue(' . $channelstr . ', $data->channel_id)',
            'filter' => $channels,
            'visible' => isVisible('channel_id', $dparr),
        ),
        array(
            'name' => 'rewritten_title',
            'type' => 'raw',
            'value' => 'CHtml::textField("rewritten_title[]", $data->rewritten_title)',
            'visible' => isVisible('rewritten_title', $dparr),
        ),
        array(
            'name' => 'blog_title',
            'type' => 'raw',
            'value' => 'CHtml::textField("blog_title[]", $data->blog_title)',
            'visible' => isVisible('blog_title', $dparr),
        ),
        array(
            'name' => 'blog_url',
            'type' => 'raw',
            'value' => 'CHtml::textField("blog_url[]", $data->blog_url)',
            'visible' => isVisible('blog_url', $dparr),
        ),
        array(
            'name' => 'notes',
            'type' => 'raw',
            'value' => 'CHtml::textArea("notes[]", $data->notes)',
            'visible' => isVisible('notes', $dparr),
        ),
        array(
            'name' => 'qa_comments',
            'type' => 'raw',
            'value' => 'CHtml::textArea("qa_comments[]", $data->qa_comments)',
            'visible' => isVisible('qa_comments', $dparr),
        ),
        array(
            'name' => 'livedate',
            'type' => 'raw',
            'value' => 'CHtml::textField("livedate[]", $data->livedate, array("id"=>"livedate_".$data->id, "readOnly"=>"readOnly"))',
            'visible' => isVisible('livedate', $dparr),
        ),
        array(
            'name' => 'tierlevel_built',
            'type' => 'raw',
            'value' => ($isadmin) ? 'CHtml::dropDownList("tierlevel_built[]", $data->tierlevel_built, '.$tierstr.')' : 'Utils::getValue(' . $tierstr . ', $data->tierlevel_built)',
            'filter' => $tiers,
            'visible' => isVisible('tierlevel_built', $dparr),
        ),
        array(
            'name' => 'sourceurl',
            'type' => 'raw',
            'value' => 'CHtml::textField("sourceurl[]", $data->sourceurl)',
            'visible' => isVisible('sourceurl', $dparr),
        ),
        array(
            'name' => 'spent',
            'type' => 'raw',
            'value' => '"$".CHtml::textField("spent[]", $data->spent, array("style"=>"width:38px;"))',
            'visible' => isVisible('spent', $dparr) && isset($roles['Admin']),
            'htmlOptions'=>array('nowrap'=>'nowrap'),
        ),
        array(
            'name' => 'target_stype',
            'type' => 'raw',
            'value' => 'CHtml::textField("target_stype[]", $data->target_stype)',
            'visible' => isVisible('target_stype', $dparr),
        ),
        array(
            'name' => 'rsummary.googlepr',
            'type' => 'raw',
            'value' => '$data->desired_domain_id ? CHtml::textField("googlepr[]", $data->rsummary->googlepr): ""',
            'visible' => isVisible('googlepr', $dparr),
        ),
        array(
            'name' => 'rsummary.mozrank',
            'type' => 'raw',
            'value' => '$data->desired_domain_id ? CHtml::textField("mozrank[]", $data->rsummary->mozrank) : ""',
            'visible' => isVisible('mozrank', $dparr),
        ),
        array(
            'name' => 'rsummary.alexarank',
            'type' => 'raw',
            'value' => '$data->desired_domain_id ? CHtml::textField("alexarank[]", $data->rsummary->alexarank): ""',
            'visible' => isVisible('alexarank', $dparr),
        ),
        /*
        'rsummary.googlepr',
        'rsummary.alexarank',
        'rsummary.mozrank',
        */
        array(
            'name' => 'client_comments',
            'type' => 'raw',
            'value' => 'CHtml::textArea("client_comments[]", $data->client_comments)',
            'visible' => isVisible('client_comments', $dparr),
        ),
        array(
            'name' => 'other',
            'type' => 'raw',
            'value' => 'CHtml::textField("other[]", $data->other)',
            'visible' => isVisible('other', $dparr),
        ),
        array(
            'name' => 'content_article_id',
            'visible' => isVisible('content_article_id', $dparr),
        ),
        array(
            'name' => 'taskstatus',
            'type' => 'raw',
            'value' => 'Utils::getValue(' . $taskstatusstr . ', $data->taskstatus)',
            'filter' => $taskstatus,
            'visible' => isVisible('taskstatus', $dparr),
        ),
        array(
            'name' => 'duedate',
            'type' => 'raw',
            //'value' => '$data->duedate ? date("M/d/Y",$data->duedate) : ""',
            'value' => 'CHtml::textField("duedate[]", $data->duedate ? date("Y-m-d",strtotime($data->duedate)) : "", array("id"=>"duedate_".$data->id, "readOnly"=>"readOnly"))',
            'visible' => isVisible('duedate', $dparr),
        ),
        array(
            'name' => 'always_on_cio',
            'header' => 'Content IO',
            'type' => 'raw',
            //##'value' => 'CHtml::checkBox("always_on_cio[]", $data->always_on_cio)',
            'value' => '($data->rcampaign->always_on_cio) ? "Yes" : CHtml::checkBox("siteonly[]", $data->siteonly)',
            //##'visible' => isVisible('always_on_cio', $dparr),
        ),
		/*
        'id',
		'content_article_id',
		'anchortext',
		'sourceurl',
		'targeturl',
        'channel_id',
        'rewritten_title',
        'desired_domain',
        'desired_domain_id',
        array(
            'name' => 'assignee',
            'type' => 'raw',
            'value' => 'CHtml::encode($data->rassignee->username)',
            'filter' => CHtml::listData(User::model()->findAll(),'id','username'),
        ),
        'blog_title',
        'blog_url',
        'qa_comments',
		'campaign_id',
		'tasktype',
		'duedate',
		'optional_keywords',
		'domain_id',
		'inventory_id',
		'sourceurl',
		'sourcedomain',
		'title',
		'taskstatus',
		'mapping_id',
		'notes',
		'content_campaign_id',
		'content_category_id',
		'send2cpdate',
		'checkouted',
		'created',
		'created_by',
		'modified',
		'modified_by',
		*/
		array(
			'class'=>'CButtonColumn',
            'template'=>'{ioready} {view} {dlhtml} {dltxt} {note} {update} {delete}',// {send2cp}
            'buttons' => array(
                /*
                'send2cp' => array(
                    'label' => 'Send to Copypress',
                    'visible' => '($data->tasktype == 1 && !$data->content_article_id)',
                    'imageUrl'=>$_themebaseurl.'/css/gridview/send2cp.png',
                    'url' => 'Yii::app()->createUrl("task/send", array("id"=>$data->id))',
                    'click' => "function(){
                        //alert($(this).parent().parent().children(':nth-child(1)').children().val());
                        sendtask2cp('send', $(this).parent().parent().children(':nth-child(1)').children().val());
                        return false;
                    }",
                    'options' => array(
                        'class'=>'send2cp',
                    ),
                ),
                */
                'ioready' => array(
                    'label' => 'Sent task to IO',
                    'visible' => '($data->iostatus == 0 || $data->iostatus == 4)',
                    'imageUrl'=>$_themebaseurl.'/css/gridview/ioready.png',
                    'url' => 'Yii::app()->createUrl("task/setattr", array("id"=>$data->id,"attrname"=>"iostatus","attrvalue"=>1))',
                    'options' => array(
                        'name'=>'ioready',
                    ),
                ),

                'dlhtml' => array(
                    'label' => 'Download article as html format',
                    'visible' => '($data->tasktype == 1 && $data->rcontent->length)',
                    'imageUrl'=>$_themebaseurl.'/css/gridview/dlhtml.png',
                    'url' => 'Yii::app()->createUrl("task/download", array("id"=>$data->id))',
                    'options' => array(
                        'class'=>'dlhtml',
                    ),
                ),
                'dltxt' => array(
                    'label' => 'Download article as txt format',
                    'visible' => '($data->tasktype == 1 && $data->rcontent->length)',
                    'imageUrl'=>$_themebaseurl.'/css/gridview/dltxt.png',
                    'url' => 'Yii::app()->createUrl("task/download", array("id"=>$data->id))',
                    'options' => array(
                        'class'=>'dltxt',
                    ),
                ),
                'note' => array(
                    'label' => 'Add Notes',
                    'imageUrl' => $_themebaseurl.'/css/gridview/note.png',
                    'url' => 'Yii::app()->createUrl("task/view", array("id"=>$data->id))',
                    'options' => array('id'=>$this->evaluateExpression('$data->id', array('data'=>$data))),
                    'click' => "function(){
                        addNote(this);
                        return false;
                    }",
                ),
                'view' => array(
                    'imageUrl'=>$_themebaseurl.'/css/gridview/viewdetail.png',
                ),
                'update' => array(
                    'imageUrl'=>$_themebaseurl.'/css/gridview/edit.png',
                ),
                'delete' => array(
                    'visible' => '!($data->rcontent->length)',
                    'imageUrl'=>$_themebaseurl.'/css/gridview/del.png',
                    'click'=>"function() {
	if(!confirm('Are you sure you want to delete this item?')) return false;
	var th=this;
	var afterDelete=function(){};
	$.fn.yiiGridView.update('task-grid', {
		type:'POST',
		url:$(this).attr('href'),
		success:function(data) {
            if (data) {alert(data);}
			$.fn.yiiGridView.update('task-grid');
			afterDelete(th,true,data);
		},
		error:function(XHR) {
			return afterDelete(th,false,XHR);
		}
	});
	return false;
}",
                ),
            ),
            'htmlOptions'=>array('nowrap'=>'nowrap'),
		),
	),
)); ?>

<div class="clear"></div>

<?php
if ($displaymode == 0 || $displaymode == 2) {
    $this->renderPartial('_send2copypress',array(
        'model'=>$model,
        'channels'=>$channels,
    ));
}
?>

<div id="hiddenContainer">
    <div id="noteboxdiv" style="display:none;">
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {

    $('#processing').bind("ajaxSend", function() {
        $(this).html("&nbsp;");
        $(this).css('background-image', 'url(' + "<?php echo $_themebaseurl; ?>" + '/images/loading.gif)');
    }).bind("ajaxComplete", function() {
        $(this).css('background-image', '');
        $(this).css('color', 'red');
    });

    var _dmstatus = <?php echo CJSON::encode($cartdomainstatus)?>;
    var _chlfilterexist = false;
    if ($("#task-grid > table.items > thead > tr.filters select[name*='channel_id']").length){
        _chlfilterexist = true;
    }


    $.fn.yiiGridView.defaults.afterAjaxUpdate = function(){
        var taskchlobj = $("select[name='Task\[channel_id\]']");

        //jquery onchange will firing event twice in ie7, So we need hack it as following.
        $("select[name^='tasktype'],select[name^='tierlevel'],select[name^='tierlevel_built'],select[name^='desired_domain_id'],input[name^='livedate'],input[name^='duedate'],input[name^='always_on_cio']").each(function() {
            //way 1
            //$(this).change(updateType);
            //$(this).unbind('click').change(updateType);

            //desired domain status plugin!!
            if ($(this).attr('name') == "desired_domain_id[]"){
                var _prev_desired = this.value;
                //hidden the TEXT "Pending/In Use" of selectedIndex from the current text
                var _prev_desired_text = this.options[this.selectedIndex].text;
                _prev_desired_text = _prev_desired_text.replace(/ \(Pending\)/, "");
                this.options[this.selectedIndex].text = _prev_desired_text.replace(/ \(In Use\)/, "");

                /*
                $.each(this.options,function(n,optobj) {  
                    //do something here  
                    //alert(n+' '+optobj.text);
                });
                */

                //Remove/Hide sites with (IN USE) for dropdown.
                $(this).find("optgroup[label=Carts] option").each(function(){
                //$(this).find("optgroup[label=Channels] option").each(function(){
                    //alert($(this).val());
                    var tmptext = $(this).text();
                    if(tmptext.indexOf("(In Use)") != -1){
                        $(this).remove();
                    }
                });
            }

            //way 2
            $(this).unbind('click').change(function(){
                var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
                var gvoffset = $(this).parent().parent().prevAll().length;
                var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
                var thistd = $(this);

                //desired domain status plugin!!
                if ($(this).attr('name') == "desired_domain_id[]"){
                    if (_prev_desired.indexOf("D:") != -1) {
                        var _pdid = _prev_desired.replace(/D:/, "");
                        //_dmstatus[_pdid] = ;
                    } else {
                    }
                    if (this.value.indexOf("D:") != -1) {
                        var _currid = this.value.replace(/D:/, "");
                        if (_dmstatus[_currid] == 0){
                            _dmstatus[_currid] = 1;//"Pending"
                        } else {
                            alert("The domain: "+ this.options[this.selectedIndex].text + " already in use");
                        }
                    } else {
                        //do nothing for now;
                    }
                    //alert(_prev_desired);
                    //alert(this.value);
                    //reset the previous value;
                    _prev_desired = this.value;
                    _prev_desired_text = this.options[this.selectedIndex].text;
                } else if ($(this).attr('name') == "always_on_cio[]") {
                    this.value = $(this).is(':checked') ? 1 : 0;
                }

                $.ajax({
                    'type': 'GET',
                    'dataType': 'json',
                    'url': "<?php echo CHtml::normalizeUrl(array('/task/setattr'));?>",
                    'data': 'id='+currenttrid+"&attrname="+$(this).attr('name')+"&attrvalue="+this.value,
                    'success':function(data){
                        //donothing for now;
                        if (data.success){
                            $(thistd).css("background-color","yellow");
                            //if (data.channel_id){
                            if (data.channel_id && _chlfilterexist){
                                //var chl = $("select[name='Task[channel_id]'] option[value='"+data.channel_id+"']").text();
                                var chl = $("select[name='Task\[channel_id\]']").children("option\[value='"+data.channel_id+"'\]").text();
                                $(thistd).parent().next().html(chl);
                                if (data.chlstr){
                                    $(thistd).parent().next().html("<select id='itemchl"+currenttrid+"' name='itemchl[]'></select>");
                                    $.each(data.chlstr, function(i,v){
                                        //alert(v);
                                        //taskchlobj.children("option\[value='"+v+"'\]").text();
                                        $("#itemchl"+currenttrid)
                                          .append($('<option>', { value : v })
                                          .text(taskchlobj.children("option\[value='"+v+"'\]").text()));
                                    });

                                    $("#itemchl"+currenttrid).unbind('click').change(function(){
                                        $.ajax({
                                            'type': 'GET',
                                            'dataType': 'json',
                                            'url': "<?php echo Yii::app()->createUrl('/task/setattr');?>",
                                            'data': 'id='+currenttrid+"&attrname=channel_id&attrvalue="+this.value,
                                            'success':function(data){
                                                if (data.success) {
                                                    $("#itemchl"+currenttrid).css("background-color","yellow");
                                                } else {
                                                    $("#itemchl"+currenttrid).css("background-color","red");
                                                    alert(data.msg);
                                                }
                                            },
                                            'complete':function(XHR,TS){XHR = null;}
                                        });
                                    });
                                }
                            }
                        } else {
                            $(thistd).css("background-color","red");
                            alert(data.msg);
                        }
                    }
                });
            });

        });

        $("input[name^='rewritten_title'],input[name^='sourceurl'],input[name^='blog_title'],input[name^='blog_url'],input[name^='blog_url'],input[name^='target_stype'],textarea[name^='qa_comments'],textarea[name^='notes'],textarea[name^='client_comments'],input[name^='other'],input[name^='anchortext'],input[name^='spent']").each(function() {

            $(this).unbind('blur').blur(function(){
                var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
                var gvoffset = $(this).parent().parent().prevAll().length;
                var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
                var thistd = $(this);

                /*
                var _attrvalue = this.value;
                if ($(thistd).attr('name') == "rewritten_title[]" || $(thistd).attr('name') == "blog_title[]"
                  || $(thistd).attr('name') == "qa_comments[]" || $(thistd).attr('name') == "notes[]"
                  || $(thistd).attr('name') == "client_comments[]" || $(thistd).attr('name') == "sourceurl[]") {
                    _attrvalue = escape(this.value);
                }
                */

                //###!! http://unixpapa.com/js/querystring.html
                //###!! http://en.wikipedia.org/wiki/Percent-encoding
                //###!! http://xkr.us/articles/javascript/encode-compare/
                //var _attrvalue = escape(this.value.replace(/\+/g, '%20'));
                var _attrvalue = encodeURIComponent(this.value);
                var thisattrname = $(this).attr('name');

                $.ajax({
                    'type': 'GET',
                    'dataType': 'json',
                    'url': "<?php echo CHtml::normalizeUrl(array('/task/setattr'));?>",
                    'data': 'id='+currenttrid+"&attrname="+thisattrname+"&attrvalue="+_attrvalue,
                    'success':function(data){
                        //donothing for now;
                        if (data.success){
                            $(thistd).css("background-color","yellow");
                        } else {
                            $(thistd).css("background-color","red");
                            alert(data.msg);
                            /*
                            if (data.forcechange) {
                                var _confirmation = confirm(data.msg);
                                if (_confirmation) {
                                    $.ajax({
                                        'type': 'GET',
                                        'dataType': 'json',
                                        'url': "<?php echo CHtml::normalizeUrl(array('/task/setattr'));?>",
                                        'data': 'id='+currenttrid+"&attrname="+thisattrname+"&attrvalue="+_attrvalue+"&forcechange=1",
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
                                }
                            } else {
                                alert(data.msg);
                            }
                            */
                        }
                    },
                    'complete':function(XHR,TS){XHR = null;}
                });
            });

        });

        $("input[name^='googlepr'],input[name^='mozrank'],input[name^='alexarank']").each(function() {
            $(this).unbind('blur').blur(function(){
                var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
                var gvoffset = $(this).parent().parent().prevAll().length;
                var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
                var thistd = $(this);
                var currentdomainid = $(this).parent().parent().find('select[name^="desired_domain_id"]').val();

                if (currentdomainid)
                {
                    currentdomainid = currentdomainid.replace(/D:/, "");
                    //alert(currentdomainid);
                    $.ajax({
                        'type': 'GET',
                        'dataType': 'json',
                        'url': "<?php echo CHtml::normalizeUrl(array('/domain/setattr'));?>",
                        'data': 'id='+currentdomainid+"&attrname="+$(this).attr('name')+"&attrvalue="+this.value,
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
                }

            });
        });

        $("a[name^='ioready']").each(function() {
            $(this).unbind('click').click(function(){
                if(!confirm('Are you sure you want to send this task to IO?')) return false;
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
                            thishref.remove();
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


        $("input[name^='livedate'],input[name^='duedate']").each(function() {
            $(this).datepicker({ dateFormat: "yy-mm-dd" });
            /*
            $(this).keydown(function (e){
                switch(e.keyCode) { 
                    case 46:  // delete
                    case 8:  // backspace
                        $(this).val(''); //Clear text
                        $(this).datepicker("hide"); //Hide the datepicker calendar if displayed
                        //$(this).blur();
                        $(this).change();
                        break;
                    default:
                        e.preventDefault();
                        break;
                }
            });
            */

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


        });
        //issue: when the user click the pager(i mean change pager number), then the mailbox & notebox may not showing again.
        //cause we append the mailbox & notebox into the grid already when we click the addmail or addnote button,
        //so when we jump to another outreach page number, the mailbox & notebox may be removed due to the grid overwrited.
        /*
        $("div.pager > ul > li a").each(function(){
            $(this).click(function() {
                $("#mailboxdiv").hide();
                $("#noteboxdiv").hide();
                $("#mailboxdiv").appendTo($("#hiddenContainer"));
                $("#noteboxdiv").appendTo($("#hiddenContainer"));

            });
            return true;
        });
        */

        var _dmids = [];
        var _chltdobjs = [];
        var _trids = [];

        //If the channel column is there, then we will get the channel automatically.
        //if ($("#task-grid > table.items > thead > tr.filters select[name*='channel_id']").length){
        if (_chlfilterexist){
            $("select[name^='desired_domain_id']").each(function(){
                var chltd = $(this).parent().next();
                var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
                var gvoffset = $(this).parent().parent().prevAll().length;
                var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);

                if (this.value.indexOf("D:") != -1) {
                    var _domainid = this.value.replace(/D:/, "");
                    _dmids.push(_domainid);
                    _chltdobjs.push(chltd);
                    _trids.push(currenttrid);
                    //alert(currentdomainid);
                }
            });
        }

        if (_dmids.length > 0){
            /*
            $.each(_dmids, function(i,v){
                //alert(_dmids[i]);
                _chltdobjs[i].css("background-color","red");
            });
            */
            
            $.ajax({
                'type': 'GET',
                'dataType': 'json',
                'url': "<?php echo Yii::app()->createUrl('/inventory/getattr');?>",
                'data': {'ids[]': _dmids, 'attrs': "channel_id", 'byattr':"domain_id"},
                'success':function(data){
                    //donothing for now;
                    if (data.success){
                        //alert(data.msg);
                        $.each(data.rs, function(i, v){
                            //alert(v.idx);
                            if (v.channel_id.length <=1){
                                return ;
                            }
                            var __trid = _trids[i];
                            var _beforechl = _chltdobjs[i].html();
                            _beforechl = $.trim(_beforechl);
                            _beforechl = _beforechl.replace(/&nbsp;$/, "");
                            //_beforechl = _beforechl.replace(/\\xa0$/, "");you can use this way to replace the last "$nbsp;" also
                            //alert(_beforechl);
                            _chltdobjs[i].html("<select id='itemchl"+__trid+"' name='itemchl[]'></select>");
                            $.each(v.channel_id, function(i,v){
                                $("#itemchl"+__trid)
                                  .append($('<option>', { value : v })
                                  .text(taskchlobj.children("option\[value='"+v+"'\]").text()));
                            });
                            //there is no attr named text for select.option, so i have to iterate all of the options
                            //$("#itemchl"+__trid+" option[text='"+_beforechl+"']").attr("selected", true);
                            $("#itemchl"+__trid).find("option").each(function(){
                                if ($(this).text() == _beforechl) {
                                    $(this).attr('selected','selected');
                                    return false;
                                }
                            });


                            $("#itemchl"+__trid).unbind('click').change(function(){
                                $.ajax({
                                    'type': 'GET',
                                    'dataType': 'json',
                                    'url': "<?php echo Yii::app()->createUrl('/task/setattr');?>",
                                    'data': 'id='+__trid+"&attrname=channel_id&attrvalue="+this.value,
                                    'success':function(data){
                                        if (data.success) {
                                            $("#itemchl"+__trid).css("background-color","yellow");
                                        } else {
                                            $("#itemchl"+__trid).css("background-color","red");
                                            alert(data.msg);
                                        }
                                    }
                                });
                            });
                        });
                    } else {
                        //alert(data.msg);
                        alert("Error, Please contact system admin");
                    }
                },
                'complete':function(XHR,TS){XHR = null;}
            });
        }
        //alert(_dmids.length);

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
                            $("#etr" +v+" > td:last > a.note > img").attr("src", "<?php echo $_themebaseurl?>/css/gridview/notes.png");
                        });
                    }
                }
            },
            'complete':function(XHR,TS){XHR = null;}
        });
        //------------------------------------------------//
    }

    $.fn.yiiGridView.defaults.afterAjaxUpdate();

//    $.each(_dmstatus, function(i, v){
//        alert(i + "-" + v);
//    });

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