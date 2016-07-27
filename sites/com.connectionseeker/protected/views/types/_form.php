<?php
$types = array(
    'site'     => 'Site Type',
    'outreach' => 'Outreach Type',
    'linktask' => 'Link Task Type',
    'category' => 'Content Categories',
    'channel' => 'Channel',
    'technorati' => 'Technorati Categories',
    'awis' => 'AWIS Categories',
    'cmsbuilder' => 'Site Builder',
    'tierlevel' => 'Tier Level',
    'bloggerprogram' => 'Blogger Program Category',
    'activeprogram' => 'BP - Active Program',
    'cms_username' => 'CMS - Username',
);

?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'types-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'type'); ?>
		<?php
        $htmlOptions = array();
        if ($model->id) {
            $htmlOptions['readonly'] = 'readonly';
            $htmlOptions['size'] = 20;
            $htmlOptions['maxlength'] = 20;
            echo $form->textField($model,'type',$htmlOptions);
        } else {
            $htmlOptions['ajax'] = array(
                'type'=>'GET', //request type
                'url'=>Yii::app()->createUrl('types/maxref'),
                'dataType'=>"json",
                'data'=>array(Yii::app()->request->csrfTokenName => Yii::app()->request->getCsrfToken(),
                              'type' =>'js:this.value',
                              'endapp'  => true),
                //leave out the data key to pass all form values through
                'success' => 'function(html){jQuery("#Types_refid").val(html.maxrefid);}',
            );
            echo $form->dropDownList($model, 'type', $types, $htmlOptions);
        }
        
        ?>
		<?php echo $form->error($model,'type'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'refid'); ?>
		<?php
        $htmlOptions = array();
        if ($model->id) {
            //$htmlOptions['readonly'] = 'readonly';
        }
        echo $form->textField($model,'refid', $htmlOptions);
        ?>
		<?php echo $form->error($model,'refid'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'typename'); ?>
		<?php echo $form->textField($model,'typename',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'typename'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'outils'); ?>
		<?php echo $form->textField($model,'outils',array('style'=>'width:380px')); ?>
		<?php echo $form->error($model,'outils'); ?>
	</div>

	<div class="row horizonleft">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php echo $form->checkBox($model,'status', array('class'=>'chkbox')); ?>
		<?php echo $form->error($model,'status'); ?>
	</div>
    <div class="clear"></div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->