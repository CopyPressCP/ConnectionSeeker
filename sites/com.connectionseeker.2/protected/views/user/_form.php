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
	'id'=>'user-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'username'); ?>
		<?php echo $form->textField($model,'username',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'username'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php $model->password = ""; echo $form->passwordField($model,'password',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'password2'); ?>
		<?php echo $form->passwordField($model,'password2',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'password2'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>

	<div class="row">
        <?php echo $form->labelEx($auth,'itemname'); ?>
        <?php //echo $form->dropDownList($auth,'itemname',$roles);?>
        <?php
        echo $form->dropDownList($auth,'itemname', CHtml::listData(Yii::app()->getAuthManager()->getRoles(), 'name', 'name'));
        ?>
        <?php echo $form->error($auth,'itemname'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'client_id'); ?>
        <?php echo $form->dropDownList($model, 'client_id', CHtml::listData(Client::model()->findAll(),'id','company'),array('prompt'=>'-- Select --')); ?>
		<?php echo $form->error($model,'client_id'); ?>
	</div>

	<div class="row" id="duty_campaigns">
		<?php echo $form->labelEx($model,'duty_campaign_ids'); ?>
        <?php
        //print_r($model->duty_campaign_ids);
        $htmlOptions = array();
        $htmlOptions['multiple'] = true;
        //$htmlOptions['prompt'] = '-- Select --';
        $htmlOptions['style'] = 'width:380px;';
        $initcmps = array();
        if ($model->client_id) {
            $initcmps = CHtml::listData(Campaign::model()->findAll("client_id=".$model->client_id),'id','name');
        }
        echo $form->dropDownList($model, 'duty_campaign_ids', $initcmps, $htmlOptions); ?>
		<?php echo $form->error($model,'duty_campaign_ids'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'type'); ?>
        <?php echo $form->dropDownList($model, 'type', User::$utype); ?>
		<?php echo $form->error($model,'type'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'aschannel'); ?>
		<?php
        $showup = 'style="display:none;"';
        if (!empty($model->channel_id)) {
            $model->aschannel = 3;
            $showup = "";
        }
        $aschls = array("1"=>"Not a channel", "2"=>"Create a new channel", "3"=>"Choose one channel from DB");
        echo $form->radioButtonlist($model,'aschannel',$aschls,array('separator'=>'&nbsp',
                                                                     'labelOptions'=>array('class'=>'labelForRadio')));
        ?>
		<?php echo $form->error($model,'aschannel'); ?>
	</div>

    <div style="clear:both"></div>
	<div class="row" id="channel_from_db" <?php echo $showup;?>>
		<?php echo $form->labelEx($model,'channel_id', array("label"=>"Channel (Choose one channel when the user is Publisher)")); ?>
        <?php echo $form->dropDownList($model, 'channel_id', CHtml::listData(Types::model()->actived()->findAll("type='channel'"),'refid','typename'),array('prompt'=>'-- Select --')); ?>
		<?php echo $form->error($model,'channel_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'display_mode'); ?>
        <?php echo $form->dropDownList($model, 'display_mode', User::$dpmode); ?>
		<?php echo $form->error($model,'display_mode'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(document).ready(function() {
    $("input[name*='aschannel']").bind("click",function(){
        var _chlv = $(this).val();
        if (_chlv == "3") {
            $("#channel_from_db").show();
        } else {
            $("#channel_from_db").hide();
        }
    });

    $("#User_duty_campaign_ids").multiselect({noneSelectedText:'Select Campaigns',selectedList:6}).multiselectfilter();
    $("#User_client_id").change(function(){
        if (this.value == "") {
            //alert(this.value);
            //hide the campaigns of div
            $("#duty_campaigns").hide();
        } else {
            //show the campaign div
            $("#duty_campaigns").show();
            $.ajax({
                'type':'GET', //request type
                'url':"<?php echo Yii::app()->createUrl('client/campaigns'); ?>",
                'dataType':"json",
                'data': 'client_id='+this.value+"&format=html4dropdown",
                'success':function(data){
                    $("#User_duty_campaign_ids").html(data.campaigns);
                    $("#User_duty_campaign_ids").multiselect('refresh');

                    /*
                    $("#User_duty_campaign_ids").multiselect('destroy');
                    $("#User_duty_campaign_ids").html(data.campaigns);
                    $("#User_duty_campaign_ids").multiselect({noneSelectedText:'Select Campaigns',selectedList:6}).multiselectfilter();

                    if (data.success) {
                        $("#User_duty_campaign_ids").html(data.campaigns);
                    } else {
                        alert(data.msg);
                    }
                    */
                }
            });
        }
    });

    if ($("#User_client_id").val() == "") {
        $("#duty_campaigns").hide();
    }


});
</script>