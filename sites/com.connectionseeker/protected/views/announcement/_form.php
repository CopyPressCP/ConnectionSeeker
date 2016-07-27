<?php
$cs=Yii::app()->clientScript;
$cs->registerCoreScript( 'jquery.ui' );

$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.min.js', CClientScript::POS_END);
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.filter.min.js', CClientScript::POS_END);
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.css');
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.filter.css');
$cs->registerCssFile( $cs->getCoreScriptUrl() . '/jui/css/base/jquery-ui.css', 'screen' );
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'announcement-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'roles'); ?>
		<?php
        $htmlOptions = array();
        $htmlOptions['multiple'] = true;
        $htmlOptions['style'] = 'width:380px;';
        echo $form->dropDownList($model,'roles',CHtml::listData(Yii::app()->getAuthManager()->getRoles(), 'name', 'name'), $htmlOptions);
        ?>
		<?php echo $form->error($model,'roles'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textArea($model,'description',array('style'=>"width:500px;height:200px;")); ?>
		<?php echo $form->error($model,'description'); ?>
	</div>

    <div class="row">
        <?php echo $form->labelEx($model,'addeddate'); ?>
        <?php 
        //1209600 means 14 days
        $this->widget('zii.widgets.jui.CJuiDatePicker', array(
            'model'=>$model,
            'name' => 'Announcement[addeddate]',
            'value' => date("Y-m-d", ($model->addeddate ? strtotime($model->addeddate) : time() - 86400)), 
            // additional javascript options for the date picker plugin
            'options'=>array(
                'showAnim'=>'fold',
                'dateFormat'=>'yy-mm-dd',
                'changeMonth' => 'true',
                'changeYear'=>'true',
                'constrainInput' => 'false',
            ),
            // DONT FORGET TO ADD TRUE this will create the datepicker return as string
        ));
        ?>
        <?php echo $form->error($model,'addeddate'); ?>
    </div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->



<script type="text/javascript">
$(document).ready(function() {
    $("#Announcement_roles").multiselect({noneSelectedText:'Select Roles',selectedList:6}).multiselectfilter();
});
</script>