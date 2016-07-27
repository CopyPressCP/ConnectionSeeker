<?php
$this->breadcrumbs=array(
	'Domains'=>array('index'),
	'Audit',
);

$cs=Yii::app()->clientScript;
$cs->registerCoreScript( 'jquery.ui' );
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/css/gridview/styles.css');
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/css/gridview/jquery.yiigridview.js', CClientScript::POS_END);

$loadingimg = "<img src='".Yii::app()->theme->baseUrl."/css/gridview/loading.gif' />";

?>

<p>
You may optionally enter some sample domains there, the "|" will seperate the sample root domain and the competitors domain, the comma will seperates the competitors domains. the command will be like: <b>sample.com|competitor1.com,competitor2.com,competitor3.com</b>
</p>


<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
    <div id="errorSummary"></div>
    <div class="clear"></div>

    <div style="float:left;width:170px;"><h2>Domains Audit:</h2></div>
    <div style="float:left;width:520px;">
      <?php echo $form->textField($model,'domain',array('size'=>160,'style'=>"width:480px;")); ?>
    </div>
    <div style="float:left;width:120px;padding-top:10px">
      <?php echo Yii::t('Domain', 'Use Historic');?>
      <?php echo CHtml::checkBox('Domain[use_historic_index]', array('class'=>'chkbox','id'=>'use_historic_index')); ?>
    </div>
	<div class="row buttons" style="float:left;width:120px;">
        <?php echo CHtml::submitButton('Explore', array('id' => 'explore', 'style'=>'width:120px;', 'type' => 'submit', 'value' => 'Explore')); ?>
	</div>
<?php $this->endWidget(); ?>

    <div style="float:left;width:10px;">&nbsp;</div>

    <?php //if ($dlvisible) { ?>
	<div class="row buttons" style="padding:5px;display:block;">
        <?php echo CHtml::link('Download', array("/backlink/dlaudit", "domain"=>$model->domain, "datasource"=>$_GET['Domain']['use_historic_index']), 
        array('class'=>'linkbutton', 'id'=>'downloadaudit', 'value' => 'Download')); ?>
	</div>
    <?php //}?>

    <div class="clear"></div>
</div>



<div style="clear:both"></div>

<div class="grid-view" id="domain-grid">
<table class="items">
<thead>
<tr>
  <th><?php echo Yii::t('Domain', 'Domain');?></th>
  <th><?php echo Yii::t('Domain', 'ExtBacklinks');?></th>
  <th><?php echo Yii::t('Domain', 'RefDomains');?></th>
  <th><?php echo Yii::t('Domain', 'Indexed');?></th>
  <th><?php echo Yii::t('Domain', 'Avg monthly backlink');?></th>
  <th><?php echo Yii::t('Domain', 'Top 10 achor texts');?></th>
  <th><?php echo Yii::t('Domain', 'Backlinks with ac rank 1+');?></th>
  <th><?php echo Yii::t('Domain', 'Backlinks with ac rank 1-4');?></th>
  <th><?php echo Yii::t('Domain', 'Backlinks with ac rank 5+');?></th>
  <th><?php echo Yii::t('Domain', '%. Of quality links');?></th>
  <th><?php echo Yii::t('Domain', 'Highest Backlink AC Rank');?></th>
  <th><?php echo Yii::t('Domain', 'Avg AC Rank of Backlink with AC Rank 1+');?></th>
  <th></th>
</thead>
<tbody>
<?php
if ($result) {
    $i = 0;
    $span = "";
    $dwimg = "<img src='".Yii::app()->theme->baseUrl."/images/download.png' />";
    foreach ($result as $kr => $vr) {
        $span .= "<span>$kr</span>";
    ?>
<tr class="<?php echo ($i % 2 == 0) ? "even":'odd';?>">
    <td><?php echo CHtml::link($kr, array("backlink/index", "competitor" =>$kr, "datasource" => $vr['datasource']));?></td>
    <td><?php echo $vr['extbacklinks'];?></td>
    <td><?php echo $vr['refdomains'];?></td>
    <td><?php echo $vr['indexedurls'];?></td>
    <td><?php echo $vr['avghis'];?></td>
    <td><?php echo $loadingimg;?></td>
    <td><?php echo $loadingimg;?></td>
    <td><?php echo $loadingimg;?></td>
    <td><?php echo $loadingimg;?></td>
    <td><?php echo $loadingimg;?></td>
    <td><?php echo $loadingimg;?></td>
    <td><?php echo $loadingimg;?></td>
    <td><div style="display:none;"><?php echo CHtml::link($dwimg, array("backlink/download", "competitor" =>$kr, "datasource" => $vr['datasource']));?></div></td>
</tr>
<?php
        $i++;
    }
} else {?>
<tr class=""><td colspan="12"><span class="empty">No results found.</span></td></tr>
<?php } ?>
</tbody>
</table>
<div title="<?php echo Yii::app()->createUrl($this->route);?>" style="display:none" class="keys">
<?php echo $span;?>
</div>

</div>

<script type="text/javascript">
$(document).ready(function() {
    var erri = 0;
    $("#domain-grid > div.keys > span").each(function(){
        //alert($(this).text());
        var gvoffset = $(this).prevAll().length;
        var currtr = $('#domain-grid .items > tbody > tr:eq('+gvoffset+')');
        //currtr.children("td:eq(4)").html("leo");
        //alert(currtd);

        $.ajax({
            'type': 'GET',
            'dataType': 'json',
            //'url': "<?php echo CHtml::normalizeUrl(array('/domain/topbacklinks'));?>",
            'url': "<?php echo Yii::app()->createUrl('/domain/topbacklinks');?>",
            'data': 'domain='+$(this).text()+'&datasource='+$("#Domain_use_historic_index").val(),
            'success':function(data){
                //alert(data.success);
                if (data.success == true) {
                    //currtr.children("td:eq(4)").html("leo");
                    currtr.children("td:eq(5)").html(data.audit.toptext);
                    currtr.children("td:eq(6)").html(data.audit.ac1);
                    currtr.children("td:eq(7)").html(data.audit.ac1to4);
                    currtr.children("td:eq(8)").html(data.audit.ac5);
                    currtr.children("td:eq(9)").html(data.audit.quality);
                    currtr.children("td:eq(10)").html(data.audit.acmax);
                    currtr.children("td:eq(11)").html(data.audit.acavg);
                    currtr.children("td:eq(12)").children("div").css("display","inline");
                } else {
                    if (erri == 0){
                        $("#errorSummary").attr("class", "errorSummary");
                        $("#errorSummary").html("<p>Please fix the following input errors:</p><ul></ul>");
                        erri = 1;
                    }
                    $("#errorSummary > ul").append("<li>"+data.msg+"</li>");
                    //alert(data.msg);
                }
            }
        });

    });

    $("#downloadaudit").click(function(){
        if (($.trim(dyndomains)).length > 3) {
            $.ajax({
                'type': 'GET',
                'dataType': 'json',
                'url': "<?php echo CHtml::normalizeUrl(array('/backlink/dlaudit'));?>",
                'data': 'domain='+dyndomains+'&datasource='+dyndatasource,
                'success':function(data){
                    //do nothing;
                }
            });
        } else {
            alert("Please type the correct domains into the box.");
        }
    });


});
</script>

<style type="text/css">
.grid-view table.items th{background-image:none;}
.grid-view table.items tr.even td { background-image: none;}
.grid-view table.items tr.odd td { background-image: none;}
#downloadaudit{height:20px;width:80px;padding:13px 14px 3px 25px;color:#fff;border:2px solid #CCCCCC;border-bottom:5px solid rgba(0, 0, 0, 0.25);}
</style>