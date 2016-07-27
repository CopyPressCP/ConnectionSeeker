<?php
$styleguide = Utils::preference("styleguide");
$model->style_guide = CHtml::decode($styleguide);

$cs=Yii::app()->clientScript;
$cs->registerCoreScript( 'jquery.ui' );
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/css/gridview/styles.css');
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/css/gridview/jquery.yiigridview.js', CClientScript::POS_END);
$cs->registerCssFile((isset(Yii::app()->theme) ? Yii::app()->theme->baseUrl.'/css/gridview' : Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('zii.widgets.assets')).'/gridview') . '/styles.css');
$cs->registerScriptFile(Yii::app()->baseUrl . '/js/xheditor/xheditor-1.1.12-en.min.js', CClientScript::POS_HEAD);
?>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("task/send2copypress"),
	'method'=>'post',
));

echo $form->hiddenField($model, 'inventory_ids');
?>

<div class="form"><?php echo $form->errorSummary($model); ?></div>
<div class="form">	
	<div class="row">
		<a href="#" id="toggleStyleGuide"><?php echo $form->labelEx($model,'style_guide'); ?></a>
        <div id="divStyleGuide">
		<?php echo $form->textArea($model,'style_guide',array('style'=>"width:780px;height:280px;")); ?>
        </div>
        <?php echo $form->error($model,'style_guide'); ?>
	</div>

	<div class="row">
        <label for="bulk_channel_id">Who are these tasks assigned to?</label>
        <?php echo CHtml::dropDownList("bulk_channel_id", 0, $channels, array('prompt'=>'[Choose a channel]')); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'duedate'); ?>
        <?php 
        //1209600 means 14 days
        $this->widget('zii.widgets.jui.CJuiDatePicker', array(
             'model'=>$model,
            'name' => 'Task[duedate]',
            'value' => date("M/d/Y", ($model->duedate ? strtotime($model->duedate) : time() + 1209600)), 
            // additional javascript options for the date picker plugin
            'options'=>array(
                'showAnim'=>'fold',
                'dateFormat'=>Yii::app()->getLocale()->getDateFormat('short', strtotime($model->duedate)),
                'changeMonth' => 'true',
                'changeYear'=>'true',
                'constrainInput' => 'false',
            ),
            // DONT FORGET TO ADD TRUE this will create the datepicker return as string
        ));
        ?>
        <?php echo $form->error($model,'duedate'); ?>
	</div>
    <div class="row buttons">
		<?php echo CHtml::button('Send to IO', array('id'=>'bulkIoBtn')); ?> 
		<?php echo CHtml::button('Cancel IO', array('id'=>'bulkCancelIoBtn')); ?> 
		<?php echo CHtml::button('Mark as Completed', array('id'=>'bulkCompleteBtn')); ?> 
		<?php echo CHtml::button('Save Draft', array('id'=>'saveDraftBtn')); ?>
		<?php echo CHtml::button('Send to Copypress', array('id'=>'contentMarketingBtn')); ?>  <br/><br/>
		<?php
        //echo CHtml::button('Send to Content Step', array('id'=>'bulkStepBtn'));
        ?> 
	</div>
</div>
<?php $this->endWidget(); ?>
</div><!-- form -->



<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
    //init WYSIWYG editor
    $('#Task_style_guide').xheditor({tools:'full',width:780,height:360});
    $('#divStyleGuide').hide();
    $('#toggleStyleGuide').click(function(){
	    $('#divStyleGuide').toggle();
	    return false;
    });

    $("#contentMarketingBtn").click(function(){
        $('#divStyleGuide').show();

        this.disabled = true;//disable the button first, protect it form send content twice in the meantime
        var taskids = new Array;

        var sendable = false;
        $("input[name='ids[]'][type='checkbox'][usage='send']:checked").each(function(i,e) {
            //e.value as same as $(this).val()
            sendable = true;
            taskids.push($(this).val());
        });

        if (!sendable){
            alert("No task can send to CopyPress.");
            return false;
        }

        var duedate = $("#Task_duedate").val();
        var styleguide = $("#Task_style_guide").val();

        $.ajax({
            'type': 'POST',
            'dataType': 'json',
            'url': "<?php echo Yii::app()->createUrl('/task/send');?>",
            'data': {'ids[]': taskids, 'duedate':duedate, 'styleguide':styleguide},
            'success':function(data){
                this.disabled = false;
                alert(data.msg);
                $.fn.yiiGridView.update('task-grid', {
                    /*
                    put some search data here.
                    data: {'ids[]':taskids}
                    */
                    data: $('.search-form form').serialize(),
                });
            },
            'complete':function(XHR,TS){XHR = null;}
        });
    });

    $("#bulkIoBtn,#bulkCancelIoBtn,#bulkStepBtn").each(function(){
        $(this).click(function(){
            //alert($(this).attr("id"));
            var bulk_channel_id = $("#bulk_channel_id").val();
            if ($(this).attr("id") == "bulkCancelIoBtn"){
                var oprname = "cancel";
            } else if($(this).attr("id") == "bulkStepBtn") {
                var oprname = "send2step";
            } else {
                var oprname = "send";

                if (bulk_channel_id == "") {
                    alert("Who are these tasks assigned to?");
                    $("#bulk_channel_id").focus();
                    $("#bulk_channel_id").css("background-color","red");
                    return false;
                }
            }
            $("#bulk_channel_id").css("background-color","white");

            this.disabled = true;//disable the button first, protect it form send content twice in the meantime
            var taskids = new Array;

            var sendable = false;
            $("input[name='ids[]'][type='checkbox'][usage='send']:checked").each(function(i,e) {
                //e.value as same as $(this).val()
                sendable = true;
                taskids.push($(this).val());
            });

            if (!sendable){
                if (oprname == "send") {
                    alert("No item can send to IO.");
                } else if (oprname == "send2step") {
                    alert("No item can send to Content Step.");
                } else {
                    alert("No IO item can cancel.");
                }
                return false;
            }

            var duedate = $("#Task_duedate").val();

            $.ajax({
                'type': 'POST',
                'dataType': 'json',
                'url': "<?php echo Yii::app()->createUrl('/task/send2io');?>",
                'data': {'ids[]': taskids, 'oprname': oprname, 'bulk_channel_id': bulk_channel_id, 'duedate': duedate},
                'success':function(data){
                    this.disabled = false;
                    alert(data.msg);
                    $.fn.yiiGridView.update('task-grid', {
                        /*
                        put some search data here.
                        data: {'ids[]':taskids}
                        */
                        data: $('.search-form form').serialize()
                    });
                },
                'complete':function(XHR,TS){XHR = null;}
            });
        });
    });

    $("#bulkCompleteBtn").click(function(){
        this.disabled = true;//disable the button first, protect it form send content twice in the meantime
        var taskids = new Array;

        var sendable = false;
        $("input[name='ids[]'][type='checkbox'][usage='send']:checked").each(function(i,e) {
            //e.value as same as $(this).val()
            sendable = true;
            taskids.push($(this).val());
        });

        if (!sendable){
            alert("Please choose one item at least.");
            return false;
        }

        $.ajax({
            'type': 'POST',
            'dataType': 'json',
            'url': "<?php echo Yii::app()->createUrl('/task/batchattr');?>",
            'data': {'ids[]': taskids, 'attrname':'iostatus', 'attrvalue':'5'},
            'success':function(data){
                this.disabled = false;
                alert(data.msg);
                $.fn.yiiGridView.update('task-grid', {
                    /*
                    put some search data here.
                    data: {'ids[]':taskids}
                    */
                    data: $('.search-form form').serialize()
                });
            },
            'complete':function(XHR,TS){XHR = null;}
        });
    });
});

//]]>
</script>