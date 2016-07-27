<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
<table border="0" align="left" cellpadding="0" cellspacing="0">
<?php if(!isset($roles['Marketer'])) { ?>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'id'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'id',array('size'=>20,'maxlength'=>20)); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'client_id'); ?></td>
	<td class="formSearch" >
    <?php echo $form->dropDownList($model, 'client_id', $ftclients, array('prompt'=>'-- Select --')); ?>
    </td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'client_domain_id'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'client_domain_id'); ?></td>
  </tr>
<?php } ?>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'client_domain'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'client_domain',array('size'=>60,'maxlength'=>255)); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'domain_id'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'domain_id',array('size'=>20,'maxlength'=>20)); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'domain'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'domain',array('size'=>60,'maxlength'=>255)); ?></td>
  </tr>
  <tr>
  	<td class="txtfrm" height="50" ><?php echo $form->label($model,'status'); ?></td>
	<td class="formSearch" >
    <?php echo $form->dropDownList($model, 'status', Cart::$dstatus, array('prompt'=>'-- ALL --')); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'created'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'created'); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'created_by'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'created_by'); ?></td>
  </tr>
  <tr>
    <td><?php echo CHtml::submitButton('Search', array('id' => 'button', 'type' => 'submit' , 'value' => 'Search')); ?>
</td>
  </tr>
</table>
<?php $this->endWidget(); ?>
<!-- search-form -->