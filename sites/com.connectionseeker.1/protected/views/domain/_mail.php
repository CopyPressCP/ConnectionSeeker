
<div id="mailboxdiv" style="display:none;">
    <div id="ifrdiv">
        <strong>
        <div class="horizonleft" id="domaininfo"></div>
        <div class="clear"></div>

        <div class="row horizonleft">Switch to Contact: </div>
        <div>
            <a href="#" id="lnabout" target="_blank">About</a>
            <a href="#" id="lnwhois" target="_blank">Whois</a>
        </div>
        </strong>
        <div class="clear"></div>
        <iframe id="ifr_webpreview" name="ifr_webpreview" style="width:520px;height:500px" frameborder="0" scrolling="auto" src="about:blank"></iframe>
    </div>


    <div id="maildiv">
        <div class="form">

        <?php $mailform=$this->beginWidget('CActiveForm', array(
            'id'=>'outreach-form',
            'action'=>Yii::app()->createUrl('email/send'),
            'enableAjaxValidation'=>true,
        )); ?>
        <input type="hidden" name="actiontype" id="actiontype" value="send" />
        <input type="hidden" name="mail_domain_id" id="mail_domain_id" value="0" />
        <p class="note">Fields with <span class="required">*</span> are required.</p>
        <?php echo $mailform->errorSummary($model); ?>

        <div class="row">
            <?php echo CHtml::label(Yii::t('Mailer', 'Template'), 'template_id'); ?>
            <?php 
            $htmlOptions = array();
            $htmlOptions['prompt'] = "-- Select --";
            $htmlOptions['ajax'] = array(
                'type'=>'GET', //request type
                'url'=>Yii::app()->createUrl('template/replacement'),
                'dataType'=>"json",
                'data'=>array(Yii::app()->request->csrfTokenName => Yii::app()->request->getCsrfToken(),
                              //'domain_id' =>'js:$("#client_domain_id").val()',
                              'id'  =>'js:this.value'),
                //leave out the data key to pass all form values through
                //for the textarea, we must use the selector.val() to set the value, due to we load the xheditor plugin
                'success' => 'function(html){jQuery("#subject").val(html.subject);jQuery("#message").val(html.content);}',
            );
            ?>
            <?php echo CHtml::dropDownList("template_id", "", CHtml::listData(Template::model()->findAll(),'id','name'), $htmlOptions); ?>
            <?php //echo $mailform->error($model,'template_id'); ?>
        </div>

        <div class="row">
            <?php echo CHtml::label(Yii::t('Mailer', 'Mail From'), 'mailfrom'); ?>
            <?php echo CHtml::dropDownList("mailfrom", "", CHtml::listData(Mailer::model()->findAll(),'id','display_name'), array('prompt'=>'-- Select --')); ?>
        </div>

        <div class="row">
            <?php echo CHtml::label(Yii::t('Mailer', 'Mail To'), 'mailto'); ?>
		    <?php echo CHtml::textField('mailto', "", array('size'=>60,'maxlength'=>255)); ?>
        </div>

        <div class="row">
            <?php echo CHtml::label(Yii::t('Mailer', 'Subject'), 'subject'); ?>
		    <?php echo CHtml::textField('subject', "", array('size'=>120)); ?>
        </div>

        <div class="row">
            <?php echo CHtml::label(Yii::t('Mailer', 'Message'), 'message'); ?>
		    <?php echo CHtml::textArea('message', "", array('rows'=>10, 'cols'=>150, 'style'=>'height:350px;width:430px;')); ?>
        </div>

        <div class="row buttons">
            <?php echo CHtml::submitButton('Add to Queue', array('id' => 'addToQueue')); ?>
            <?php echo CHtml::submitButton('Send', array('id' => 'sendMail')); ?>
        </div>

        <?php $this->endWidget(); ?>

        </div>
    </div>

    <div class="clear"></div>
</div>

<script type="text/javascript">
//add to queue Or Mail
var addToQOM = function(event) {
    //alert(event.data.actiontype);
    var actionvalue = 2;
    if (event.data.actiontype == 'queue') {
        actionvalue = 9;
    }

    $("#actiontype").val(event.data.actiontype);
    $.ajax({
        'success': function(data) {
            alert(data.message);
            if (data.success){
                var parentstatusbox = $("#mailboxdiv").parent().parent().prev().find("select[name=touched_status]");
                parentstatusbox.val(actionvalue);
                parentstatusbox.css("background", "#f99");
                parentstatusbox.focus();
            }

            //$('#ui-tabs-1').empty();
            //$('#ui-tabs-1').append(data);
        },

        'dataType': 'json',
        'type':'POST',
        'url':"<?php echo Yii::app()->createUrl('email/send');?>",
        'cache': false,
        'data': $("#outreach-form").serialize()
    });

    $("#outreach-form").unbind();
    return false;
}

$('#addToQueue').unbind('click').click({actiontype:'queue'}, addToQOM);
$('#sendMail').unbind('click').click({actiontype:'send'}, addToQOM);
//$('#sendAllMail').unbind('click').click({actiontype:'sendall'}, addToQOM);
</script>
