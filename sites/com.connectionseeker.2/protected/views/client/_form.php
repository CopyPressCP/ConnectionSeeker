<?php
$cs=Yii::app()->clientScript;
$cs->registerCoreScript( 'jquery.ui' );
//$cs->registerScriptFile(Yii::app()->baseUrl . '/js/timepicker/jquery.timePicker.min.js', CClientScript::POS_HEAD);
//$cs->registerScriptFile(Yii::app()->baseUrl . '/js/charCount.js', CClientScript::POS_HEAD);
//http://stackoverflow.com/questions/6964265/how-can-i-modify-jquery-ui-tabs-in-yii-framework
//http://luwenxiang1990.blog.163.com/blog/static/17360763920117149561935/
//http://www.yiiframework.com/forum/index.php?/topic/7508-ajax-form-submission-inside-cjuitabs/

$roles = Yii::app()->authManager->getRoles(Yii::app()->user->id);
?>

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
<?php
if(!isset($roles['Marketer'])){//means Admin
?>
	<div class="row">
		<?php echo $form->labelEx($model,'user_id'); ?>
        <?php echo $form->dropDownList($model,'user_id',CHtml::listData(User::model()->actived()->findAll(),'id','username')); ?>
		<?php echo $form->error($model,'user_id'); ?>
	</div>
<?php
}
?>
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

	<div class="row">
		<?php echo $form->labelEx($model,'assignee'); ?>
		<?php //echo $form->textField($model,'assignee'); ?>
        <?php echo $form->dropDownList($model,'assignee',CHtml::listData(User::model()->actived()->findAll(),'id','username')); ?>
		<?php echo $form->error($model,'assignee'); ?>
	</div>

	<div class="row horizonleft">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php echo $form->checkBox($model,'status', array('class'=>'chkbox')); ?>
		<?php echo $form->error($model,'status'); ?>
	</div>
  </div>

  <div id="middlebasicinfo">
	<div class="row" id="client_domains">
        <?php
        echo $form->labelEx($domodel,'domain', array('label'=>'Domains (subdomains ok, no www)'));
        echo $form->error($domodel,'domain');
        $_t_count = 3;//default text filed count

        if (isset($model->id) && $model->id > 0) {
            $_domodel = $domodel->findAllByAttributes(array('client_id'=>$model->id));
            //if (count($_domodel)) {
            $domaincount = count($_domodel);
            if ($domaincount > 0) {
                //print_r($_domodel);
                $removeddomains = array();
                foreach($_domodel as $_dv) {
                    if ($_dv->status == 0) {$removeddomains[] = $_dv->domain;continue;}
                    echo $form->textField($_dv,'domain',array('size'=>60,'maxlength'=>255,'name'=>get_class($domodel)."[domain][cd_"."$_dv->id]"));
                }
            }
            $_t_count = $_t_count - $domaincount;
        }

        if ($_t_count > 0) {
            for ($i = 0; $i < $_t_count; $i++) {
                echo $form->textField($domodel,'domain[]',array('size'=>60,'maxlength'=>255));
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
        <?php echo CHtml::link(Yii::t('Client', 'Cancel'), array("client/index")); ?>
	</div>
<?php //var_dump(Yii::app()->user);?>


<script type="text/javascript">
$(document).ready(function() {
    $('#add_more_domain').click(function(){
        $('#client_domains').append('<?php echo $form->textField($domodel,"domain[]",array("size"=>60,"maxlength"=>255)); ?>');
        $('#client_domains').append('<?php echo $form->textField($domodel,"domain[]",array("size"=>60,"maxlength"=>255)); ?>');
    });
});
</script>

<?php $this->endWidget(); ?>



</div><!-- form -->

