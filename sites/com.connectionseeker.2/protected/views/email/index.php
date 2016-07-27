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
		'created',
        array(
            'name' => 'created_by',
            'type' => 'raw',
            'value' => 'CHtml::encode($data->rcreatedby->username)',
            'filter' => $ftusers,
        ),

		/*
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
            'template'=>'{plus} {view}{update}{delete}',
            'buttons' => array(
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


<script type="text/javascript">
$(document).ready(function() {
    //do nothing for now.
});

function triggerGroup(t) {
    //$('#'+id+' > div.keys > span:eq('+row+')').text();
    //alert($(t).text().substring(0, 1));

    var gvid = $(t).parent().parent().closest('.grid-view').attr('id');
    var gvoffset = $(t).parent().parent().prevAll().length;
    var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);

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
    var vartr = $('<tr><td colspan="11"></td></tr>').attr({ 'id': currenttrid+"_dtr", 'class':"bltr"});
    groupdiv.appendTo(vartr.find("td"));
    //$("<div class='div_backlinks'></div>").append(tabletpl.appendTo(vartr.find("td")));

    $(t).parent().parent().after(vartr);

    //alert(currenttrid);
    $('#'+gvid+' > div.keys > span:eq('+gvoffset+')').after("<span>"+currenttrid+"_dtr</span>");
}
</script>
