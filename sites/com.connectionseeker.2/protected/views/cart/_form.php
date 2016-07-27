<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'cart-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

<?php
$roles = Yii::app()->authManager->getRoles(Yii::app()->user->id);
if(isset($roles['Marketer'])) {
    $__uinfo = User::model()->findByPk(Yii::app()->user->id);
    echo $form->hiddenField($model,'client_id',array('value'=>$__uinfo->client_id));
} else {
?>
	<div class="row">
		<?php echo $form->labelEx($model,'client_id'); ?>
        <?php echo $form->dropDownList($model, 'client_id', CHtml::listData(Client::model()->findAll(),'id','company'),array('prompt'=>'-- Select --')); ?>
		<?php echo $form->error($model,'client_id'); ?>
	</div>
<?php
}
?>

	<div class="row">
		<?php echo $form->labelEx($model,'client_domain_id'); ?>
		<?php echo $form->textField($model,'client_domain_id'); ?>
		<?php echo $form->error($model,'client_domain_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'client_domain'); ?>
		<?php echo $form->textField($model,'client_domain',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'client_domain'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'domain_id'); ?>
		<?php echo $form->textField($model,'domain_id',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'domain_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'domain'); ?>
		<?php echo $form->textField($model,'domain',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'domain'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'status'); ?>
        <?php echo $form->dropDownList($model, 'status', Cart::$dstatus); ?>
		<?php echo $form->error($model,'status'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->