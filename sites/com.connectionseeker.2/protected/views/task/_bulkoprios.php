<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("task/batchattr"),
	'method'=>'post',
	'id'=>'bulkOprTasks',
));
?>
<div class="form"><?php echo $form->errorSummary($model); ?></div>

    <div class="row buttons">
		<?php echo CHtml::button('Accept', array('id'=>'bulkAcceptIoBtn')); ?> 
		<?php //echo CHtml::button('Approve', array('id'=>'bulkApproveIoBtn')); ?> 
		<?php //echo CHtml::button('Rewind', array('id'=>'bulkRewindIoBtn')); ?> 
	</div>
</div>
<?php $this->endWidget(); ?>
</div><!-- form -->

<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {

    //$("#bulkAcceptIoBtn, #bulkAcceptIoBtn, #bulkRewindIoBtn").click(function(){
    $("#bulkAcceptIoBtn").click(function(){
        this.disabled = true;//disable the button first, protect it form send content twice in the meantime
        var taskids = new Array;

        var sendable = false;
        $("input[name='ids[]'][type='checkbox']:checked").each(function(i,e) {
            //e.value as same as $(this).val()
            sendable = true;
            taskids.push($(this).val());
        });

        if (!sendable){
            alert("Please choose one IO item at least.");
            return false;
        }


        $.ajax({
            'type': 'POST',
            'dataType': 'json',
            'url': "<?php echo Yii::app()->createUrl('/task/batchattr');?>",
            'data': {'ids[]': taskids, 'attrname':'iostatus', 'attrvalue':'2'},
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

