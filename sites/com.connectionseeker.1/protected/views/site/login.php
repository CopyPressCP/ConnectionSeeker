<?php
$this->pageTitle=Yii::app()->name . ' - Login';
$this->breadcrumbs=array(
	'Login',
);
?>

<div class="login-logo">
<img alt="Welcome To ConnectionSeeker" border="0" alt="Connectionseeker" src="<?php echo Yii::app()->theme->baseUrl; ?>/images/logo.png">
</div>
<p>&nbsp;</p>
<p>&nbsp;</p>

<div class="form" id="contlogin">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'login-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'username', array('class'=>'fixedlabe')); ?>
		<?php echo $form->textField($model,'username', array('class'=>'fixedinput')); ?>
		<?php echo $form->error($model,'username'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'password', array('class'=>'fixedlabe')); ?>
		<?php echo $form->passwordField($model,'password',array('class'=>'fixedinput')); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>

	<div class="row rememberMe">
		<?php echo $form->label($model,'rememberMe', array('class'=>'fixedlabe')); ?>
		<?php echo $form->checkBox($model,'rememberMe'); ?>
		<?php echo $form->error($model,'rememberMe'); ?>
	</div>

	<div class="row buttons">
		<?php //echo CHtml::submitButton('Login'); ?>
		<?php echo CHtml::imageButton(Yii::app()->request->baseUrl . '/images/submit.gif'); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->
