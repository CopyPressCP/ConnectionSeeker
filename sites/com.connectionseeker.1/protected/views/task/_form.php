<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'task-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'inventory_id'); ?>
		<?php echo $form->textField($model,'inventory_id'); ?>
		<?php echo $form->error($model,'inventory_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'campaign_id'); ?>
		<?php echo $form->textField($model,'campaign_id'); ?>
		<?php echo $form->error($model,'campaign_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'domain'); ?>
		<?php echo $form->textField($model,'domain',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'domain'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'domain_id'); ?>
		<?php echo $form->textField($model,'domain_id',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'domain_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'anchortext'); ?>
		<?php echo $form->textArea($model,'anchortext',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'anchortext'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'targeturl'); ?>
		<?php echo $form->textField($model,'targeturl',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'targeturl'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'sourceurl'); ?>
		<?php echo $form->textField($model,'sourceurl',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'sourceurl'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'sourcedomain'); ?>
		<?php echo $form->textField($model,'sourcedomain',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'sourcedomain'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textArea($model,'title',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'title'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'tasktype'); ?>
		<?php echo $form->textField($model,'tasktype'); ?>
		<?php echo $form->error($model,'tasktype'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'taskstatus'); ?>
		<?php echo $form->textField($model,'taskstatus',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'taskstatus'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'assignee'); ?>
		<?php echo $form->textField($model,'assignee'); ?>
		<?php echo $form->error($model,'assignee'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'optional_keywords'); ?>
		<?php echo $form->textArea($model,'optional_keywords',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'optional_keywords'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'mapping_id'); ?>
		<?php echo $form->textField($model,'mapping_id',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'mapping_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'notes'); ?>
		<?php echo $form->textArea($model,'notes',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'notes'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'duedate'); ?>
		<?php echo $form->textField($model,'duedate'); ?>
		<?php echo $form->error($model,'duedate'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'content_article_id'); ?>
		<?php echo $form->textField($model,'content_article_id'); ?>
		<?php echo $form->error($model,'content_article_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'content_campaign_id'); ?>
		<?php echo $form->textField($model,'content_campaign_id'); ?>
		<?php echo $form->error($model,'content_campaign_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'content_category_id'); ?>
		<?php echo $form->textField($model,'content_category_id'); ?>
		<?php echo $form->error($model,'content_category_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'send2cpdate'); ?>
		<?php echo $form->textField($model,'send2cpdate'); ?>
		<?php echo $form->error($model,'send2cpdate'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'checkouted'); ?>
		<?php echo $form->textField($model,'checkouted'); ?>
		<?php echo $form->error($model,'checkouted'); ?>
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

	<div class="row">
		<?php echo $form->labelEx($model,'modified'); ?>
		<?php echo $form->textField($model,'modified'); ?>
		<?php echo $form->error($model,'modified'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'modified_by'); ?>
		<?php echo $form->textField($model,'modified_by'); ?>
		<?php echo $form->error($model,'modified_by'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->