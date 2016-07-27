<?php
$this->breadcrumbs=array(
	'Domains'=>array('index'),
	'Manage',
);

$mailvisible = false;

//Yii::app()->request->getQuery this return the $_GET. Yii::app()->request->getPost this return the $_POST 
if (Yii::app()->request->getParam("touched") == true) {
    //print_r($_REQUEST["Domain"]);
    $_request_ts = array();
    if (is_array($_REQUEST["Domain"]["touched_status"])) $_request_ts = $_REQUEST["Domain"]["touched_status"];
    if (in_array($_REQUEST["Domain"]["touched_status"], array(6,15,20)) || in_array(6, $_request_ts) 
        || in_array(15, $_request_ts) || in_array(20, $_request_ts)
        || !empty($_REQUEST["Domain"]["domain"]) || !empty($_REQUEST["Domain"]["id"])) {
        $dataProvider = $model->search();
    } else {
        $dataProvider = $model->touched()->actived()->search();
    }
    $mailvisible = true;
    $h1title = "Outreach List";
} else {
    $dataProvider = $model->search();
    $h1title = "Manage Domains";
}

$types = Types::model()->actived()->bytype(array("site","outreach","category","technorati","awis","tierlevel"))->findAll();
$gtps = CHtml::listData($types, 'refid', 'typename', 'type');

$stypes = $gtps['site'] ? $gtps['site'] : array();
$otypes = $gtps['outreach'] ? $gtps['outreach'] : array();
$categories = $gtps['category'] ? $gtps['category'] : array();
$technoraties = $gtps['technorati'] ? $gtps['technorati'] : array();
$awises = $gtps['awis'] ? $gtps['awis'] : array();
$tierleveles = $gtps['tierlevel'] ? $gtps['tierlevel'] : array();

//$stypestr = CVarDumper::DumpAsString($stypes);
$stypestr = Utils::array2String(array("" => '[Site Type]') + $stypes);
$otypestr = Utils::array2String(array("" => '[Outreach Type]') + $otypes);
//$categorystr = Utils::array2String(array("" => '[Category]') + $categories);
$categorystr = Utils::array2String($categories);
$technoratistr = Utils::array2String(array("" => '[Technorati Category]') + $technoraties);
$awisstr = Utils::array2String(array("" => '[AWIS Category]') + $awises);

$touchedstatus = Domain::$status;
$statusstr = Utils::array2String($touchedstatus);
//echo $statusstr;

$cs=Yii::app()->clientScript;
$cs->registerCoreScript( 'jquery.ui' );

$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.min.js', CClientScript::POS_END);
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.filter.min.js', CClientScript::POS_END);
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.css');
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.filter.css');
$cs->registerCssFile( $cs->getCoreScriptUrl() . '/jui/css/base/jquery-ui.css', 'screen' );

$cs->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('domain-grid', {
		data: $(this).serialize()
	});
	return false;
});
");


function fmtCategory($cats) {
    if ($cats) {
        $_tmps = explode("|", $cats);
        array_pop($_tmps);
        array_shift($_tmps);
        return $cats = $_tmps;
    } else {
        return array();
    }
}

function fmtSemrush($semor, $islink = false) {
    $rtn = "";
    if (is_numeric($semor)) {
        if ($semor > 0 ) {
            $rtn = "Yes";
        } elseif ($semor < 0)  {
            $rtn = "No";
        }
    } else {
        if (!empty($semor)) {
            $rtn = "Yes";
        }
    }

    if ($islink && $rtn != "") {
        if (is_numeric($semor) && $semor < 0) {
            $semor = "ERROR CODE: ".$semor;
        } else {
            $semor = str_replace("|", ", ", $semor);
        }
        $rtn = "<a href='javascript:void(0);' title='{$semor}'>".$rtn."</a>";
    }

    echo $rtn;
}

$multiHtmlOptions = Utils::array2String(array("multiple" => 'true'));
function stripStr2Arr($str, $sep = "|"){
    if (!is_array($str) && strpos($str, $sep) === 0) {
        $_tmps = substr($str, 1, -1);
        $str= explode("|", $_tmps);
    }

    return $str;
}

$isadmin = 0;
$roles = Yii::app()->authManager->getRoles(Yii::app()->user->id);
if(isset($roles['Admin'])){
    $isadmin = 1;//true
}

$semrushes = array(
    "0" => "Pending",
    "1" => "Yes",
    "-1" => "No",
);

?>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<!-- search-form -->
<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
	'touchedstatus'=>$touchedstatus,
	'stypes'=>$stypes,
	'otypes'=>$otypes,
	'categories'=>$categories,
	'countries'=>$countries,
	'semrushes'=>$semrushes,
	'tierleveles'=>$tierleveles,
)); ?>
</div><!-- search-form -->

<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'domain-grid',
	'dataProvider'=>$dataProvider,
	//'filter'=>$model,
    'selectableRows' => '2',
	'columns'=>array(
        array(
            'id'=>'ids',
            'class'=>'CCheckBoxColumn',
        ),
		'id',
		/*'domain',*/
		array(
			'name' => 'domain',
			'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->domain),"http://www.".$data->domain, array("target"=>"_blank"))',
		),
        array(
            'name' => 'semrushkeywords',
            'header' => "SEM KW",
            'type' => 'raw',
            'value' => 'str_replace("|", ", ", $data->rsummary->semrushkeywords)',
            //'value' => 'fmtSemrush($data->rsummary->semrushkeywords, true)',
            'visible' => isVisible('semrushkeywords', $dparr),
            'filter' => $semrushes,
        ),


        array(
            'name' => 'category_str',
            'type' => 'raw',
            'value' => 'CHtml::dropDownList("category[]", stripStr2Arr($data->category), '.$categorystr.', '.$multiHtmlOptions.')',
            'filter' => CHtml::activeDropDownList($model, 'category', $categories, array('multiple' =>'true','id'=>'Domain_category_filter','style'=>'width:160px')),
        ),
		array(
			'class'=>'CButtonColumn',
            'template'=>'',
		),
	),
)); ?>


<div class="clear"></div>

<?php $this->renderPartial('_bulkattr',array(
	'model'=>$model,
    'roles'=>$roles,
	'categories'=>$categories,
)); ?>

<style>
.filtermultiselect{
    width:100px;
}
</style>

<script type="text/javascript">
$(document).ready(function() {
    $('#processing').bind("ajaxSend", function() {
        $(this).html("&nbsp;");
        $(this).css('background-image', 'url(' + "<?php echo Yii::app()->theme->baseUrl; ?>" + '/images/loading.gif)');
        //$(this).show();
    }).bind("ajaxComplete", function() {
        $(this).css('background-image', '');
        $(this).css('color', 'red');
        //$(this).hide();
    });

    $("#Domain_move2category").multiselect({noneSelectedText:'Select Categories',selectedList:5}).multiselectfilter();

    $.fn.yiiGridView.defaults.afterAjaxUpdate = function(){
        //var filterstatus = $("table.items tr.filters select[name='Domain\[touched_status\]\[\]']");
        $("#Domain_status_filter").multiselect({noneSelectedText:'Select Status',selectedList:3,minWidth:160}).multiselectfilter();
        $("#Domain_category_filter").multiselect({noneSelectedText:'Select Catetories',selectedList:3,minWidth:200}).multiselectfilter();

        $("select[name^='category']").each(function(){
            var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
            var gvoffset = $(this).parent().parent().prevAll().length;
            var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
            var thistd = $(this);

            var attrVar = "catid" + currenttrid;
            var sText = "Select Catgory";
            /*
            if ($(this).attr('name') == 'category[]') {
                var attrVar = "catid" + currenttrid;
                var sText = "Select Catgory";
            } else {
                var attrVar = "ociid" + currenttrid;
                var sText = "Select Owner";
            }
            */
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

                    $.ajax({
                        'type': 'GET',
                        'dataType': 'json',
                        'url': "<?php echo CHtml::normalizeUrl(array('/domain/setattr'));?>",
                        'data': 'id='+currenttrid+"&attrname="+$(this).attr('name')+"&attrvalue="+$(this).val(),
                        'success':function(data){
                            if (data.success){
                                oldAttrValue = newAttrValue;
                                $(thistd).css("background-color","yellow");
                            } else {
                                $(thistd).css("background-color","red");
                                alert(data.msg);
                            }
                        }
                    });
                }
            }).multiselectfilter();
        });

        var _ids = [];
        $('#domain-grid > div.keys > span').each(function(i){
            _ids[i] = $(this).html();
        });
        $("#domain-grid > table.items > tbody > tr").each(function(i){
            $(this).attr("id", "etr"+_ids[i]);//reset table.tr.id
        });
    }

    $.fn.yiiGridView.defaults.afterAjaxUpdate();

    $("#bulksetattr").click(function(){
        var rtn = false;
        var domainids = new Array;
        var idsidx = new Array;

        $("#bulkseterror").hide();
        $("input[name='ids[]'][type='checkbox']:checked").each(function(i) {
            rtn = true;
            domainids[i] = $(this).val();
            idsidx[i] = $(this).attr("id");
        });

        if (!rtn){
            $("#bulkseterror > ul").html("<li>Please choose one domain at least.</li>");
        } else {
            $("#Domain_domain_ids").val(domainids.join(","));
        }

        if (rtn){
            if ($("#Domain_move2category").val()){
            } else {
                $("#bulkseterror > ul").html("<li>Please choose the options from the dropdown list.</li>");
                $("#bulkseterror").show();
                return false;
            }
        }

        if (!rtn) {
            $("#bulkseterror").show();
            return false;
        }
        //###//return rtn;


        $.ajax({
            'type': 'POST',
            'dataType': 'json',
            'url': "<?php echo Yii::app()->createUrl('domain/bulkattr');?>",
            'data': $("#bulkSetDomainAttrForm").serialize(),
            'success':function(data){
                //donothing for now;
                if (data.success){
                    //alert(data.msg);
                    if (data.ids){
                        //$.each(data.ids, function(i, v){
                        //});
                        $.each(idsidx, function(i, v){
                            $("#"+v).attr('checked', false);
                            //$("#"+v).parent().next().next().addClass("linethrough");
                            var trparent = $("#"+v).parent().parent();
                            trparent.addClass("linethrough");
                            trparent.children(":last").html($("#Domain_move2category").multiselect("getButton").text());
                            //$.fn.yiiGridView.defaults.afterAjaxUpdate();
                            /*
                            trparent.find("select[name^='category']").multiselect("uncheckAll");
                            trparent.find("select[name^='category']").val($("#Domain_move2category").val());
                            */
                            //##$("#"+v).parent().parent().find("select[name^='category']").val($("#Domain_move2category").val());
                        });
                    }
                } else {
                    alert(data.msg);
                }
            },
            'complete':function(XHR,TS){XHR = null;}
        });
    });

    $("#bulkallsetattr").click(function(){
        var bulknumber = 0;
        var bulksummary = $("#domain-grid div.summary").html();
        if ($("#domain-grid div.summary").length > 0){
            bulksummary = bulksummary.match(/of \d result/g);
            bulksummary = String(bulksummary);
            bulknumber = bulksummary.match(/\d/g);
        } else {
            alert("No results found.");
            return false;
        }

        if(confirm("Are you sure you want to move "+bulknumber+" domain(s)?")){
            //do nothing for now;
        } else {
            return false;
        }

        $("#bulkseterror").hide();
        if ($("#Domain_move2category").val()){
        } else {
            $("#bulkseterror > ul").html("<li>Please choose the options from the dropdown list.</li>");
            $("#bulkseterror").show();
            return false;
        }

        var category_str = $( "#Domain_move2category" ).multiselect("getChecked").map(function(){
           return this.title;
        }).get().join(', ');
        var category = $("#Domain_move2category").val().join('|');
        category = "|"+category+"|";

        $.ajax({
            'type': 'POST',
            'dataType': 'json',
            'url': "<?php echo Yii::app()->createUrl('domain/bulkattr');?>",
            'data': {'search':$("#domainSearchForm").serialize(),'operation':'moveAll',
                     'category':category,'category_str':category_str},
            'success':function(data){
                //donothing for now;
                if (data.success){
                    alert(data.msg);
                    $.fn.yiiGridView.update('domain-grid', {
                        data: $('.search-form form').serialize()
                    });
                } else {
                    alert(data.msg);
                }
            },
            'complete':function(XHR,TS){XHR = null;}
        });
    });
});
</script>

<style type="text/css">
.grid-view table.items tr.bltr td {
    height:100%;
}
</style>