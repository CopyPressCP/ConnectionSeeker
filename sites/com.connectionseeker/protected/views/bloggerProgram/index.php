<?php
$this->breadcrumbs=array(
	'Blogger Programs'=>array('index'),
	'Manage',
);

$cs=Yii::app()->clientScript;
$cs->registerCoreScript( 'jquery.ui' );
//may be we can use another dropdown plugin http://harvesthq.github.com/chosen/
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.min.js', CClientScript::POS_END);
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.filter.min.js', CClientScript::POS_END);
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.css');
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.filter.css');
$cs->registerCssFile( $cs->getCoreScriptUrl() . '/jui/css/base/jquery-ui.css', 'screen' );

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('blogger-program-grid', {
		data: $(this).serialize()
	});
	return false;
});
");


$bpstatuses = BloggerProgram::$bpstatuses;

$types = Types::model()->bytype(array("bloggerprogram","activeprogram",'cms_username'))->findAll();
$gtps = CHtml::listData($types, 'refid', 'typename', 'type');
$_bloggerprogrames = $gtps['bloggerprogram'] ? $gtps['bloggerprogram'] : array();
$_activeprogrames = $gtps['activeprogram'] ? $gtps['activeprogram'] : array();
$_cms_usernames = $gtps['cms_username'] ? $gtps['cms_username'] : array();

function fmtAttrs($attrs) {
    if ($attrs) {
        $_tmps = explode("|", $attrs);
        array_pop($_tmps);
        array_shift($_tmps);
        return $attrs = $_tmps;
    } else {
        return array();
    }
}

$syndicationes = array("1"=>"Yes","0"=>"No");
$syndicationstr = Utils::array2String(array("" => '[Syndication]') + $syndicationes);
$bpstatusstr = Utils::array2String(array("" => '[Status]') + $bpstatuses);
$categorystr = Utils::array2String($_bloggerprogrames);
$apstr = Utils::array2String($_activeprogrames);


$multiHtmlOptions = Utils::array2String(array("multiple" => 'true'));
function stripStr2Arr($str, $sep = "|"){
    if (!is_array($str) && strpos($str, $sep) === 0) {
        $_tmps = substr($str, 1, -1);
        $str= explode("|", $_tmps);
    }

    return $str;
}

$cuid = Yii::app()->user->id;
$roles = Yii::app()->authManager->getRoles($cuid);
?>

<div id="innermenu">
    <?php $this->renderPartial('/bloggerProgram/menu',array('roles'=>$roles,)); ?>
</div>

<h1>Manage Blogger Programs</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<!-- search-form -->
<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none;padding:0px;margin:0px;">
<?php $this->renderPartial('_search',array(
	'_bloggerprogrames'=>$_bloggerprogrames,
	'_activeprogrames'=>$_activeprogrames,
	'syndicationes'=>$syndicationes,
	'bpstatuses'=>$bpstatuses,
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<!-- search-form -->
<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'blogger-program-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		//'id',
		//'domain',
		array(
			'name' => 'domain',
			'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->domain),"http://www.".$data->domain, array("target"=>"_blank"))',
		),
		'mozauthority',
		//'rinventory.last_published',
		array(
			//'name' => 'rinventory.last_published',
			'name' => 'last_published',
			'type' => 'raw',
            'value' => 'CHtml::encode($data->rinventory->last_published)',
            'headerHtmlOptions'=>array('style'=>'color:#fff'),
		),

        array(
            'name' => 'category_str',
            'type' => 'raw',
            'value' => 'CHtml::dropDownList("category[]", stripStr2Arr($data->category), '.$categorystr.', '.$multiHtmlOptions.')',
            'filter' => CHtml::activeDropDownList($model, 'category', $_bloggerprogrames, array('multiple' =>'true','id'=>'BloggerProgram_category_filter','style'=>'width:160px')),
        ),
		'first_name',
		'last_name',
        //'cms_username',
		array(
			'name' => 'cms_username',
			'type' => 'raw',
            'value' => '$data->cms_user_id ? CHtml::link(CHtml::encode($data->cms_username),"https://content.copypress.com/article/articles.php?keyword=&copy_writer_id=".$data->cms_user_id, array("target"=>"_blank")) : $data->cms_username',
		),
		'contact_email',
        array(
            'name' => 'per_word_rate',
            'type' => 'raw',
            'value' => 'CHtml::textField("per_word_rate[]", $data->per_word_rate)',
        ),
        array(
            'name' => 'status',
            'type' => 'raw',
            'value' => 'CHtml::dropDownList("status", $data->status, '.$bpstatusstr.')',
            'filter' => $bpstatuses,
        ),
        array(
            'name' => 'activeprogram',
            'type' => 'raw',
            'value' => 'CHtml::dropDownList("activeprogram[]", stripStr2Arr($data->activeprogram), '.$apstr.', '.$multiHtmlOptions.')',
            'filter' => CHtml::activeDropDownList($model, 'activeprogram', $_activeprogrames, array('multiple' =>'true','id'=>'BloggerProgram_activeprogram_filter','style'=>'width:160px')),
        ),
        array(
            'name' => 'ronenote.notes',
            'type' => 'raw',
            'filter' => '<div style="width:160px"></div>',
            'headerHtmlOptions'=>array('style'=>'color:#fff;width:160px;'),
        ),
        /*
        array(
            'name' => 'syndication',
            'type' => 'raw',
            'value' => 'CHtml::dropDownList("syndication", $data->syndication, '.$syndicationstr.')',
            'filter' => $syndicationes,
        ),
		'ronenote.notes',
		'category',
		'domain_id',
		'category_str',
		'cms_username',
		'cms_username_str',
		'per_word_rate',
		'activeprogram',
		'activeprogram_str',
		'status',
		'isdelete',
        array(
            'name' => 'cms_user_id',
            'type' => 'raw',
            'value' => 'CHtml::textField("cms_user_id[]", $data->cms_user_id)',
        ),
		*/
		array(
			'class'=>'CButtonColumn',
            'template'=>'{viewall} {note} {view} {update} {delete}',
            'buttons' => array(
                'viewall' => array(
                    'label' => 'View IO History',
                    'imageUrl'=>Yii::app()->theme->baseUrl.'/css/gridview/viewall.png',
                    'url' => 'Yii::app()->createUrl("ios/historic", array("domain_id"=>$data->domain_id,"desired_domain"=>$data->domain,"currentaction"=>"bloggerprogram"))',
                    'options' => array(
                        'class'=>'viewall',
                        'name'=>'viewallioh',
                    ),
                ),
                'note' => array(
                    'label' => 'Add Notes',
                    'imageUrl' => Yii::app()->theme->baseUrl.'/css/gridview/note.png',
                    'url' => 'Yii::app()->createUrl("bloggerProgram/view", array("id"=>$data->id))',
                    'options' => array('id'=>$this->evaluateExpression('$data->id', array('data'=>$data))),
                    'click' => "function(){
                        addNote(this);
                        return false;
                    }",
                ),
                /*
                'price' => array(
                    'label' => 'Add Price',
                    'imageUrl' => Yii::app()->theme->baseUrl.'/css/gridview/dollar.png',
                    'url' => 'Yii::app()->createUrl("bloggerProgram/view", array("id"=>$data->id))',
                    'options' => array('id'=>$this->evaluateExpression('$data->id', array('data'=>$data))),
                    'click' => "function(){
                        addPrice(this);
                        return false;
                    }",
                ),
                */
            ),
            'htmlOptions'=>array('nowrap'=>'nowrap'),
		),
	),
)); ?>


<div id="hiddenContainer">
    <div id="alliohboxdiv" style="display:none;"></div>
    <div id="noteboxdiv" style="display:none;"></div>
    <div id="priceboxdiv" style="display:none;"></div>
</div>

<script type="text/javascript">
<!--
$(document).ready(function(){
    function hideAllBox(){
        $("#noteboxdiv").hide();
        $("#priceboxdiv").hide();
        $("#alliohboxdiv").hide();
        $("#noteboxdiv").appendTo($("#hiddenContainer"));
        $("#priceboxdiv").appendTo($("#hiddenContainer"));
        $("#alliohboxdiv").appendTo($("#hiddenContainer"));
    }

    $("#searchBloggerProgram").click(function(){
        hideAllBox();
    });

    $("#BloggerProgram_status").multiselect({noneSelectedText:'Select Status',selectedList:3}).multiselectfilter();
    $("#BloggerProgram_activeprogram").multiselect({noneSelectedText:'Select Program',selectedList:3}).multiselectfilter();
    $("#BloggerProgram_category").multiselect({noneSelectedText:'Select Category',selectedList:3}).multiselectfilter();
    $("#BloggerProgram_syndication").multiselect({noneSelectedText:'Select Syndication',selectedList:3}).multiselectfilter();

    $.fn.yiiGridView.defaults.afterAjaxUpdate = function(){
        $("#BloggerProgram_category_filter").multiselect({noneSelectedText:'Categories',selectedList:3,minWidth:200})
                                            .multiselectfilter();
        $("#BloggerProgram_activeprogram_filter").multiselect({noneSelectedText:'Active Program',selectedList:3,minWidth:200})
                                            .multiselectfilter();

        $("select[name='syndication'],select[name='status']").each(function() {
            var oldAttrValue = $(this).val();
            $(this).unbind('click').change(function(){
                var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
                var gvoffset = $(this).parent().parent().prevAll().length;
                var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
                var thistd = $(this);
                if(!confirm('Are you sure you want to change this value?')) {
                    $(this).val(oldAttrValue);
                    return false;
                }

                $.ajax({
                    'type': 'GET',
                    'dataType': 'json',
                    'url': "<?php echo CHtml::normalizeUrl(array('/bloggerProgram/setattr'));?>",
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

        $("input[name^='per_word_rate'],input[name^='cms_user_id']").each(function() {
            $(this).css({width:"60px"});
            var oldAttrValue = $.trim($(this).val());
            $(this).unbind('blur').blur(function(){
                var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
                var gvoffset = $(this).parent().parent().prevAll().length;
                var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
                var thistd = $(this);
                var newAttrValue = $.trim($(this).val());
                if (newAttrValue == oldAttrValue){
                    //if the value not changed, then we no need fire a api call
                    return;
                } else {
                    if(!confirm('Are you sure you want to change this value?')) {
                        $(this).val(oldAttrValue);
                        return false;
                    }
                }

                if (currenttrid)
                {
                    $.ajax({
                        'type': 'GET',
                        'dataType': 'json',
                        'url': "<?php echo CHtml::normalizeUrl(array('/bloggerProgram/setattr'));?>",
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
                }

            });
        });

        $("select[name^='category'],select[name^='activeprogram']").each(function(){
            var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
            var gvoffset = $(this).parent().parent().prevAll().length;
            var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
            var thistd = $(this);

            var attrVar = $(this).attr("name").replace(/\[\]/g,'');
            var sText = "Select Category";
            if (attrVar == 'activeprogram') sText = "Select Program";
            attrVar = attrVar + "id" + currenttrid;
            //console.log(attrVar);

            var oldAttrValue = $.trim($(this).val());//use $.trim() to change the int type to string type

            $(this).attr("id", attrVar);
            $("#"+attrVar).multiselect({
                noneSelectedText:sText,selectedList:5, 
                beforeclose: function(event, ui){
                    // event handler here
                    //alert($(this).val());
                    var newAttrValue = $.trim($(this).val());
                    if (newAttrValue == oldAttrValue){
                        //if the value not changed, then we no need fire a api call
                        return;
                    }
                    if(!confirm('Are you sure you want to change this value?')) {
                        //alert(oldAttrValue);
                        //console.log($(this).val());
                        //$(this).val();
                        /*
                        var array_of_checked_values = $("#"+attrVar).multiselect("getChecked").map(function(){
                            return this.value;   
                        }).get();
                        console.log(array_of_checked_values);
                        */
                        return false;
                    }

                    $.ajax({
                        'type': 'GET',
                        'dataType': 'json',
                        'url': "<?php echo CHtml::normalizeUrl(array('/bloggerProgram/setattr'));?>",
                        'data': 'id='+currenttrid+"&attrname="+$(this).attr('name')+"&attrvalue="+$(this).val(),
                        'success':function(data){
                            if (data.success){
                                oldAttrValue = newAttrValue;
                                $(thistd).css("background-color","yellow");
                                alert(data.msg);
                            } else {
                                $(thistd).css("background-color","red");
                                alert(data.msg);
                            }
                        }
                    });
                }
            }).multiselectfilter();
        });

        $("a.viewall[name='viewallioh']").each(function(){
            var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
            var gvoffset = $(this).parent().parent().prevAll().length;
            var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
            var thistd = $(this);

            $(this).unbind('click').click(function(){
                $("#priceboxdiv").hide();
                $("#noteboxdiv").hide();
                if (lastclickid == currenttrid || lastclickid == 0) {
                    $("#alliohboxdiv").toggle();
                } else {
                    $("#alliohboxdiv").show();
                }
                if ($("#alliohboxdiv").is(":visible")) {
                    $.ajax({
                        'type': 'GET',
                        //'dataType': 'json',
                        //'url': "<?php echo Yii::app()->createUrl('/ios/historic');?>",
                        'dataType': 'html',
                        'url': thistd.attr('href'),
                        'data': 'id='+currenttrid+"&ajax=true",
                        'success':function(data){
                            $("#alliohboxdiv").html(data);
                        },
                        'complete':function(XHR,TS){XHR = null;}
                    });

                    if ($("#"+currenttrid+"_dtr").length>0) {
                        /*
                        here you couldn't use the find("td"), coz it will search all of the posterity td elements,
                        The .find() and .children() methods are similar,
                        but .children() only travels a single level down the DOM tree.
                        */
                        $("#alliohboxdiv").appendTo($("#"+currenttrid+"_dtr").children("td"));
                    } else {
                        var tdlength = $("table.items tr:first > th").length;
                        var vartr = $('<tr><td colspan="'+tdlength+'"></td></tr>').attr({ 'id': currenttrid+"_dtr", 'class':"bltr"});
                        $("#alliohboxdiv").appendTo(vartr.children("td"));
                        thistd.parent().parent().after(vartr);
                        $('#'+gvid+' > div.keys > span:eq('+gvoffset+')').after("<span>"+currenttrid+"_dtr</span>");
                    }
                } else {}

                lastclickid = currenttrid;
                return false;
            });
        });

        //issue: when the user click the pager(i mean change pager number), then the mailbox & notebox may not showing again.
        //cause we append the mailbox & notebox into the grid already when we click the addmail or addnote button,
        //so when we jump to another outreach page number, the mailbox & notebox may be removed due to the grid overwrited.
        $("div.pager > ul > li a").each(function(){
            $(this).click(function() {
                hideAllBox();
            });
            return true;
        });

        $("table.items #pageSize").change(function(){
            hideAllBox();
        });

        $("table.items thead tr.filters input, table.items thead tr.filters select").each(function(){
            $(this).change(function() {
                hideAllBox();
            });
            return true;
        });

        var _ids = [];
        $('#blogger-program-grid > div.keys > span').each(function(i){
            _ids[i] = $(this).html();
        });
        $("#blogger-program-grid > table.items > tbody > tr").each(function(i){
            $(this).attr("id", "etr"+_ids[i]);//reset table.tr.id
        });

        $.ajax({
            'type': 'POST',
            'dataType': 'json',
            'url': "<?php echo Yii::app()->createUrl('/bloggerProgramNote/icon');?>",
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

                    if (data.freshnote) {
                        $.each(data.freshnote, function (v){
                            $("#etr" +v+" > td:last > a.note > img").attr("src", "<?php echo Yii::app()->theme->baseUrl?>/css/gridview/freshnote.png");
                        });
                    }
                }
            },
            'complete':function(XHR,TS){XHR = null;}
        });

        $.ajax({
            'type': 'POST',
            'dataType': 'json',
            'url': "<?php echo Yii::app()->createUrl('/bloggerProgramPrice/icon');?>",
            'data': {'ids[]': _ids},
            'success':function(data){
                //alert(data.msg);
                if (data.success){
                    if (data.ids){
                        $.each(data.ids, function (v){
                            //alert(v);
                            $("#etr" +v+" > td:last > a.price > img").attr("src", "<?php echo Yii::app()->theme->baseUrl?>/css/gridview/dollars.png");
                        });
                    }
                }
            },
            'complete':function(XHR,TS){XHR = null;}
        });
    }

    $.fn.yiiGridView.defaults.afterAjaxUpdate();
});

var lastclickid = 0;

function addNote(t) {
    var gvid = $(t).parent().parent().closest('.grid-view').attr('id');
    var gvoffset = $(t).parent().parent().prevAll().length;
    var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
    var currentdomain = $(t).parent().parent().children("td:eq(1)").text();

    $("#priceboxdiv").hide();
    $("#alliohboxdiv").hide();
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
            'url': "<?php echo Yii::app()->createUrl('/bloggerProgram/note');?>",
            'data': 'blogger_program_id='+currenttrid+"&ajax=true",
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
            var vartr = $('<tr><td colspan="12"></td></tr>').attr({ 'id': currenttrid+"_dtr", 'class':"bltr"});
            //$("#noteboxdiv").appendTo(vartr.find("td"));
            $("#noteboxdiv").appendTo(vartr.children("td"));
            $(t).parent().parent().after(vartr);
            $('#'+gvid+' > div.keys > span:eq('+gvoffset+')').after("<span>"+currenttrid+"_dtr</span>");
        }

    } else {}

    lastclickid = currenttrid;
}

function addPrice(t) {
    var gvid = $(t).parent().parent().closest('.grid-view').attr('id');
    var gvoffset = $(t).parent().parent().prevAll().length;
    var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
    var currentdomain = $(t).parent().parent().children("td:eq(1)").text();

    $("#noteboxdiv").hide();
    $("#alliohboxdiv").hide();
    if (lastclickid == currenttrid || lastclickid == 0) {
        $("#priceboxdiv").toggle();
    } else {
        $("#priceboxdiv").show();
    }

    if ($("#priceboxdiv").is(":visible")) {

        $.ajax({
            'type': 'GET',
            //'dataType': 'json',
            'dataType': 'html',
            'url': "<?php echo Yii::app()->createUrl('/bloggerProgram/price');?>",
            'data': 'blogger_program_id='+currenttrid+"&ajax=true",
            'success':function(data){
                $("#priceboxdiv").html(data);
            },
            'complete':function(XHR,TS){XHR = null;}
        });


        if ($("#"+currenttrid+"_dtr").length>0) {
            /*
            here you couldn't use the find("td"), coz it will search all of the posterity td elements,
            The .find() and .children() methods are similar,
            but .children() only travels a single level down the DOM tree.
            */
            $("#priceboxdiv").appendTo($("#"+currenttrid+"_dtr").children("td"));
        } else {
            var vartr = $('<tr><td colspan="12"></td></tr>').attr({ 'id': currenttrid+"_dtr", 'class':"bltr"});
            //$("#noteboxdiv").appendTo(vartr.find("td"));
            $("#priceboxdiv").appendTo(vartr.children("td"));
            $(t).parent().parent().after(vartr);
            $('#'+gvid+' > div.keys > span:eq('+gvoffset+')').after("<span>"+currenttrid+"_dtr</span>");
        }

    } else {}

    lastclickid = currenttrid;
}
</script>
