<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'discovery-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'competitor_id'); ?>
		<?php echo $form->textField($model,'competitor_id'); ?>
		<?php echo $form->error($model,'competitor_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'domain_id'); ?>
		<?php echo $form->textField($model,'domain_id'); ?>
		<?php echo $form->error($model,'domain_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'fresh_called'); ?>
		<?php echo $form->textField($model,'fresh_called'); ?>
		<?php echo $form->error($model,'fresh_called'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'historic_called'); ?>
		<?php echo $form->textField($model,'historic_called'); ?>
		<?php echo $form->error($model,'historic_called'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->