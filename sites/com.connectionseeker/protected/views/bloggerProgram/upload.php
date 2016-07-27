<?php
$cs=Yii::app()->clientScript;
$cs->registerCoreScript( 'jquery.ui' );

$actions = array(
    "1"=>"Bulk Upload Domain",
    "2"=>"Bulk Upload Domain with updated information",
);


$bpstatuses = BloggerProgram::$bpstatuses;

$types = Types::model()->bytype(array("bloggerprogram","activeprogram",'cms_username'))->findAll();
$gtps = CHtml::listData($types, 'refid', 'typename', 'type');
$_bloggerprogrames = $gtps['bloggerprogram'] ? $gtps['bloggerprogram'] : array();
$_activeprogrames = $gtps['activeprogram'] ? $gtps['activeprogram'] : array();
$_cms_usernames = $gtps['cms_username'] ? $gtps['cms_username'] : array();

$syndicationes = array("1"=>"Yes","0"=>"No");
?>

<div id="innermenu">
    <?php $this->renderPartial('/bloggerProgram/menu'); ?>
</div>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("bloggerProgram/upload"),
	'method'=>'post',
    'htmlOptions'=>array('enctype'=>'multipart/form-data'),
	'id'=>'blogger-program-form',
	'enableAjaxValidation'=>false,
)); ?>
    <h2>Upload File (Support CSV, Excel2005, Excel2007, Gnumeric and Openoffice Calc for now)</h2>

	<p class="note">Fields with <span class="required">*</span> are required.</p>
	<?php echo $form->errorSummary($model); ?>

	<div class="row">
        <?php echo CHtml::label('What you wanna do?', 'BloggerProgram_action');?>

        <?php echo CHtml::dropDownList('BloggerProgram[action]', $_REQUEST['BloggerProgram']['action'], $actions, array('style'=>'width:320px;')); ?>
        <div style="float:right;display:block;" id="innermenu"><ul class="actions">
            <li><a href="assets/bloggerprogram.xls" target="_blank" id="downloadtpl">Download Template</a></li>
         </ul></div>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'category'); ?>
        <?php echo $form->dropDownList($model, 'category', $_bloggerprogrames,array('prompt'=>'-- Select --')); ?>
		<?php echo $form->error($model,'category'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'activeprogram'); ?>
        <?php echo $form->dropDownList($model, 'activeprogram', $_activeprogrames,array('prompt'=>'-- Select --')); ?>
		<?php echo $form->error($model,'activeprogram'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'syndication'); ?>
        <?php echo $form->dropDownList($model, 'syndication', $syndicationes,array('prompt'=>'-- Select --')); ?>
		<?php echo $form->error($model,'syndication'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'status'); ?>
        <?php echo $form->dropDownList($model, 'status', $bpstatuses, array('prompt'=>'-- Select --')); ?>
		<?php echo $form->error($model,'status'); ?>
	</div>

	<div class="row">
        <?php echo CHtml::label('Import from local file', 'BloggerProgram_upfile');?>

        <?php echo CHtml::activeFileField($model, 'upfile', array('style'=>'height:30px;'));?>

	</div>

	<div class="clear"></div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Upload', array("id"=>"dupload")); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->

<script type="text/javascript">
$(document).ready(function() {
    $("#dupload").click(function(){
        if ($("#BloggerProgram_upfile").val()) {
            return true;
        } else {
            alert("Please choose an import file");
            return false;
        }
    });
});
</script>
