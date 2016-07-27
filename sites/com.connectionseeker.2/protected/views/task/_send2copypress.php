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
		<?php echo $form->labelEx($model,'duedate'); ?>
        <?php 
        //1209600 means 14 days
        $this->widget('zii.widgets.jui.CJuiDatePicker', array(
             'model'=>$model,
            'name' => 'Task[duedate]',
            'value' => date("M/d/Y", ($model->duedate ? $model->duedate : time() + 1209600)), 
            // additional javascript options for the date picker plugin
            'options'=>array(
                'showAnim'=>'fold',
                'dateFormat'=>Yii::app()->getLocale()->getDateFormat('short', $model->duedate),
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
		<?php echo CHtml::button('Send to Copypress', array('id'=>'contentMarketingBtn')); ?> 
		<?php echo CHtml::button('Send to IO', array('id'=>'bulkIoBtn')); ?> 
		<?php echo CHtml::button('Mark as Completed', array('id'=>'bulkCompleteBtn')); ?> 
		<?php echo CHtml::button('Save Draft', array('id'=>'saveDraftBtn')); ?> 
	</div>
</div>
<?php $this->endWidget(); ?>
</div><!-- form -->



<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
    //init WYSIWYG editor
    $('#Task_style_guide').xheditor({tools:'full',width:780,height:360});

    $('#toggleStyleGuide').click(function(){
	    $('#divStyleGuide').toggle();
	    return false;
    });

    $("#contentMarketingBtn").click(function(){
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
                    data: $('.search-form form').serialize()
                });
            },
            'complete':function(XHR,TS){XHR = null;}
        });
    });

    $("#bulkIoBtn").click(function(){
        this.disabled = true;//disable the button first, protect it form send content twice in the meantime
        var taskids = new Array;

        var sendable = false;
        $("input[name='ids[]'][type='checkbox'][usage='send']:checked").each(function(i,e) {
            //e.value as same as $(this).val()
            sendable = true;
            taskids.push($(this).val());
        });

        if (!sendable){
            alert("No item can send to IO.");
            return false;
        }

        $.ajax({
            'type': 'POST',
            'dataType': 'json',
            'url': "<?php echo Yii::app()->createUrl('/task/send2io');?>",
            'data': {'ids[]': taskids},
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
            'data': {'ids[]': taskids, 'attrname':'progressstatus', 'attrvalue':'4'},
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