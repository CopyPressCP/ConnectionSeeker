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
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'domain'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'domain',array('size'=>60,'maxlength'=>255)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'client_id'); ?>
</td>
	<td class="formSearch" ><?php
        $htmlOptions = array();
        $htmlOptions['prompt'] = '-- select --';
        echo $form->dropDownList($model, 'client_id', $ftclients, $htmlOptions);
    ?></td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'created'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'created'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'created_by'); ?>
</td>
	<td class="formSearch" ><?php
        $htmlOptions = array();
        $htmlOptions['prompt'] = '-- select --';
        echo $form->dropDownList($model, 'created_by', $ftusers, $htmlOptions);
    ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'modified'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'modified'); ?>
</td>
  </tr>

  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'modified_by'); ?></td>
	<td class="formSearch" ><?php
        //echo $form->textField($model,'modified_by');
        $htmlOptions = array();
        $htmlOptions['prompt'] = '-- select --';
        echo $form->dropDownList($model, 'modified_by', $ftusers, $htmlOptions);
    ?></td>
  </tr>
  <tr>
    <td><?php echo CHtml::submitButton('Search', array('id' => 'button', 'type' => 'submit' , 'value' => 'Search')); ?>
</td>
  </tr>
</table>
<?php $this->endWidget(); ?>
<!-- search-form -->