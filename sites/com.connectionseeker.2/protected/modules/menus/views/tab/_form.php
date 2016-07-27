<div class="form span-12 first">

<?php if( $model->scenario==='update' ): ?>

	<h3><?php echo Menus::getMenuTypeName($model->type); ?></h3>

<?php endif; ?>
	
<?php $form=$this->beginWidget('CActiveForm'); ?>
<?php
          echo $form->hiddenField($model, 'parent_id'); 
          echo $form->hiddenField($model, 'status'); 
          echo $form->hiddenField($model, 'is_tab'); 
          echo $form->hiddenField($model, 'type'); 
  ?>
	<div class="row">
		<?php echo $form->labelEx($model, 'name'); ?>
		<?php echo $form->textField($model, 'name', array('maxlength'=>255, 'class'=>'text-field')); ?>
		<?php echo $form->error($model, 'name'); ?>
		<p class="hint"><?php echo Menus::t('core', 'A name for this item'); ?></p>
	</div>
    <?php if ($model->id): ?>
	<div class="row">
		<?php echo $form->labelEx($model, 'itemname'); ?>
		<?php echo $form->textField($model, 'itemname', array('maxlength'=>255, 'class'=>'text-field')); ?>
		<?php echo $form->error($model, 'itemname'); ?>
		<p class="hint"><?php echo Menus::t('core', 'Do not change the name unless you know what you are doing.'); ?></p>
	</div>
    <?php endif;?>
	<div class="row">
		<?php echo $form->labelEx($model, 'url'); ?>
		<?php echo $form->textField($model, 'url', array('maxlength'=>255, 'class'=>'text-field')); ?>
		<?php echo $form->error($model, 'url'); ?>
		<p class="hint"><?php echo Menus::t('core', 'Do not change the name unless you know what you are doing.'); ?></p>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'img'); ?>
		<?php echo $form->textField($model, 'img', array('maxlength'=>255, 'class'=>'text-field')); ?>
		<?php echo $form->error($model, 'img'); ?>
		<p class="hint"><?php echo Menus::t('core', 'Do not change the name unless you know what you are doing.'); ?></p>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'mapping'); ?>
		<?php echo $form->textField($model, 'mapping', array('maxlength'=>255, 'class'=>'text-field')); ?>
		<?php echo $form->error($model, 'mapping'); ?>
		<p class="hint"><?php echo Menus::t('core', 'Just for top menu.'); ?></p>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton(Menus::t('core', 'Save')); ?> | <?php echo CHtml::link(Menus::t('core', 'Cancel'), Yii::app()->user->rightsReturnUrl); ?>
	</div>

<?php $this->endWidget(); ?>

</div>