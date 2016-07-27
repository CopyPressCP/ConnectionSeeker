<?php
$this->pageTitle=Yii::app()->name . ' - Create '.ucfirst($this->getAction()->getId());
$this->breadcrumbs=array(
	'Publisher',
);

//echo $this->getAction()->getId();

$cs=Yii::app()->clientScript;
$cs->registerCoreScript( 'jquery.ui' );
?>

<div class="pagecontent">

<h1>Sign up as a <?php echo $this->getAction()->getId();?></h1>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'client-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary(array($model,$domodel)); ?>

<div id="basicinfo">
  <div id="leftbasicinfo">
	<div class="row">
		<?php echo $form->labelEx($model,'company'); ?>
		<?php echo $form->textField($model,'company',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'company'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'contact_name'); ?>
		<?php echo $form->textField($model,'contact_name',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'contact_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'telephone'); ?>
		<?php echo $form->textField($model,'telephone',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'telephone'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'cellphone'); ?>
		<?php echo $form->textField($model,'cellphone',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'cellphone'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'note'); ?>
		<?php echo $form->textArea($model,'note',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'note'); ?>
	</div>

  </div>

  <div id="middlebasicinfo">
	<div class="row" id="client_domains">
        <?php
        echo $form->labelEx($domodel,'domain', array('label'=>'Domains (subdomains ok, no www)'));
        echo $form->error($domodel,'domain');
        $_t_count = 3;//default text filed count

        if ($_t_count > 0) {
            for ($i = 0; $i < $_t_count; $i++) {
                echo $form->textField($domodel,'domain[]',array('size'=>60,'maxlength'=>255));
                echo "<br/>";
            }
        }
        ?>
	</div>
	<div class="row" id="add_more_domain">
        <?php echo CHtml::link(Yii::t('Client', '+Add More'), 'javascript:void(0);'); ?>
	</div>
  </div>

<?php if (isset($removeddomains) && $removeddomains) {?>
  <div id="rightbasicinfo">
    <label>Removed Domains</label>
    <?php
        foreach($removeddomains as $_rdm) {
    ?>
	<div class="row">
		<span style="color:red"><?php echo $_rdm; ?></span>
	</div>
    <?php }?>
  </div>
<?php }?>

  <div style="clear:both"></div>
</div><!-- end of #basicinfo -->


	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?> | 
        <?php echo CHtml::link(Yii::t('Client', 'Cancel'), array("site/index")); ?>
	</div>
<?php //var_dump(Yii::app()->user);?>


<script type="text/javascript">
$(document).ready(function() {
    $('#add_more_domain').click(function(){
        $('#client_domains').append('<?php echo $form->textField($domodel,"domain[]",array("size"=>60,"maxlength"=>255)); ?><br />');
        $('#client_domains').append('<?php echo $form->textField($domodel,"domain[]",array("size"=>60,"maxlength"=>255)); ?><br />');
    });
});
</script>

<?php $this->endWidget(); ?>



</div><!-- form -->

</div>