<?php
$cs=Yii::app()->clientScript;
$cs->registerCoreScript( 'jquery.ui' );

$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.min.js', CClientScript::POS_END);
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.filter.min.js', CClientScript::POS_END);
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.css');
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.filter.css');
$cs->registerCssFile( $cs->getCoreScriptUrl() . '/jui/css/base/jquery-ui.css', 'screen' );

$this->breadcrumbs=array(
	'Users'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Create User', 'url'=>array('create')),
	array('label'=>'View User', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage User', 'url'=>array('index')),
);
?>

<h1>Generate User API Key For <?php echo $model->username; ?></h1>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'client_id'); ?>
		<?php echo $form->textField($model,'client_id',array('size'=>60,'maxlength'=>128,'readOnly'=>"readOnly")); ?>
		<?php echo $form->error($model,'client_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'apikey'); ?>
		<?php echo $form->textField($model,'apikey',array('size'=>60,'style'=>'width:380px','readOnly'=>"readOnly")); ?>
		<?php echo $form->error($model,'apikey'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'secretkey'); ?>
		<?php echo $form->textField($model,'secretkey',array('size'=>60,'style'=>'width:380px','readOnly'=>"readOnly")); ?>
		<?php echo $form->error($model,'secretkey'); ?>
	</div>

	<div class="row">
        <div id="apikeyresult" style="border: 1px solid red;width:400px;">
        Client ID:<?php echo $model->client_id;?> <br />
        API Key:<?php echo $model->apikey;?> <br />
        SECRET Key:<?php echo $model->secretkey;?> <br />
        </div>
        <br />
	</div>

	<div class="row buttons">
      <?php //echo CHtml::submitButton($model->apikey ? 'Re-Generate':'Generate', array('id'=>'keygenbtn','name'=>'keygenbtn')); ?>
      <?php echo CHtml::Button($model->apikey ? 'Re-Generate':'Generate', array('id'=>'keygenbtn','name'=>'keygenbtn','type'=>'button')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(document).ready(function() {
    $("#keygenbtn").click(function(){
        if (this.value == 'Re-Generate') {
            if(!confirm('Are you sure you want to Re-Generate this APIKey?')) return false;
        }
        $.ajax({
            'type':'GET', //request type
            'url':"<?php echo Yii::app()->createUrl('user/generate'); ?>",
            'dataType':"json",
            'data': "id=<?php echo $_GET['id'];?>",
            'success':function(data){
                //alert(data.msg);
                if (data.success) {
                    $("#User_client_id").val(data.client_id);
                    $("#User_apikey").val(data.apikey);
                    $("#User_secretkey").val(data.secretkey);
                    $("#apikeyresult").html("Client ID:"+data.client_id+"<br />API Key:"+data.apikey+"<br />SECRET Key:"+data.secretkey);
                    $("#apikeyresult").animate({backgroundColor: "#ffff99",}, 1000);
                } else {
                    alert(data.msg);
                }
            }
        });
    });
});
</script>