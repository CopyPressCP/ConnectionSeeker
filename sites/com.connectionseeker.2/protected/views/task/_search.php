<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
    'id'=>'taskSearchForm',
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
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'campaign_id'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'campaign_id'); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'domain'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'domain',array('size'=>60,'maxlength'=>255)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'domain_id'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'domain_id',array('size'=>20,'maxlength'=>20)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'anchortext'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textArea($model,'anchortext',array('rows'=>6, 'cols'=>50)); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'targeturl'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'targeturl',array('size'=>60,'maxlength'=>255)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'sourceurl'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'sourceurl',array('size'=>60,'maxlength'=>255)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'sourcedomain'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'sourcedomain',array('size'=>60,'maxlength'=>255)); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'title'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textArea($model,'title',array('rows'=>6, 'cols'=>50)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'tasktype'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'tasktype'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'taskstatus'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'taskstatus',array('size'=>50,'maxlength'=>50)); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'assignee'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'assignee'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'optional_keywords'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textArea($model,'optional_keywords',array('rows'=>6, 'cols'=>50)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'mapping_id'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'mapping_id',array('size'=>60,'maxlength'=>255)); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'notes'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textArea($model,'notes',array('rows'=>6, 'cols'=>50)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'duedate'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'duedate'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'content_article_id'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'content_article_id'); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'content_campaign_id'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'content_campaign_id'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'content_category_id'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'content_category_id'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'send2cpdate'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'send2cpdate'); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'checkouted'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'checkouted'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'created'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'created'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'created_by'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'created_by'); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'modified'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'modified'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'modified_by'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'modified_by'); ?>
</td>
  </tr>
</table>
<table border="0" align="left" cellpadding="0" cellspacing="0">
  <tr>
    <td>
       <div class="form">
       <div class="row buttons"> &nbsp;&nbsp;
       <?php echo CHtml::submitButton('Search', array('id' => 'button', 'type' => 'submit', 'value' => 'Search')); ?>
       </div>
       </div>
    </td>
    <td>
       <div class="form">
       <div class="row buttons"> &nbsp;&nbsp;
       <?php echo CHtml::Button('Export', array('id' => 'downloadTask', 'type' => 'button', 'value' => 'Export')); ?>
       </div>
       </div>
    </td>
  </tr>
</table>
<?php $this->endWidget(); ?>
<!-- search-form -->