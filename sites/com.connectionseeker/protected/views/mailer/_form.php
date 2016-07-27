<?php
if (empty($model->id)) {
    $model->mailbox = "[Gmail]/All Mail";
    $model->smtp_host = "ssl://smtp.gmail.com";
    $model->smtp_port = "465";
    $model->pop3_host = "imap.gmail.com";
    $model->pop3_port = "993";
}

$types = Types::model()->actived()->bytype(array("category"))->findAll();
if ($types) {
    $gtps = CHtml::listData($types, 'refid', 'typename', 'type');
    $categories = $gtps['category'];
} else {
    $categories = array();
}

if ($model->category) {
    $_tmps = explode("|", $model->category);
    array_pop($_tmps);
    array_shift($_tmps);
    $model->category = $_tmps;
}

$cs=Yii::app()->clientScript;
$cs->registerCoreScript( 'jquery.ui' );
//may be we can use another dropdown plugin http://harvesthq.github.com/chosen/
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.min.js', CClientScript::POS_END);
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.filter.min.js', CClientScript::POS_END);
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.css');
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.filter.css');
$cs->registerCssFile( $cs->getCoreScriptUrl() . '/jui/css/base/jquery-ui.css', 'screen' );

$cuid = Yii::app()->user->id;
$roles = Yii::app()->authManager->getRoles($cuid);

?>
<div class="form">

<?php if(Yii::app()->user->hasFlash('success')):?>
    <div class="errorSummary">
        <?php echo Yii::app()->user->getFlash('success'); ?>
    </div>
<?php
Yii::app()->clientScript->registerScript(
   'HideFlashNotice',
   '$(".errorSummary").animate({opacity: 1.0}, 3000).fadeOut("slow");',
   CClientScript::POS_READY
);
?>
<?php endif; ?>

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'mailer-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'username'); ?>
		<?php echo $form->textField($model,'username',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'username'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password',array('size'=>60,'maxlength'=>60)); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>
<!-- 
	<div class="row">
		<?php echo $form->labelEx($model,'user_alias'); ?>
		<?php echo $form->textField($model,'user_alias',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'user_alias'); ?>
	</div> -->

	<div class="row">
		<?php echo $form->labelEx($model,'smtp_host'); ?>
		<?php echo $form->textField($model,'smtp_host',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'smtp_host'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'smtp_port'); ?>
		<?php echo $form->textField($model,'smtp_port',array('size'=>5,'maxlength'=>5)); ?>
		<?php echo $form->error($model,'smtp_port'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'pop3_host'); ?>
		<?php echo $form->textField($model,'pop3_host',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'pop3_host'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'pop3_port'); ?>
		<?php echo $form->textField($model,'pop3_port',array('size'=>5,'maxlength'=>5)); ?>
		<?php echo $form->error($model,'pop3_port'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'display_name'); ?>
		<?php echo $form->textField($model,'display_name',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'display_name'); ?>
	</div>

	<!-- <div class="row">
		<?php echo $form->labelEx($model,'email_from'); ?>
		<?php echo $form->textField($model,'email_from',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'email_from'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'reply_to'); ?>
		<?php echo $form->textField($model,'reply_to',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'reply_to'); ?>
	</div> -->

	<div class="row">
		<?php echo $form->labelEx($model,'mailbox'); ?>
		<?php echo $form->textField($model,'mailbox',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'mailbox'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'category'); ?>
        <?php
        $htmlOptions = array();
        $htmlOptions['multiple'] = true;
        $htmlOptions['style'] = "width:320px;";
        echo $form->dropDownList($model,'category',$categories,$htmlOptions); ?>
		<?php echo $form->error($model,'category'); ?>
	</div>

    <?php if (isset($roles["Admin"])) { ?>
	<div class="row">
		<?php echo $form->labelEx($model,'cron_out_ip'); ?>
		<?php echo $form->textField($model,'cron_out_ip',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'cron_out_ip'); ?>
	</div>
    <?php } ?>

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

<script type="text/javascript">
<!--
$(document).ready(function(){
    $("#Mailer_category").multiselect({noneSelectedText:'Select Category',selectedList:6}).multiselectfilter();
});
//-->
</script>
