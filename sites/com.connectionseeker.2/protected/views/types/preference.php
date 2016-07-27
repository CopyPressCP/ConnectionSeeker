<?php
$this->breadcrumbs=array(
	'Types'=>array('index'),
	'Preference',
);

$cs=Yii::app()->clientScript;
$cs->registerCoreScript( 'jquery.ui' );
$cs->registerScriptFile(Yii::app()->baseUrl . '/js/xheditor/xheditor-1.1.12-en.min.js', CClientScript::POS_HEAD);

$styleguide = CHtml::decode($styleguide);
?>

<h1>Create Types</h1>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'types-form',
	'enableAjaxValidation'=>false,
)); ?>
	<p class="note">Fields with <span class="required">*</span> are required.</p>
	<?php
    if (!empty($hits)) {
        if (!is_array($hits)) {
            $hits = array($hits);
        }
        echo "<div class='errorSummary'><ul>";
        foreach ($hits as $hv) {
            echo "<li>$hv</li>\n";
        }
        echo "</ul></div>";
    }
    ?>

	<div class="row">
        <label for="articletype"><?php echo Yii::t('Types', 'Article Type')?> <span class="required">*</span></label>
        <?php echo CHtml::textField('articletype', $articletype, array('size'=>60,'maxlength'=>256)); ?>
	</div>

	<div class="row">
        <label for="styleguide"><?php echo Yii::t('Types', 'Style Guide')?> <span class="required">*</span></label>
        <?php echo CHtml::textArea('styleguide', $styleguide, array('style'=>'height:780px;width:350px;')); ?>
	</div>

    <div class="clear"></div>
    <br />
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('id'=>'chgPrefBtn')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(document).ready(function() {
    //init WYSIWYG editor
    $("#styleguide").xheditor({tools:'full',width:780,height:350});

    $("#chgPrefBtn").click(function(){
        var rtn = true;
        if ($("#articletype").val() == ""){
            alert("Please provide the correct article type");
            rtn = false;
        }

        if ($("#styleguide").val() == ""){
            alert("Please enter the style guide");
            rtn = false;
        }
        return rtn;
    });
});
</script>
