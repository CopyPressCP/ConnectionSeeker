<?php
//print_r($types);

$actions = array(
    "1"=>"Upload technorati domains into system",
    "2"=>"Upload AWIS domains into system",
    "3"=>"Bulk upload domains",
);

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
		<?php echo CHtml::submitButton('Upload'); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->
