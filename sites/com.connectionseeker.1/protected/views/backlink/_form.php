<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'backlink-form',
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
		<?php echo $form->textField($model,'domain_id',array('size'=>20,'maxlength'=>20)); ?>
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

	<div class="row">
		<?php echo $form->labelEx($model,'url'); ?>
		<?php echo $form->textField($model,'url',array('size'=>60,'maxlength'=>2048)); ?>
		<?php echo $form->error($model,'url'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'domain'); ?>
		<?php echo $form->textField($model,'domain',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'domain'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'googlepr'); ?>
		<?php echo $form->textField($model,'googlepr'); ?>
		<?php echo $form->error($model,'googlepr'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'acrank'); ?>
		<?php echo $form->textField($model,'acrank'); ?>
		<?php echo $form->error($model,'acrank'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'anchortext'); ?>
		<?php echo $form->textField($model,'anchortext',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'anchortext'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'date'); ?>
		<?php echo $form->textField($model,'date'); ?>
		<?php echo $form->error($model,'date'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'flagredirect'); ?>
		<?php echo $form->textField($model,'flagredirect'); ?>
		<?php echo $form->error($model,'flagredirect'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'flagframe'); ?>
		<?php echo $form->textField($model,'flagframe'); ?>
		<?php echo $form->error($model,'flagframe'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'flagnofollow'); ?>
		<?php echo $form->textField($model,'flagnofollow'); ?>
		<?php echo $form->error($model,'flagnofollow'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'flagimages'); ?>
		<?php echo $form->textField($model,'flagimages'); ?>
		<?php echo $form->error($model,'flagimages'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'flagdeleted'); ?>
		<?php echo $form->textField($model,'flagdeleted'); ?>
		<?php echo $form->error($model,'flagdeleted'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'flagalttext'); ?>
		<?php echo $form->textField($model,'flagalttext'); ?>
		<?php echo $form->error($model,'flagalttext'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'flagmention'); ?>
		<?php echo $form->textField($model,'flagmention'); ?>
		<?php echo $form->error($model,'flagmention'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'targeturl'); ?>
		<?php echo $form->textField($model,'targeturl',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'targeturl'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->