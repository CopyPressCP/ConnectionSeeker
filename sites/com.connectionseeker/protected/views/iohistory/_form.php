<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'iohistory-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'task_id'); ?>
		<?php echo $form->textField($model,'task_id'); ?>
		<?php echo $form->error($model,'task_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'oldiostatus'); ?>
		<?php echo $form->textField($model,'oldiostatus'); ?>
		<?php echo $form->error($model,'oldiostatus'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'iostatus'); ?>
		<?php echo $form->textField($model,'iostatus'); ?>
		<?php echo $form->error($model,'iostatus'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'timeline'); ?>
		<?php echo $form->textField($model,'timeline'); ?>
		<?php echo $form->error($model,'timeline'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'role'); ?>
		<?php echo $form->textField($model,'role',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'role'); ?>
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