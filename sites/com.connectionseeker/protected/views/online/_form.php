<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'online-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'user_id'); ?>
		<?php echo $form->textField($model,'user_id'); ?>
		<?php echo $form->error($model,'user_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'date_tracked'); ?>
		<?php echo $form->textField($model,'date_tracked'); ?>
		<?php echo $form->error($model,'date_tracked'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'total_online'); ?>
		<?php echo $form->textField($model,'total_online'); ?>
		<?php echo $form->error($model,'total_online'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'login_time'); ?>
		<?php echo $form->textField($model,'login_time'); ?>
		<?php echo $form->error($model,'login_time'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'session_online'); ?>
		<?php echo $form->textField($model,'session_online'); ?>
		<?php echo $form->error($model,'session_online'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'last_operation_time'); ?>
		<?php echo $form->textField($model,'last_operation_time'); ?>
		<?php echo $form->error($model,'last_operation_time'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->