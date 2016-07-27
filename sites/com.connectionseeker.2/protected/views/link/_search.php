<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
<table border="0" align="left" cellpadding="0" cellspacing="0">
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'id'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'id'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'inventory_id'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'inventory_id'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'sourceurl'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'sourceurl',array('size'=>60,'maxlength'=>500)); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'campaign_id'); ?>
</td>
	<td class="formSearch" >
    <?php //echo $form->textField($model,'campaign_id'); ?>
    <?php echo $form->dropDownList($model,'campaign_id',$campaigns,array('prompt'=>'-- Campaign --')); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'targeturl'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'targeturl',array('size'=>60,'maxlength'=>500)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'targetdomain'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'targetdomain',array('size'=>60,'maxlength'=>255)); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'anchortext'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textArea($model,'anchortext',array('rows'=>6, 'cols'=>50)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'category_id'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'category_id'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'tasktype_id'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'tasktype_id'); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'status'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'status'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'checked'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'checked'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'notes'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textArea($model,'notes',array('rows'=>6, 'cols'=>50)); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'created'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'created'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'created_by'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'created_by'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'modified'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'modified'); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'modified_by'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'modified_by'); ?>
</td>
  </tr>
  <tr>
    <td><?php echo CHtml::submitButton('Search', array('id' => 'button', 'type' => 'submit' , 'value' => 'Search')); ?>
</td>
  </tr>
</table>
<?php $this->endWidget(); ?>
<!-- search-form -->