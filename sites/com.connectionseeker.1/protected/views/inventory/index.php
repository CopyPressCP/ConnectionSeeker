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
?>

<h1>Manage Inventories</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<!-- search-form -->
<?php $this->renderPartial('_search',array(
	'model'=>$model,
	'_stypes'=>$_stypes,
	'_categories'=>$_categories,
	'_channels'=>$_channels,
	'_status'=>$_status,
	'_linktasks'=>$_linktasks,
)); ?>
<!-- search-form -->


<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'inventory-grid',
	'dataProvider'=>$model->search(),
	//'filter'=>$model,
    'selectableRows' => '2',

	'columns'=>array(
        array(
            'id'=>'ids',
            'class'=>'CCheckBoxColumn',
        ),
		'id',
		'domain',
        array(
            'name' => 'rdomain.stype',
            'type' => 'raw',
            'value' => 'CHtml::encode(Utils::getValue(' . $stypestr . ', $data->rdomain->stype))',
        ),
		'rdomain.googlepr',
		'rdomain.alexarank',
		'rdomain.linkingdomains',
		'rdomain.inboundlinks',
		//'rdomain.age',
        array(
            'name' => 'rdomain.age',
            'type' => 'raw',
            'value' => 'CHtml::encode((($data->rdomain->onlinesince - 658454400) > 0) ? date("Y-m-d", $data->rdomain->onlinesince) : "-1")',
        ),
        'category_str',
        'accept_tasktype_str',
        array(
            'name' => 'channel_id',
            'type' => 'raw',
            'value' => 'CHtml::encode(Utils::getValue(' . $chnlstr . ', $data->channel_id))',
            //'filter' => $_channels,
            //'filter' => CHtml::activeDropDownList($model, 'channel_id', $_channels, array('id'=>'Inventory_2nd_channel_id','multiple'=>true,'style'=>'width:180px;')),
        ),
        /*
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
		),
	),
)); ?>

<div class="clear"></div>

<!-- search-form -->
<?php $this->renderPartial('_add2task',array(
	'model'=>$model,
	'_linktasks'=>$_linktasks,
	'_categories'=>$_categories,
)); ?>
<!-- search-form -->


<script type="text/javascript">
<!--
$(document).ready(function(){
    $("#Inventory_category").multiselect({noneSelectedText:'Select Category',selectedList:3}).multiselectfilter();
    $("#Inventory_channel_id").multiselect({noneSelectedText:'Select Channel',selectedList:5}).multiselectfilter();
    $("#Inventory_accept_tasktype").multiselect({noneSelectedText:'Select Accept Type',selectedList:6}).multiselectfilter();
    //$("#Inventory_2nd_category").multiselect({noneSelectedText:'Select Category',selectedList:3}).multiselectfilter();
    //$("#Inventory_2nd_channel_id").multiselect({noneSelectedText:'Select Channel',selectedList:3}).multiselectfilter();

    $("#add2task").click(function(){
        var rtn = false;
        var ivtids = new Array;

        $("input[name='ids[]'][type='checkbox']:checked").each(function(i) {
            rtn = true;
            ivtids[i] = $(this).val();
        });

        if (!rtn){
            $("#add2taskerror > ul").html("<li>Please choose one inventory at least.</li>");
        } else {
            $("#Task_inventory_ids").val(ivtids.join(","));
        }

        if (rtn){
            $.each(["client_id","campaign_id","tasktype","content_category_id"], function(nt,vt) {  
                //alert($("#Task_"+vt).val());
                if ($("#Task_"+vt).val() == "") {
                    rtn = false;
                    $("#add2taskerror > ul").html("<li>Please choose the options from the dropdown list.</li>");
                    $("#Task_"+vt).focus();
                    $("#Task_"+vt).css("background-color","red");

                    //in the $.each(), you should use the "return false" instead of "break", and "reture true" means continue;
                    return false;
                }
            });
        }

        if (!rtn) $("#add2taskerror").show();

        return rtn;
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
        }
    });
}
//checknoflinktask();
//-->
</script>