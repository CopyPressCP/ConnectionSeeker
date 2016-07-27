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
		<?php echo $form->labelEx($model,'channel_id'); ?>
		<?php echo $form->textField($model,'channel_id',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'channel_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'desired_domain'); ?>
		<?php echo $form->textField($model,'desired_domain',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'desired_domain'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'rewritten_title'); ?>
		<?php echo $form->textField($model,'rewritten_title',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'rewritten_title'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'blog_title'); ?>
		<?php echo $form->textField($model,'blog_title',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'blog_title'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'blog_url'); ?>
		<?php echo $form->textField($model,'blog_url',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'blog_url'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'progressstatus'); ?>
		<?php echo $form->textField($model,'progressstatus',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'progressstatus'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'iostatus'); ?>
		<?php echo $form->textField($model,'iostatus',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'iostatus'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'qa_comments'); ?>
		<?php echo $form->textArea($model,'qa_comments',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'qa_comments'); ?>
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


	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->