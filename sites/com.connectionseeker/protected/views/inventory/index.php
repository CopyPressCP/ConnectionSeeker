<?php
$this->breadcrumbs=array(
	'Inventories'=>array('index'),
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

//Yii::app()->clientScript->registerScript('search', "
$cs->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('inventory-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

$types = Types::model()->actived()->findAll("type='site' OR type='outreach' OR type='category' OR type='channel' OR type='linktask'");
$gtps = CHtml::listData($types, 'refid', 'typename', 'type');
//extract($gtps);//doNot use extract here,cause the name $site, $category,... was too simple, it is easy overwrite by accidently
$_stypes = $gtps['site'] ? $gtps['site'] : array();
$_otypes = $gtps['outreach'] ? $gtps['outreach'] : array();
$_linktasks = $gtps['linktask'] ? $gtps['linktask'] : array();
//print_r($_stypes);
$_categories = $gtps['category'] ? $gtps['category'] : array();
$_channels = $gtps['channel'] ? $gtps['channel'] : array();
$chnlstr = Utils::array2String($_channels);
$catstr = Utils::array2String($_categories);
$stypestr = Utils::array2String($_stypes);
$otypestr = Utils::array2String($_otypes);
//print_r($gtps);
$_status = array('0'=>'Inactive','1'=>'Active');

$uinfo = User::model()->findByPk(Yii::app()->user->id);
$clients = Client::model()->actived()->findAll(array('order'=>'company ASC'));
$_clients = CHtml::listData($clients,'id','company');
$_clientstr = Utils::array2String($_clients);

if (isset($roles['Marketer'])) {
    $domains = ClientDomain::model()->byduty()->findAll('client_id=:client_id', array(':client_id'=>$uinfo->client_id));
    $domains = CHtml::listData($domains,'id','domain');
    $clients = $uinfo->client_id;
} else {
    //$clients = Client::model()->actived()->findAll();
    $domains = array();
}

/*
$currentaction = $this->action->getId();
if ($currentaction == "index") $currentaction = "acquired"; 
*/
$currentaction = $model->currentaction;


$probabilities = Inventory::$probabilities;
$pbtstr = Utils::array2String(array("0" => '[Probability]') + $probabilities);
$ownerHtmlOptions = Utils::array2String(array("multiple" => 'true'));

function stripStr2Arr($str, $sep = "|"){
    if (!is_array($str) && strpos($str, $sep) === 0) {
        $_tmps = substr($str, 1, -1);
        $str= explode("|", $_tmps);
    }

    return $str;
}
?>

<h1>Manage <?php echo ucfirst($currentaction);?> Inventories</h1>
<div id="processing" style="float:left;width:220px;">&nbsp;</div><br />

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<!-- search-form -->
<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none;padding:0px;margin:0px;">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
	'_stypes'=>$_stypes,
	'_otypes'=>$_otypes,
	'_categories'=>$_categories,
	'_channels'=>$_channels,
	'_status'=>$_status,
	'_linktasks'=>$_linktasks,
	'roles'=>$roles,
)); ?>
</div><!-- search-form -->


<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'inventory-grid',
	'dataProvider'=>$model->actived()->search(),
	'filter'=>$model,
    'selectableRows' => '0',
	'columns'=>array(
        array(
            'id'=>'ids',
            'class'=>'CCheckBoxColumn',
        ),
		'id',
        array(
            'name' => 'domain',
            'type' => 'raw',
            'value' => 'domain2URL($data->domain, true, array("target"=>"_blank"))',
            //'value' => 'CHtml::encode($data->domain)',
            //'visible' => !isset($roles['Marketer']),
        ),
        array(
            //'name' => 'rdomain.stype',
            'header' => 'Site Type',
            'name' => 'stype',
            'type' => 'raw',
            'value' => 'CHtml::encode(Utils::getValue(' . $stypestr . ', $data->rdomain->stype))',
            'filter' => $_stypes,
        ),
        array(
            //'name' => 'rdomain.otype',
            'header' => 'Outreach Type',
            'name' => 'otype',
            'type' => 'raw',
            'value' => 'CHtml::encode(Utils::getValue(' . $otypestr . ', $data->rdomain->otype))',
            'filter' => $_otypes,
        ),
        /*
        array(
            'header' => 'PR',
            'name' => 'googlepr',
            'type' => 'raw',
            'value' => '$data->rdomain->googlepr',
        ),
		//'rdomain.googlepr',
        array(
            'header' => 'Moz Rank',
            //'name' => 'rdomain.rsummary.mozrank',
            'name' => 'mozrank',
            'type' => 'raw',
            'value' => 'round($data->rdomain->rsummary->mozrank)',
        ),
        array(
            'header' => 'Authority',
            //'name' => 'rdomain.rsummary.mozauthority',
            'name' => 'mozauthority',
            'type' => 'raw',
            'value' => 'round($data->rdomain->rsummary->mozauthority)',
        ),
        array(
            'header' => 'Alexa Rank',
            'name' => 'alexarank',
            'value'=>'number_format($data->rdomain->alexarank)',
        ),
        */
        /*
		'rdomain.alexarank:number',
        'rdomain.linkingdomains:number',
		'rdomain.inboundlinks:number',
		'domain',
		'rdomain.alexarank',
		'rdomain.linkingdomains',
		'rdomain.inboundlinks',
        array(
            'name' => 'rdomain.linkingdomains',
            'value'=>'number_format($data->rdomain->linkingdomains)',
        ),
        array(
            'name' => 'rdomain.inboundlinks',
            'value'=>'number_format($data->rdomain->inboundlinks)',
        ),

        //'rdomain.age',
        array(
            'name' => 'rdomain.onlinesince',
            'type' => 'raw',
            'value' => 'CHtml::encode((($data->rdomain->onlinesince - 658454400) > 0) ? date("Y-m-d", $data->rdomain->onlinesince) : "-1")',
            'header' => 'Age',
        ),
        'category_str',
        array(
            'name' => 'category_str',
            'type' => 'raw',
            'value' => !isset($roles['Marketer']) && $currentaction == "acquired" ? 'CHtml::dropDownList("category[]", stripStr2Arr($data->category), '.$catstr.', '.$ownerHtmlOptions.')' : 'CHtml::encode($data->category_str)',
            //'filter' => $_categories,
            //'value' => 'CHtml::encode($data->category_str)',
        ),
        */
        array(
            'name' => 'category_str',
            'type' => 'raw',
            'value' => !isset($roles['Marketer']) && $currentaction != "denied" ? 'CHtml::dropDownList("category[]", stripStr2Arr($data->category), '.$catstr.', '.$ownerHtmlOptions.')' : 'CHtml::encode($data->category_str)',
            //'filter' => $_categories,
            //'value' => 'CHtml::encode($data->category_str)',
        ),

        array(
            'name' => 'client_id',
            'type' => 'raw',
            'value' => 'CHtml::dropDownList("client_id[]", stripStr2Arr($data->client_id), '.$_clientstr.', '.$ownerHtmlOptions.')',
            'filter' => $_clients,
            //'visible' => !isset($roles['Marketer']) && $currentaction == "acquired",
            'visible' => !isset($roles['Marketer']) && ($currentaction == "acquired" || $currentaction == "published"),
        ),

        array(
            'name' => 'channel_str',
            'type' => 'raw',
            'value' => 'CHtml::encode($data->channel_str)',
            'visible' => !isset($roles['Marketer']) && $currentaction == "published",
        ),
        array(
            'name' => 'acquired_channel_id',
            'type' => 'raw',
            'value' => '$data->acquired_channel_id ? CHtml::encode(Utils::getValue(' . $chnlstr . ', $data->acquired_channel_id)) : ""',
            'visible' => !isset($roles['Marketer']) && $currentaction == "acquired",
            'filter' => $_channels,
        ),
        array(
            'name' => 'denied_by',
            'value' => '$data->denied_by_str',
            'type' => 'raw',
            'visible' => !isset($roles['Marketer']) && $currentaction == "denied",
            'filter' => CHtml::listData(User::model()->findAll(),'id','username'),
        ),
        array(
            'name' => 'acquireddate',
            'type' => 'raw',
            'visible' => !isset($roles['Marketer']) && $currentaction == "acquired",
            //'visible' => !isset($roles['Marketer']) && $currentaction != "published",
        ),

        array(
            'name' => 'owner_channel_id',
            'type' => 'raw',
            'value' => 'CHtml::dropDownList("owner_channel_id[]", stripStr2Arr($data->owner_channel_id), '.$chnlstr.', '.$ownerHtmlOptions.')',
            'filter' => $_channels,
            'visible' => !isset($roles['Marketer']) && $currentaction == "acquired",
        ),

        array(
            'name' => 'probability',
            'type' => 'raw',
            'value' => 'CHtml::dropDownList("probability[]", $data->probability, '.$pbtstr.')',
            'filter' => $probabilities,
            'visible' => !isset($roles['Marketer']) && $currentaction == "acquired",
        ),

        array(
            'name' => 'compete_value',
            'type' => 'raw',
            'visible' => !isset($roles['Marketer']) && $currentaction == "published",
        ),

        array(
            'name' => 'islogin',
            'type' => 'raw',
            'value' => 'CHtml::checkBox("islogin[]", $data->islogin)',
            'visible' => !isset($roles['Marketer']) && $currentaction == "published",
        ),
        array(
            'name' => 'isip',
            'type' => 'raw',
            'value' => 'CHtml::checkBox("isip[]", $data->isip)',
            'visible' => !isset($roles['Marketer']) && $currentaction == "published",
        ),

        array(
            'name' => 'last_published',
            'type' => 'raw',
            'visible' => !isset($roles['Marketer']) && $currentaction == "published",
        ),

        /*
        'channel_str',
        'accept_tasktype_str',
        array(
            'name' => 'channel_id',
            'type' => 'raw',
            'value' => 'CHtml::encode(Utils::getValue(' . $chnlstr . ', $data->channel_id))',
            //'filter' => $_channels,
            //'filter' => CHtml::activeDropDownList($model, 'channel_id', $_channels, array('id'=>'Inventory_2nd_channel_id','multiple'=>true,'style'=>'width:180px;')),
        ),
        array(
            'name' => 'link_on_homepage',
            'type' => 'raw',
            'value' => '$data->link_on_homepage',
        ),
		'link_on_homepage',
        */
		/*
        'domain_id',
		'category_str',
		'channel_id',
		'category',
		'ext_backend_acct',
		'notes',
		'status',
		'created',
		'created_by',
		'modified',
		'modified_by',
		*/
		array(
			'class'=>'CButtonColumn',
            'template'=>isset($roles['Marketer']) ? '' : '{viewall} {note}{price}{view}{update}{delete}',
            //'visible'=>!isset($roles['Marketer']),
            'buttons' => array(
                'viewall' => array(
                    'label' => 'View IO History',
                    'visible' => $model->currentaction != 'acquired' ? 'true' : 'false',
                    'imageUrl'=>Yii::app()->theme->baseUrl.'/css/gridview/viewall.png',
                    'url' => 'Yii::app()->createUrl("ios/historic", array("domain_id"=>$data->domain_id,"desired_domain"=>$data->domain,"currentaction"=>'.$model->currentaction.'))',
                    'options' => array(
                        'class'=>'viewall',
                        'name'=>'viewallioh',
                    ),
                ),
                'note' => array(
                    'label' => 'Add Notes',
                    'imageUrl' => Yii::app()->theme->baseUrl.'/css/gridview/note.png',
                    'url' => 'Yii::app()->createUrl("domain/view", array("id"=>$data->domain_id))',
                    'options' => array('id'=>$this->evaluateExpression('$data->domain_id', array('data'=>$data))),
                    'click' => "function(){
                        addNote(this);
                        return false;
                    }",
                ),
                'price' => array(
                    'label' => 'Add Price',
                    'imageUrl' => Yii::app()->theme->baseUrl.'/css/gridview/dollar.png',
                    'url' => 'Yii::app()->createUrl("domain/view", array("id"=>$data->domain_id))',
                    'options' => array('id'=>$this->evaluateExpression('$data->domain_id', array('data'=>$data))),
                    'click' => "function(){
                        addPrice(this);
                        return false;
                    }",
                ),
            ),
            'htmlOptions'=>array('nowrap'=>'nowrap'),
		),
	),
)); ?>

<div class="clear"></div>

<!-- cart-form -->
<?php $this->renderPartial('_add2cart',array(
	'model'=>$model,
    'roles'=>$roles,
    'clients'=>$clients,
    'domains'=>$domains,
)); ?>
<!-- cart-form -->

<div id="hiddenContainer">
    <div id="alliohboxdiv" style="display:none;"></div>

    <div id="noteboxdiv" style="display:none;">
    </div>

    <div id="priceboxdiv" style="display:none;">
    </div>
</div>

<script type="text/javascript">
<!--
var lastclickid = 0;
$(document).ready(function(){
    $("#Inventory_category").multiselect({noneSelectedText:'Select Category',selectedList:3}).multiselectfilter();
    //$("#Inventory_channel_id").multiselect({noneSelectedText:'Select Channel',selectedList:5}).multiselectfilter();
    $("#Inventory_owner_channel_id").multiselect({noneSelectedText:'Select Owner',selectedList:5}).multiselectfilter();
    //$("#Inventory_accept_tasktype").multiselect({noneSelectedText:'Select Accept Type',selectedList:6}).multiselectfilter();
    //$("#Inventory_2nd_category").multiselect({noneSelectedText:'Select Category',selectedList:3}).multiselectfilter();
    //$("#Inventory_2nd_channel_id").multiselect({noneSelectedText:'Select Channel',selectedList:3}).multiselectfilter();

    $('#processing').bind("ajaxSend", function() {
        $(this).html("&nbsp;");
        $(this).css('background-image', 'url(' + "<?php echo Yii::app()->theme->baseUrl; ?>" + '/images/loading.gif)');
    }).bind("ajaxComplete", function() {
        $(this).css('background-image', '');
        $(this).css('color', 'red');
    });

    function hideAllContainer(){
        $("#alliohboxdiv").hide();
        $("#noteboxdiv").hide();
        $("#priceboxdiv").hide();
        $("#noteboxdiv").appendTo($("#hiddenContainer"));
        $("#priceboxdiv").appendTo($("#hiddenContainer"));
        $("#alliohboxdiv").appendTo($("#hiddenContainer"));
    }

    $.fn.yiiGridView.defaults.afterAjaxUpdate = function(){
        /*
        $("select[name^='owner_channel_id']").each(function(){
            var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
            var gvoffset = $(this).parent().parent().prevAll().length;
            var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
            var thistd = $(this);

            var ocivar = "oci" + currenttrid;
            $(this).attr("id", ocivar);
            $("#"+ocivar).multiselect({
                noneSelectedText:'Select Owner',selectedList:5, 
                beforeclose: function(event, ui){
                    // event handler here
                    alert($(this).val());
                    //alert(this.value);
                    //alert(this.options.length);
                }
            }).multiselectfilter();
        });
        */
        $("input[name^='islogin'],input[name^='isip']").each(function() {
            $(this).unbind('click').click(function(){
                //DO NOT REMOVE THIS LINE;
                hideAllContainer();

                var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
                var gvoffset = $(this).parent().parent().prevAll().length;
                var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
                var thistd = $(this);
                var currentvalue = $(this).is(':checked') ? 1 : 0;

                $.ajax({
                    'type': 'GET',
                    'dataType': 'json',
                    'url': "<?php echo CHtml::normalizeUrl(array('/inventory/setattr'));?>",
                    'data': 'id='+currenttrid+"&attrname="+$(this).attr('name')+"&attrvalue="+currentvalue,
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

        $("select[name^='category'],select[name^='owner_channel_id'],select[name^='client_id']").each(function(){
            var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
            var gvoffset = $(this).parent().parent().prevAll().length;
            var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
            var thistd = $(this);
            if ($(this).attr('name') == 'category[]') {
                var attrVar = "catid" + currenttrid;
                var sText = "Select Catgory";
            } else if ($(this).attr('name') == 'client_id[]') {
                var attrVar = "cid" + currenttrid;
                var sText = "Select Potential Client";
            } else {
                var attrVar = "ociid" + currenttrid;
                var sText = "Select Owner";
            }
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
                        'url': "<?php echo CHtml::normalizeUrl(array('/inventory/setattr'));?>",
                        'data': 'id='+currenttrid+"&attrname="+$(this).attr('name')+"&attrvalue="+$(this).val(),
                        'success':function(data){
                            //donothing for now;
                            if (data.success){
                                oldAttrValue = newAttrValue;
                                //alert(data.msg);
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

        $("select[name^='probability']").each(function() {
            var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
            var gvoffset = $(this).parent().parent().prevAll().length;
            var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
            var thistd = $(this);

            $(this).unbind('click').change(function(){
                $.ajax({
                    'type': 'GET',
                    'dataType': 'json',
                    'url': "<?php echo CHtml::normalizeUrl(array('/inventory/setattr'));?>",
                    'data': 'id='+currenttrid+"&attrname="+$(this).attr('name')+"&attrvalue="+this.value,
                    'success':function(data){
                        //donothing for now;
                        if (data.success){
                            //alert(data.msg);
                            $(thistd).css("background-color","yellow");
                        } else {
                            $(thistd).css("background-color","red");
                            alert(data.msg);
                        }
                    }
                });
            });
        });

        $("a.viewall[name='viewallioh']").each(function(){
            var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
            var gvoffset = $(this).parent().parent().prevAll().length;
            var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
            var thistd = $(this);

            $(this).unbind('click').click(function(){
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
        $("div.pager > ul > li a, table.items thead tr.filters input, table.items thead tr.filters select").each(function(){
            $(this).click(function() {
                hideAllContainer();
            });
            return true;
        });

        $("table.items #pageSize").change(function(){
            hideAllContainer();
            return true;
        });

        $("#searchButton").click(function(){
            hideAllContainer();
            return true;
        });

        var _ids = [];
        $('#inventory-grid > table.items a.price').each(function(i){
            _ids[i]=$(this).attr("href").replace(/([\s\S]*?)(id=)(\d+)(&)?/g,'$3');
            //console.log(_ids);
        });
        $("#inventory-grid > table.items > tbody > tr").each(function(i){
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
            'url': "<?php echo Yii::app()->createUrl('/domainPrice/icon');?>",
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


    $("#add2cart").click(function(){
        var rtn = false;
        var ivtids = new Array;
        var idsidx = new Array;

        $("input[name='ids[]'][type='checkbox']:checked").each(function(i) {
            rtn = true;
            ivtids[i] = $(this).val();
            idsidx[i] = $(this).attr("id");
        });

        if (!rtn){
            $("#add2carterror > ul").html("<li>Please choose one inventory at least.</li>");
        } else {
            $("#Cart_inventory_ids").val(ivtids.join(","));
        }

        if (rtn){
            $.each(["client_id","client_domain_id"], function(nt,vt) {  
                //alert($("#Task_"+vt).val());
                if ($("#Cart_"+vt).val() == "") {
                    rtn = false;
                    $("#add2carterror > ul").html("<li>Please choose the options from the dropdown list.</li>");
                    $("#Cart_"+vt).focus();
                    $("#Cart_"+vt).css("background-color","red");

                    //in the $.each(), you should use the "return false" instead of "break", and "reture true" means continue;
                    return false;
                }
            });
        }

        if (!rtn) $("#add2carterror").show();
        //###//return rtn;

        $.ajax({
            'type': 'POST',
            'dataType': 'json',
            'url': "<?php echo Yii::app()->createUrl('cart/add');?>",
            'data': $("#addDomain2CartForm").serialize(),
            'success':function(data){
                //donothing for now;
                if (data.success){
                    alert(data.msg);
                    if (data.ids){
                        //$.each(data.ids, function(i, v){
                        //});
                        $.each(idsidx, function(i, v){
                            $("#"+v).attr('checked', false);
                            //$("#"+v).parent().next().next().addClass("linethrough");
                            $("#"+v).parent().parent().addClass("linethrough");
                        });
                    }
                } else {
                    alert(data.msg);
                }
            },
            'complete':function(XHR,TS){XHR = null;}
        });
    });

    $("#downloadInventory").click(function(){
        var url = "<?php echo Yii::app()->createUrl('/download/inventory');?>";
        var rparent = $('#inventorySearchForm input[type=hidden][name=r]').parent();
        var rself = rparent.html();
        $('#inventorySearchForm input[type=hidden][name=r]').remove();
        //alert($('#inventorySearchForm').serialize());
        url = url + "&" + $('#inventorySearchForm').serialize();
        rparent.html(rself);
        window.location.href = url;
    });

    $.fn.yiiGridView.defaults.afterAjaxUpdate();
});

/*
function checknoflinktask() {
    var ivtids = [];
    $("input[name='ids[]'][type='checkbox']").each(function(i) {
        ivtids[i] = $(this).val();
    });

    $.ajax({
        'type': 'POST',
        'dataType': 'json',
        'url': "<?php echo Yii::app()->createUrl('/link/noft');?>",
        'data': {'ivtids[]': ivtids},
        'success':function(data){
            //alert(data.msg);
            if (data.success){
                data.noftasks.each(function(de){
                    //alert(de.inventory_id);
                    var apphtml = " - <strong style='color:red'>(<a href='/inventories/linkbuildingtasks?with_sourceurl=without&inventory_id="+ de.inventory_id+"' target='_blank'>"+de.count+"</a>)</strong>";
                    $('#etr' + de.inventory_id).children("td:eq(1)").append(apphtml);
                });
            }
        },
        'complete':function(XHR,TS){XHR = null;}
    });
}
//checknoflinktask();
*/

function addNote(t) {
    var gvid = $(t).parent().parent().closest('.grid-view').attr('id');
    var gvoffset = $(t).parent().parent().prevAll().length;
    var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
    var currentdomain = $(t).parent().parent().children("td:eq(2)").text();

    var ahref = $(t).attr('href');
    var domain_id=ahref.replace(/([\s\S]*?)(id=)(\d+)(&)?/g,'$3');
    $("#alliohboxdiv").hide();
    $("#priceboxdiv").hide();
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
            'data': 'domain_id='+domain_id+"&ajax=true",
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
            var vartr = $('<tr><td colspan="14"></td></tr>').attr({ 'id': currenttrid+"_dtr", 'class':"bltr"});
            //$("#noteboxdiv").appendTo(vartr.find("td"));
            $("#noteboxdiv").appendTo(vartr.children("td"));
            $(t).parent().parent().after(vartr);
            $('#'+gvid+' > div.keys > span:eq('+gvoffset+')').after("<span>"+currenttrid+"_dtr</span>");
        }

    } else {}

    lastclickid = currenttrid;
    return false;
}

function addPrice(t) {
    var gvid = $(t).parent().parent().closest('.grid-view').attr('id');
    var gvoffset = $(t).parent().parent().prevAll().length;
    var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
    var currentdomain = $(t).parent().parent().children("td:eq(2)").text();

    var ahref = $(t).attr('href');
    var domain_id=ahref.replace(/([\s\S]*?)(id=)(\d+)(&)?/g,'$3');

    $("#alliohboxdiv").hide();
    $("#noteboxdiv").hide();
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
            'url': "<?php echo Yii::app()->createUrl('/domain/price');?>",
            'data': 'domain_id='+domain_id+"&ajax=true",
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
            var vartr = $('<tr><td colspan="14"></td></tr>').attr({ 'id': currenttrid+"_dtr", 'class':"bltr"});
            //$("#noteboxdiv").appendTo(vartr.find("td"));
            $("#priceboxdiv").appendTo(vartr.children("td"));
            $(t).parent().parent().after(vartr);
            $('#'+gvid+' > div.keys > span:eq('+gvoffset+')').after("<span>"+currenttrid+"_dtr</span>");
        }

    } else {}

    lastclickid = currenttrid;
    return false;
}

//-->
</script>