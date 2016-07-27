<?php
//print_r($types);

$actions = array(
    //"1"=>"Upload technorati domains into system",
    //"2"=>"Upload AWIS domains into system",
    "3"=>"Bulk upload domains",
    //##"4"=>"Bulk update Primary Email & Owner",
    "4"=>"Bulk Upload Domains with metrics (Dangerous:It will overwrite all of the metrics)",
    "5"=>"Bulk Upload Domain with metrics (If domain exist, then overwrite the null value metrics only)",
    //##"6"=>"Bulk upload domains with metrics (If domain exist, then overwrite the metrics)",
);

$cs=Yii::app()->clientScript;
$cs->registerCoreScript( 'jquery.ui' );

$types = Types::model()->actived()->bytype(array("site","outreach","category"))->findAll();
$gtps = CHtml::listData($types, 'refid', 'typename', 'type');

$stypes = $gtps['site'] ? $gtps['site'] : array();
$otypes = $gtps['outreach'] ? $gtps['outreach'] : array();
$categories = $gtps['category'] ? $gtps['category'] : array();
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("domain/upload"),
	'method'=>'post',
    'htmlOptions'=>array('enctype'=>'multipart/form-data'),
	'id'=>'domain-form',
	'enableAjaxValidation'=>false,
)); ?>
    <h2>Upload File (Support CSV, Excel2005, Excel2007, Gnumeric and Openoffice Calc for now)</h2>

	<p class="note">Fields with <span class="required">*</span> are required.</p>
	<?php echo $form->errorSummary($model); ?>

	<div class="row">
        <?php echo CHtml::label('What you wanna do?', 'Domain_action');?>

        <?php echo CHtml::dropDownList('Domain[action]', $_GET['Domain']['action'], $actions, array('style'=>'width:430px;')); ?>
        <div style="float:right" id="innermenu"><ul class="actions">
            <li><a href="#" target="_blank" id="downloadtpl">Download Template</a></li>
         </ul></div>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'stype'); ?>
        <?php echo $form->dropDownList($model, 'stype', $stypes,array('prompt'=>'-- Select --')); ?>
		<?php echo $form->error($model,'stype'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'otype'); ?>
        <?php echo $form->dropDownList($model, 'otype', $otypes,array('prompt'=>'-- Select --')); ?>
		<?php echo $form->error($model,'otype'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'category'); ?>
        <?php echo $form->dropDownList($model, 'category', $categories,array('prompt'=>'-- Select --')); ?>
		<?php echo $form->error($model,'category'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'touched_status'); ?>
        <?php echo $form->dropDownList($model, 'touched_status', Domain::$status, array('prompt'=>'-- Select --')); ?>
		<?php echo $form->error($model,'touched_status'); ?>
	</div>

	<div class="row">
        <?php echo CHtml::label('Import from local file', 'Domain_upfile');?>

        <?php echo CHtml::activeFileField($model, 'upfile', array('style'=>'height:30px;'));?>

	</div>

	<div class="clear"></div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Upload', array("id"=>"dupload")); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->

<script type="text/javascript">
$(document).ready(function() {
    $("#dupload").click(function(){
        if ($("#Domain_upfile").val()) {
            return true;
        } else {
            alert("Please choose an import file");
            return false;
        }
    });
    $("#Domain_action").change(function(){
        if($("#Domain_action").val() == 4) {
            $("#Domain_stype").attr('disabled', 'disabled');
            $("#Domain_otype").attr('disabled', 'disabled');
            $("#Domain_category").attr('disabled', 'disabled');
            $("#Domain_touched_status").attr('disabled', 'disabled');
        } else {
            $("#Domain_stype").removeAttr('disabled');
            $("#Domain_otype").removeAttr('disabled');
            $("#Domain_category").removeAttr('disabled');
            $("#Domain_touched_status").removeAttr('disabled');
        }
    });

    $("#downloadtpl").click(function(){
        var tplid = $("#Domain_action").val();
        var tplhref = "assets/upload-template"+tplid+".xls";
        //alert(tplhref);
        window.location.href=tplhref;
        return false;
    });
});
</script>
