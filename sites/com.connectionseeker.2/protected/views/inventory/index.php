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

$types = Types::model()->actived()->findAll("type='site' OR type='category' OR type='channel' OR type='linktask'");
$gtps = CHtml::listData($types, 'refid', 'typename', 'type');
//extract($gtps);//doNot use extract here,cause the name $site, $category,... was too simple, it is easy overwrite by accidently
$_stypes = $gtps['site'] ? $gtps['site'] : array();
$_linktasks = $gtps['linktask'] ? $gtps['linktask'] : array();
//print_r($_stypes);
$_categories = $gtps['category'] ? $gtps['category'] : array();
$_channels = $gtps['channel'] ? $gtps['channel'] : array();
$chnlstr = Utils::array2String(array("" => '[Channel]') + $_channels);
$stypestr = Utils::array2String($_stypes);
//print_r($gtps);
$_status = array('0'=>'Inactive','1'=>'Active');


$uinfo = User::model()->findByPk(Yii::app()->user->id);
if (isset($roles['Marketer'])) {
    $domains = ClientDomain::model()->byduty()->findAll('client_id=:client_id', array(':client_id'=>$uinfo->client_id));
    $domains = CHtml::listData($domains,'id','domain');
    $clients = $uinfo->client_id;
} else {
    $clients = Client::model()->actived()->findAll();
    $domains = array();
}
?>

<h1>Manage Inventories</h1>

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
	'dataProvider'=>$model->search(),
	//'filter'=>$model,
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
            'name' => 'rdomain.stype',
            'type' => 'raw',
            'value' => 'CHtml::encode(Utils::getValue(' . $stypestr . ', $data->rdomain->stype))',
        ),
		'rdomain.googlepr',
        array(
            'header' => 'Moz Rank',
            'name' => 'rdomain.rsummary.mozrank',
            'type' => 'raw',
            'value' => 'round($data->rdomain->rsummary->mozrank)',
        ),
        array(
            'name' => 'rdomain.rsummary.mozauthority',
            'type' => 'raw',
            'value' => 'round($data->rdomain->rsummary->mozauthority)',
        ),
		'rdomain.alexarank:number',
		'rdomain.linkingdomains:number',
		'rdomain.inboundlinks:number',
        /*
		'domain',
		'rdomain.alexarank',
		'rdomain.linkingdomains',
		'rdomain.inboundlinks',
        array(
            'name' => 'rdomain.alexarank',
            'value'=>'number_format($data->rdomain->alexarank)',
        ),
        array(
            'name' => 'rdomain.linkingdomains',
            'value'=>'number_format($data->rdomain->linkingdomains)',
        ),
        array(
            'name' => 'rdomain.inboundlinks',
            'value'=>'number_format($data->rdomain->inboundlinks)',
        ),
        */

        //'rdomain.age',
        array(
            'name' => 'rdomain.onlinesince',
            'type' => 'raw',
            'value' => 'CHtml::encode((($data->rdomain->onlinesince - 658454400) > 0) ? date("Y-m-d", $data->rdomain->onlinesince) : "-1")',
            'header' => 'Age',
        ),
        'category_str',
        array(
            'name' => 'channel_str',
            'type' => 'raw',
            'value' => 'CHtml::encode($data->channel_str)',
            'visible' => !isset($roles['Marketer']),
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
            'template'=>isset($roles['Marketer']) ? '' : '{view}{update}{delete}',
            //'visible'=>!isset($roles['Marketer']),
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


<script type="text/javascript">
<!--
$(document).ready(function(){
    $("#Inventory_category").multiselect({noneSelectedText:'Select Category',selectedList:3}).multiselectfilter();
    $("#Inventory_channel_id").multiselect({noneSelectedText:'Select Channel',selectedList:5}).multiselectfilter();
    //$("#Inventory_accept_tasktype").multiselect({noneSelectedText:'Select Accept Type',selectedList:6}).multiselectfilter();
    //$("#Inventory_2nd_category").multiselect({noneSelectedText:'Select Category',selectedList:3}).multiselectfilter();
    //$("#Inventory_2nd_channel_id").multiselect({noneSelectedText:'Select Channel',selectedList:3}).multiselectfilter();

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
});

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
//-->
</script>