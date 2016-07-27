
<div id="mailboxdiv" style="display:none;">
    <div id="ifrdiv">
        <strong>
        <div class="horizonleft" id="domaininfo"></div>
        <div class="horizonleft" style="width:50px;height:160px;">&nbsp;</div>
        <div class="form">
            <p class="note">Update Email and Primary email here.</p>
            <div class="row">
                <label for="mailto">Email:</label>
                <input type="text" id="email" name="email" value="" maxlength="255" size="60">
            </div>
            <div class="row">
                <label for="mailto">Primary email:</label>
                <input type="text" id="primary_email" name="primary_email" value="" maxlength="255" size="60">
            </div>
        </div>
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

            $roles = Yii::app()->authManager->getRoles(Yii::app()->user->id);
            if ($tplm && !isset($roles['Admin'])) {
                $tplmodel = new Template;
                $_tplm = $tplmodel->findAllByAttributes(array("created_by"=>Yii::app()->user->id));
                if (count($_tplm) == 1) {
                    echo CHtml::hiddenField("template_id", $tplm->id);// Please see the index.php for var defaulttpl
                    echo $tplm->name;
                } else {
                    echo CHtml::dropDownList("template_id", "", CHtml::listData($_tplm,'id','name'), $htmlOptions);
                }
            } else {
                echo CHtml::dropDownList("template_id", "", CHtml::listData(Template::model()->findAll(),'id','name'), $htmlOptions);
            }
            ?>
            <?php //echo $mailform->error($model,'template_id'); ?>
        </div>

        <div class="row">
            <?php echo CHtml::label(Yii::t('Mailer', 'Mail From'), 'mailfrom'); ?>
            <?php
            $mlmodel = new Mailer;
            //$mlm = $mlmodel->findByAttributes(array("created_by"=>Yii::app()->user->id));
            $mlm = $mlmodel->findAllByAttributes(array("created_by"=>Yii::app()->user->id));
            $mfdefault = "";
            $mailer_count = count($mlm);
            if ($mailer_count > 0) {
                if ($mailer_count == 1) {
                    $mfdefault = $mlm{0}->id;
                    echo CHtml::hiddenField("mailfrom", $mfdefault);
                    echo $mlm{0}->display_name;
                } else {
                    echo CHtml::dropDownList("mailfrom", $mfdefault, CHtml::listData($mlm,'id','display_name'), array('prompt'=>'-- Select --'));
                }
            } else {
                //echo CHtml::dropDownList("mailfrom", $mfdefault, CHtml::listData(Mailer::model()->findAll(),'id','display_name'), array('prompt'=>'-- Select --'));
                echo CHtml::dropDownList("mailfrom", $mfdefault, CHtml::listData($mlmodel->findAll(),'id','display_name'), array('prompt'=>'-- Select --'));
            }
            ?>
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
		    <?php echo CHtml::textArea('message', "", array('style'=>'height:420px;width:460px;',"class"=>"xheditor-mfull")); ?>
		    <?php //echo CHtml::textArea('message', "", array('rows'=>'20', 'cols'=>'80',"class"=>"xheditor-mfull")); ?>
        </div>

        <div class="row buttons">
            <?php //echo CHtml::submitButton('Add to Queue', array('id' => 'addToQueue')); ?>
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
                var statustr = $("#mailboxdiv").parent().parent().prev();
                var parentstatusbox = statustr.find("select[name=touched_status]");
                parentstatusbox.val(actionvalue);
                parentstatusbox.css("background", "#f99");
                parentstatusbox.focus();

                parentstatusbox.parent().prev().html("<?php echo Yii::app()->user->name;?>");
            }

            //$('#ui-tabs-1').empty();
            //$('#ui-tabs-1').append(data);
        },

        'dataType': 'json',
        'type':'POST',
        'url':"<?php echo Yii::app()->createUrl('email/send');?>",
        'cache': false,
        'data': $("#outreach-form").serialize(),
        'complete':function(XHR,TS){XHR = null;}
    });

    $("#outreach-form").unbind();
    return false;
}

$('#addToQueue').unbind('click').click({actiontype:'queue'}, addToQOM);
$('#sendMail').unbind('click').click({actiontype:'send'}, addToQOM);
//$('#sendAllMail').unbind('click').click({actiontype:'sendall'}, addToQOM);
</script>
