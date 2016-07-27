<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("task/add"),
	'method'=>'post',
));

?>

<div class="form">
  <div class="errorSummary" id="add2taskerror" style="display:none;">
  <p>Please fix the following input errors:</p>
  <ul></ul>
  </div>
</div>

<script type="text/javascript">
<!--
var _client_categories;
//-->
</script>

<input type="hidden" value="" name="Task[inventory_ids]" id="Task_inventory_ids">

<table border="0" align="left" cellpadding="0" cellspacing="0">
  <tr>
	<td class="formSearch" >
        <?php //echo CHtml::label('Clients', 'Task_client_id');?>
        <?php
        $htmlOptions = array();
        $htmlOptions['prompt'] = '-- Select Client --';
        $htmlOptions['ajax'] = array(
            'type'=>'GET', //request type
            'url'=>Yii::app()->createUrl('client/campaigns'),
            'dataType'=>"json",
            'data'=>array(Yii::app()->request->csrfTokenName => Yii::app()->request->getCsrfToken(),
                          'client_id' =>'js:$("#Task_client_id").val()',
                          'attrs'  => 'category'),
            //leave out the data key to pass all form values through
            'success' => 'function(html){jQuery("#Task_campaign_id").html(html.campaigns);_client_categories = html.attrs;updateCatDefault();}',
        );
        echo CHtml::dropDownList('Task[client_id]', $_GET['Task']['client_id'], CHtml::listData(Client::model()->actived()->findAll(),'id','company'),$htmlOptions);
        ?>
    </td>
	<td class="formSearch" ><?php //echo CHtml::label('Campaigns', 'Task_campaign_id');?>
                            <?php echo CHtml::dropDownList('Task[campaign_id]', $_GET['Task']['campaign_id'], array(), array('onchange'=>'updateCatDefault();', 'prompt'=>'-- Select Campaign --')); ?></td>

	<td class="formSearch" ><?php //echo CHtml::label('Task Type', 'Task_tasktype');?>
                            <?php echo CHtml::dropDownList('Task[tasktype]', $_GET['Task']['tasktype'], $_linktasks, array('prompt'=>'-- Task Type --')); ?></td>

	<td class="formSearch" ><?php //echo CHtml::label('Content Category', 'Task_category');?>
                            <?php echo CHtml::dropDownList('Task[content_category_id]', $_GET['Task']['content_category_id'], $_categories, array('prompt'=>'-- Content Category --')); ?></td>
    <td>
       <div class="buttons"> &nbsp;&nbsp;
       <?php echo CHtml::submitButton('Add to Tasks', array('id' => 'add2task', 'type' => 'submit' , 'value' => 'Add to Tasks')); ?>
       </div>
    </td>
  </tr>
</table>

<?php $this->endWidget(); ?>
<!-- search-form -->

<script type="text/javascript">
<!--
function updateCatDefault(){
    var cpid = $("#Task_campaign_id").val();
    if (_client_categories[cpid] && typeof _client_categories[cpid] == 'object'){
        $("#Task_content_category_id").val(_client_categories[cpid].category);
    } else {
        $("#Task_content_category_id").val("");
    }
}

/*
if (typeof JSON.stringify !== 'function') {
    JSON.stringify = function (value, replacer, space) {

        var i;
        gap = '';
        indent = '';

        if (typeof space === 'number') {
            for (i = 0; i < space; i += 1) {
                indent += ' ';
            }
        } else if (typeof space === 'string') {
            indent = space;
        }

        rep = replacer;
        if (replacer && typeof replacer !== 'function' &&
                (typeof replacer !== 'object' ||
                typeof replacer.length !== 'number')) {
            throw new Error('JSON.stringify');
        }
        return str('', {'': value});
    };
}
*/
//-->
</script>