<?php
$this->breadcrumbs=array(
	'Emails'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('email-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

$events = array('processed','deferred','delivered','open','click','bounce','dropped','spamreport','unsubscribe');
$events = array_combine($events, $events);

$fttemplates = CHtml::listData(Template::model()->findAll(),'id','name');
$ftfromes = CHtml::listData(Mailer::model()->findAll(),'id','user_alias');
$ftusers = CHtml::listData(User::model()->findAll(),'id','username');
?>

<h1>Manage Emails</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<!-- search-form -->
<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
	'fttemplates'=>$fttemplates,
	'ftfromes'=>$ftfromes,
	'ftusers'=>$ftusers,
	'events'=>$events,
)); ?>
</div><!-- search-form -->

<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'email-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
        array(
            'name' => 'domain_id',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->rdomain->domain), array("domain/view", "id" =>$data->domain_id))',
        ),
		'subject',
        array(
            'name' => 'template_id',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->rtemplate->name), array("template/view", "id" =>$data->template_id))',
            'filter' => $fttemplates,
        ),
        array(
            'name' => 'from',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->rmailer->user_alias), array("mailer/view", "id" =>$data->from))',
            'filter' => $ftfromes,
        ),
		'to',
		'send_time',
        array(
            'name' => 'opened',
            'type' => 'raw',
            'value' => '$data->opened ? date("Y-m-d H:i:s", $data->opened ) : ""',
        ),
		'replied_time',
		//'is_reply',
		'created',
        array(
            'name' => 'created_by',
            'type' => 'raw',
            'value' => 'CHtml::encode($data->rcreatedby->username)',
            'filter' => $ftusers,
        ),

		/*
        'reventone.event',
        array(
            'name' => 'revent.event',
            'type' => 'raw',
            'value' => 'Email::getEvents($data->revent)',
        ),
		'domain_id',
		'template_id',
		'from',
		'cc',
		'subject',
		'content',
		'status',
		'send_time',
		'created',
		'created_by',
		'modified',
		'modified_by',
		*/

        array(
            'class'=>'CButtonColumn',
            'class'=>'application.extensions.lkgrid.LinkmeButtonColumn',
            'evaluateFields'=>array('options'),
            'template'=>'{note} {plus} {view}{update}{delete}',
            'buttons' => array(
                'note' => array(
                    'label' => 'Add Notes',
                    'imageUrl' => Yii::app()->theme->baseUrl.'/css/gridview/note.png',
                    'url' => 'Yii::app()->createUrl("domain/view", array("id"=>$data->domain_id))',
                    'options' => array('domain_id'=>'$data->domain_id'),
                    'visible' => '$data->domain_id > 0 ? true : false',
                    'click' => "function(){
                        addNote(this);
                        return false;
                    }",
                ),
                'plus'=> array(
                    'imageUrl' => Yii::app()->theme->baseUrl.'/css/gridview/addnote.png',
                    'url' => 'Yii::app()->createUrl("email/group", array("id"=>$data->id))',
                    'click' => "function(){
                        triggerGroup(this);
                        return false;
                    }",
                ),
            ),
            'htmlOptions'=>array('nowrap'=>'nowrap'),
		),
	),
)); ?>

<div id="hiddenContainer">
    <div id="noteboxdiv" style="display:none;"></div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    $("#downloadEmails").click(function(){
        var url = "<?php echo Yii::app()->createUrl('/download/email');?>";
        var rparent = $('#emailSearchForm input[type=hidden][name=r]').parent();
        var rself = rparent.html();
        $('#emailSearchForm input[type=hidden][name=r]').remove();
        //alert($('#inventorySearchForm').serialize());
        url = url + "&" + $('#emailSearchForm').serialize();
        rparent.html(rself);
        window.location.href = url;
    });

    $.fn.yiiGridView.defaults.afterAjaxUpdate = function(){
        var _ids = [];
        $('#email-grid > div.keys > span').each(function(i){
            _ids[i] = $(this).html();
        });
        $("#email-grid > table.items > tbody > tr").each(function(i){
            $(this).attr("id", "etr"+_ids[i]);//reset table.tr.id
        });

        $.ajax({
            'type': 'POST',
            'dataType': 'json',
            'url': "<?php echo Yii::app()->createUrl('/note/icon');?>",
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


        //issue: when the user click the pager(i mean change pager number), then the mailbox & notebox may not showing again.
        //cause we append the mailbox & notebox into the grid already when we click the addmail or addnote button,
        //so when we jump to another outreach page number, the mailbox & notebox may be removed due to the grid overwrited.
        $("div.pager > ul > li a").each(function(){
            $(this).click(function() {
                $("#noteboxdiv").hide();
                $("#noteboxdiv").appendTo($("#hiddenContainer"));
            });
            return true;
        });

        $("table.items #pageSize").change(function(){
            $("#noteboxdiv").hide();
            $("#noteboxdiv").appendTo($("#hiddenContainer"));
        });

        $("table.items thead tr.filters input, table.items thead tr.filters select").each(function(){
            $(this).change(function() {
                $("#noteboxdiv").hide();
                $("#noteboxdiv").appendTo($("#hiddenContainer"));
            });
            return true;
        });
    }

    $.fn.yiiGridView.defaults.afterAjaxUpdate();
});

var lastclickid = 0;

function triggerGroup(t) {
    //$('#'+id+' > div.keys > span:eq('+row+')').text();
    //alert($(t).text().substring(0, 1));

    var gvid = $(t).parent().parent().closest('.grid-view').attr('id');
    var gvoffset = $(t).parent().parent().prevAll().length;
    var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
    var ncolumn = $(t).parent().parent().find("td").length;

    /*
    var isthere = $('#'+gvid+' > div.keys > span:eq('+(gvoffset + 1)+')').text();
    if (isthere == (currenttrid+"_dtr")){
        //alert("it was already create!");
        return ;
    }
    */

    var divid = "#groupdiv_" + currenttrid;
    /*
    var dlink = $(t).text();
    if (dlink.substring(0, 1) == "+"){
        dlink = "-" + dlink.substring(1);
        $(divid).show();
    } else {
        dlink = "+" + dlink.substring(1);
        $(divid).hide();
    }
    $(t).text(dlink);
    //alert(dlink);
    */
    $(divid).toggle();
    $("#noteboxdiv").hide();
    $("#noteboxdiv").appendTo($("#hiddenContainer"));

    var tabletpl = $('<table><thead><tr><th>From</th><th>Subject</th><th>Content</th><th>Send Time</th></tr></thead></table>');
    tabletpl.attr({'class':"childitems" });

    var rowtpl = "";

    /*
    //we have 3 ways to find out this object is exist or not.
    if($("#id")[0]){} else {}
    if($("#id").length>0){}else{}
    if(document.getElementById("id")){} else {}
    */
    if ($(divid).length > 0) {
        // do nothing;
        $(divid).appendTo($("#"+currenttrid+"_dtr").children("td"));
        return ;
    } else {
        // call ajax to get the domian backlink information
        //var blurl = "";
        //var tgurl = "";
        $.ajax({
            'type':'GET',
            'dataType':'json',
            'url':"<?php echo CHtml::normalizeUrl(array('/email/group'));?>",
            'data':'id='+currenttrid,
            'success':function(data){
                $.each(data, function(idx, eml){
                    rowtpl = $('<tr></tr>');
                    //blurl = "<a href='"+fmt(eml.url)+"' target='_blank'>"+fmt(eml.url)+"</a>";
                    //$('<td></td>').html(blurl).appendTo(rowtpl);
                    //$('<td></td>').text(fmt(eml.googlepr)).appendTo(rowtpl);
                    //$('<td></td>').html(eml.domain).appendTo(rowtpl);
                    $('<td></td>').html(eml.email_from).appendTo(rowtpl);
                    $('<td></td>').html(eml.subject).appendTo(rowtpl);
                    $('<td></td>').html(eml.content).appendTo(rowtpl);
                    $('<td></td>').html(eml.send_time).appendTo(rowtpl);
                    rowtpl.appendTo(tabletpl);
                });
            },
            'complete':function(XHR,TS){XHR = null;}
        });
    }

    var groupdiv = $("<div class='backlinks'></div>").attr({'id': "groupdiv_" + currenttrid});
    tabletpl.appendTo(groupdiv);

    if ($("#"+currenttrid+"_dtr").length>0) {
        $(groupdiv).appendTo($("#"+currenttrid+"_dtr").children("td"));
    } else {
        var vartr = $('<tr><td colspan="'+ncolumn+'"></td></tr>').attr({ 'id': currenttrid+"_dtr", 'class':"bltr"});
        groupdiv.appendTo(vartr.find("td"));
        //$("<div class='div_backlinks'></div>").append(tabletpl.appendTo(vartr.find("td")));

        $(t).parent().parent().after(vartr);
        //alert(currenttrid);
        $('#'+gvid+' > div.keys > span:eq('+gvoffset+')').after("<span>"+currenttrid+"_dtr</span>");
    }
}

function addNote(t) {
    var gvid = $(t).parent().parent().closest('.grid-view').attr('id');
    var gvoffset = $(t).parent().parent().prevAll().length;
    var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
    var currentdomainid = $(t).attr("domain_id");
    var ncolumn = $(t).parent().parent().find("td").length;

    var divid = "#groupdiv_" + currenttrid;
    $(divid).hide();
    $(divid).appendTo($("#hiddenContainer"));
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
            'url': "<?php echo Yii::app()->createUrl('/domain/note');?>",
            'data': 'domain_id='+currenttrid+"&ajax=true",
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
            var vartr = $('<tr><td colspan="'+ncolumn+'"></td></tr>').attr({ 'id': currenttrid+"_dtr", 'class':"bltr"});
            //$("#noteboxdiv").appendTo(vartr.find("td"));
            $("#noteboxdiv").appendTo(vartr.children("td"));
            $(t).parent().parent().after(vartr);
            $('#'+gvid+' > div.keys > span:eq('+gvoffset+')').after("<span>"+currenttrid+"_dtr</span>");
        }

    } else {}

    lastclickid = currenttrid;
}
</script>
