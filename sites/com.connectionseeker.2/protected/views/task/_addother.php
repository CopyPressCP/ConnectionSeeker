<div class="grid-view">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'task-form',
	'enableAjaxValidation'=>false,
));
?>
    <div class="form"><?php echo $form->errorSummary($model); ?></div>

<?php
//we only store the campaign_id,client_id,content_category_id,tasktype,inventory_ids into $_POST['Task']
echo $form->hiddenField($model, 'campaign_id');
echo $form->hiddenField($model, 'client_id');
echo $form->hiddenField($model, 'content_category_id');
echo $form->hiddenField($model, 'tasktype');
echo $form->hiddenField($model, 'inventory_ids');

foreach ($ivtmodel as $k => $iv) {
    $id = $iv->id;
    $model->optionalkw4 = $iv->domain;
    echo $form->hiddenField($model, 'inventory_id', array('value' => $id, 'name' => 'inventory_id[]'));
?>


    <div><span class="hintTitle"><?php echo CHtml::link($iv->domain, Yii::app()->createUrl("inventory/view", array("id"=>$iv->id)), array('target'=>'_blank'));?></span>
    (Age:<span class="hintTitle">
         <?php echo ($iv->rdomain->onlinesince>658454400) ? date("Y-m-d", $iv->rdomain->onlinesince) : "-1";?>
         </span>
    Page Rank:<span class="hintTitle"><?php echo $iv->rdomain->googlepr;?></span>
    Category:<span class="hintTitle"><?php echo $iv->category_str;?></span>
    Site Type:<span class="hintTitle"><?php echo $_stypes[$iv->rdomain->stype];?></span>
    Channel:<span class="hintTitle"><?php echo $_channels[$iv->channel_id];?></span>
    )
    </div>

    <div class="clear"></div>
    <table class="items" id="table-<?php echo $k; ?>" >
    <tr>
        <th><?php echo $form->labelEx($model,'anchortext'); ?></th>
        <th nowrap><?php echo $form->labelEx($model,'targeturl'); ?></th>
        <th></th>
    </tr>
    <tr class="filters">
        <td>
            <?php
            if ($_kws) {
                echo $form->dropDownList($model,'anchortext', $_kws, array('name' => 'anchortext' . $id . '[]'));
            } else {
                echo $form->textField($model,'anchortext', array('name' =>'anchortext' . $id . '[]', 'style'=>"width:370px;"));
            }
            ?>
        </td>
        <td>
            <?php
            if ($_urls) {
                echo $form->dropDownList($model,'targeturl',$_urls, array('name' => 'targeturl' . $id . '[]'));
            } else {
                echo $form->textField($model,'targeturl', array('name' =>'targeturl' . $id . '[]', 'style'=>"width:370px;"));
            }
            ?>
        </td>
        <td><?php echo CHtml::button('Add', array('value' => '+', 'onclick' =>"copytr(this)", 'class'=>'clonebtn') ) .  CHtml::button('remove', array('value' => '-', 'onclick' =>"removetr(this)", 'class'=>'clonebtn'));?></td>
    </tr>
    </table>
    <br />

<?php 
    }
?>

<div class="form">	
	<div class="row">
		<?php echo $form->labelEx($model,'assignee'); ?>
		<?php echo $form->dropDownList($model,'assignee', Chtml::listData(User::model()->findAll(),'id','username'), array('prompt'=>'-- Select --')); ?>
        <?php echo $form->error($model,'assignee'); ?>
	</div>
    <div class="row buttons">
		<?php echo CHtml::submitButton('Add Link Task', array('id'=>'contentMarketingBtn')); ?>
	</div>
</div>
<?php $this->endWidget(); ?>
</div><!-- form -->

<script type="text/javascript">
$(document).ready(function() {
    $("#contentMarketingBtn").click(function(){
        var rtn = true;
        $("select[name*='anchortext'],input[name*='anchortext']").each(function(){
            if ($(this).val() == "" || $(this).val() == undefined){
                alert("Please Choose Anchor Text First");
                $(this).focus();
                $(this).css("background-color","red");
                return rtn = false;
            }
        });
        if (!rtn) return false;

        $("select[name*='targeturl'],input[name*='targeturl']").each(function(){
            if ($(this).val() == "" || $(this).val() == undefined){
                alert("Please Choose Target URL First");
                $(this).focus();
                $(this).css("background-color","red");
                return rtn = false;
            }
        });
        if (!rtn) return false;

        if ($('#Task_campaign_id').value == "" || $('#Task_campaign_id').value == 0){
            alert("Please choose one campaign first.");
            return false;
        }

        if ($('#Task_assignee').val() == "" || $('#Task_assignee').val() == 0){
            alert("Please assign this task to one user at least.");
            $('#Task_assignee').css("background-color","red");
            return false;
        }

        return rtn;
    });
});
</script>