<?php
$this->breadcrumbs=array(
	'Discoveries'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('discovery-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
//print_r($_GET);
?>

<h1>Manage Discoveries</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
<br /><span style="color:red">Caution: The anchor match filter is slow query, it will take some minutes when you use it.</span>
</p>

<!-- search-form -->
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
<!-- search-form -->
<div style="clear:both"></div>

<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'discovery-grid',
	'dataProvider'=>$model->search(),
	//'filter'=>$model,
	'columns'=>array(
		//'id',
		//'competitor_id',
		//'domain_id',
		//'fresh_called',
		//'historic_called',
        array(
            'name' => 'domain_id',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode("+ " . $data->rdomain->domain), "javascript:;", array("onclick"=> "toggleBacklinks(this);"))',
            //'value' => 'CHtml::encode($data->rdomain->domain)',
        ),

        array(
            'name' => 'competitor_id',
            'type' => 'raw',
            //'value' => 'CHtml::link(CHtml::encode($data->rcompetitor->domain), array("competitor/view", "id" =>$data->competitor_id))',
            'value' => 'CHtml::encode($data->rcompetitor->domain)',
            //'filter' => CHtml::listData(Competitor::model()->findAll(),'id','domain'),
        ),

        'hubcount',

        array(
            'name' => 'rdomain.age',
            'type' => 'raw',
            'value' => 'CHtml::encode((($data->rdomain->onlinesince - 658454400) > 0) ? date("Y-m-d", $data->rdomain->onlinesince) : "-1")',
            //'filter' => CHtml::listData(Domain::model()->findAll(),'id','domain'),
        ),

        array(
            'name' => 'rdomain.googlepr',
            'type' => 'raw',
            'value' => 'CHtml::encode($data->rdomain->googlepr)',
        ),

        array(
            'name' => 'rdomain.alexarank',
            'type' => 'raw',
            'value' => 'CHtml::encode($data->rdomain->alexarank)',
        ),

        array(
            'name' => 'rdomain.linkingdomains',
            'type' => 'raw',
            'value' => 'CHtml::encode($data->rdomain->linkingdomains)',
        ),

        array(
            'name' => 'rdomain.inboundlinks',
            'type' => 'raw',
            'value' => 'CHtml::encode($data->rdomain->inboundlinks)',
        ),

        //'rdomain.stype',
        array(
            'name' => 'rdomain.stype',
            'type' => 'raw',
            'value' => 'CHtml::dropDownList("rdomain.stype", $data->rdomain->stype, '.$stypestr.')',
        ),

        //'rdomain.otype',
        array(
            'name' => 'rdomain.otype',
            'type' => 'raw',
            'value' => 'CHtml::dropDownList("rdomain.otype", $data->rdomain->otype, '.$otypestr.')',
        ),

        /*
        array(
            'name' => 'fresh_called',
            'type' => 'raw',
            'value' => 'CHtml::encode((($data->fresh_called - 658454400) > 0) ? date("Y-m-d H:i:s", $data->fresh_called) : "-1")',
        ),

        array(
            'name' => 'historic_called',
            'type' => 'raw',
            'value' => 'CHtml::encode((($data->historic_called - 658454400) > 0) ? date("Y-m-d H:i:s", $data->historic_called) : "-1")',
        ),
        */

        /*
		array(
			'class'=>'CButtonColumn',
		),
        */

        array(
            'class'=>'CButtonColumn',
            //'template'=>'{delete}{touch}',
            'template'=>'{touch} {update}',
            'buttons' => array(
                'touch' => array(
                    'label' => 'Add This Domain Into Outreach',
                    'visible' => '$data->rdomain->created_by == null',
                    //'visible' => '$data->rdomain->touched_by == null',
                    'imageUrl'=>Yii::app()->theme->baseUrl.'/images/taskcart.png',
                    'url' => 'Yii::app()->createUrl("discovery/touch", array("id"=>$data->rdomain->id))',                   
                    'options' => array(
                        'class'=>'active',
                    ),
                    'click' => "function() {
    if(!confirm('Do you really want to add this domain '+$(this).parent().parent().children(':nth-child(1)').text()+' to this card?' )) return false;
    var th=this;
    var afterTouch=function(){};
    $.fn.yiiGridView.update('discovery-grid', {
        type:'POST',
        url:$(this).attr('href'),
        data: {'YII_CSRF_TOKEN': '" . Yii::app()->request->csrfToken . "'},
        success:function(data) {
            afterTouch(th,true,data);
            $(th).remove();
        },
        error:function(XHR) {
            return afterTouch(th,false,XHR);
        }
    });
    return false;
}",
                ),
            'update'=> array(
                    'imageUrl'=>Yii::app()->theme->baseUrl.'/images/edit.png',
                    'url' => 'Yii::app()->createUrl("domain/update", array("id"=>$data->rdomain->id))',                   
                ),
            ),
        ),
	),
)); ?>


<script type="text/javascript">

$(document).ready(function() {
    //$("select[name='stype[]']").each(function() {
    $.fn.yiiGridView.defaults.afterAjaxUpdate = function(){
        $("select[name='rdomain.stype'],select[name='rdomain.otype']").each(function() {
            //way 1
            //$(this).change(updateType);
            //$(this).unbind('click').change(updateType);

            //way 2
            $(this).unbind('click').change(function(){
                var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
                var gvoffset = $(this).parent().parent().prevAll().length;
                var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);

                $.ajax({
                    'type': 'GET',
                    'dataType': 'json',
                    'url': "<?php echo CHtml::normalizeUrl(array('/domain/setattr'));?>",
                    'data': 'id=0&bdid='+currenttrid+"&attrname="+$(this).attr('name')+"&attrvalue="+this.value,
                    'success':function(data){
                        //donothing for now;
                        alert(data.msg);
                    }
                });
            });
        });
    }

    $.fn.yiiGridView.defaults.afterAjaxUpdate();
});

function fmt(v) {
    if (typeof(v) == "undefined" || v == null){
        return -1;
    }

    return v;
}

/*
function updateType(t, s){
    var gvid = $(t).parent().parent().closest('.grid-view').attr('id');
    var gvoffset = $(t).parent().parent().prevAll().length;
    var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);

    $.ajax({
        'type':'GET',
        'dataType':'json',
        'url':"<?php echo CHtml::normalizeUrl(array('/domain/settype'));?>",
        'data':'id=0&bdid='+currenttrid+"&type="+$(t).attr('name')+"&typevalue="+t.value,
        'success':function(data){
            //donothing for now;
            alert(data.msg);
        }
    });
}
*/

function toggleBacklinks(t) {
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

    var divid = "#bldiv_" + currenttrid;
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


    var tabletpl = $('<table><thead><tr><th>Page URL</th><th>PR</th><th>ACRank</th><th>Anchor Text</th><th>Target URL</th><th>Flag(R|F|N|I|D|A|M)</th></tr></thead></table>');
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
        var blurl = "";
        var tgurl = "";
        $.ajax({
            'type':'GET',
            'dataType':'json',
            'url':"<?php echo CHtml::normalizeUrl(array('/discovery/backlink'));?>",
            'data':'id='+currenttrid+"&client_domain_id=<?php echo Yii::app()->request->getQuery('client_domain_id');?>",
            'success':function(data){
                $.each(data, function(idx, backlink){
                    rowtpl = $('<tr></tr>');
                    blurl = "<a href='"+fmt(backlink.url)+"' target='_blank'>"+fmt(backlink.url)+"</a>";
                    tgurl = "<a href='"+fmt(backlink.targeturl)+"' target='_blank'>"+fmt(backlink.targeturl)+"</a>";
                    $('<td></td>').html(blurl).appendTo(rowtpl);
                    $('<td></td>').text(fmt(backlink.googlepr)).appendTo(rowtpl);
                    $('<td></td>').text(fmt(backlink.acrank)).appendTo(rowtpl);
                    $('<td></td>').text(fmt(backlink.anchortext)).appendTo(rowtpl);
                    $('<td></td>').html(tgurl).appendTo(rowtpl);
                    $('<td></td>').text(backlink.flagredirect+"|"+backlink.flagframe+"|"+backlink.flagnofollow+"|"+backlink.flagimages+"|"+backlink.flagdeleted+"|"+backlink.flagalttext+"|"+backlink.flagmention).appendTo(rowtpl);
                    rowtpl.appendTo(tabletpl);
                });
            }
        });
    }

    var backlinkdiv = $("<div class='backlinks'></div>").attr({'id': "bldiv_" + currenttrid});
    tabletpl.appendTo(backlinkdiv);
    var vartr = $('<tr><td colspan="11"></td></tr>').attr({ 'id': currenttrid+"_dtr", 'class':"bltr"});
    backlinkdiv.appendTo(vartr.find("td"));
    //$("<div class='div_backlinks'></div>").append(tabletpl.appendTo(vartr.find("td")));

    $(t).parent().parent().after(vartr);

    //alert(currenttrid);
    $('#'+gvid+' > div.keys > span:eq('+gvoffset+')').after("<span>"+currenttrid+"_dtr</span>");
}


/*
$('.grid-view table tbody tr').live('click', function() {
        var id = $.fn.yiiGridView.getKey(
                $(this).closest('.grid-view').attr('id'),
                $(this).prevAll().length
        );

        alert(id);
        //window.location = viewUrl + id;
});
*/

</script>