<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("domain/bulkattr"),
	'method'=>'post',
	'id'=>'bulkSetDomainAttrForm',
));
?>

<div class="form">
  <div class="errorSummary" id="bulkseterror" style="display:none;">
  <p>Please fix the following input errors:</p>
  <ul></ul>
  </div>
</div>

<input type="hidden" value="" name="Domain[domain_ids]" id="Domain_domain_ids">

<table border="0" align="left" cellpadding="0" cellspacing="0">
  <tr>

	<td class="txtfrm" height="50" >Move To<?php //echo $form->label($model,'category'); ?></td>
	<td class="formSearch" >
<?php
    $htmlOptions = array();
    $htmlOptions['multiple'] = true;
    //$htmlOptions['prompt'] = '[Category]';
    $htmlOptions['id'] = 'Domain_move2category';
    $htmlOptions['name'] = 'Domain[move2category]';
    echo $form->dropDownList($model, 'category', $categories, $htmlOptions);
    ?></td>

    <td>
       <div class="buttons"> &nbsp;&nbsp;
       <?php //echo CHtml::submitButton('Bulk Set', array('id' => 'bulksetattr', 'type' => 'submit', 'value' => 'Bulk Set')); ?>
       <?php echo CHtml::Button('Move', array('id' => 'bulksetattr', 'type' => 'button', 'value' => 'Move')); ?>
       <?php echo CHtml::Button('Move All Search Results To', array('id' => 'bulkallsetattr', 'type' => 'button', 'value' => 'Move All Search Results To')); ?>
       </div>
    </td>
  </tr>
</table>

<?php $this->endWidget(); ?>
<!-- search-form -->
