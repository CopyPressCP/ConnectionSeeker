<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'blogger-program-note-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'blogger_program_id'); ?>
		<?php echo $form->textField($model,'blogger_program_id',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'blogger_program_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'domain_id'); ?>
		<?php echo $form->textField($model,'domain_id',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'domain_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'notes'); ?>
		<?php echo $form->textArea($model,'notes',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'notes'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'isprivate'); ?>
		<?php echo $form->textField($model,'isprivate'); ?>
		<?php echo $form->error($model,'isprivate'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'created'); ?>
		<?php echo $form->textField($model,'created'); ?>
		<?php echo $form->error($model,'created'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'created_by'); ?>
		<?php echo $form->textField($model,'created_by'); ?>
		<?php echo $form->error($model,'created_by'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->