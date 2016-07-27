<?php
$types = array(
    'ip'=>'Black IPAddress',
    'domain'=>'Black Domain',
    'keyword'=>'Black Keyword',
);

$chl = array();
$channels = Types::model()->actived()->bytype('channel')->findAll();
if ($channels) $chl = CHtml::listData($channels, 'refid', 'typename');
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'blacklist-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'type'); ?>
        <?php echo $form->dropDownList($model, 'type', $types, array('prompt'=>'-- Select --')); ?>
		<?php echo $form->error($model,'type'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'blackvalue'); ?>
		<?php echo $form->textField($model,'blackvalue',array('size'=>60,'maxlength'=>500)); ?>
		<?php echo $form->error($model,'blackvalue'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'channel_id'); ?>
		<?php echo $form->textField($model,'channel_id'); ?>
		<?php echo $form->error($model,'channel_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'channel_id'); ?>
        <?php echo $form->dropDownList($model, 'channel_id', $chl, array('prompt'=>'-- Select --')); ?>
		<?php echo $form->error($model,'channel_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'notes'); ?>
		<?php echo $form->textArea($model,'notes',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'notes'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->