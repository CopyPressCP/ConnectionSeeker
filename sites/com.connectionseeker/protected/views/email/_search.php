<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
    'id'=>'emailSearchForm',
)); ?>
<table border="0" align="left" cellpadding="0" cellspacing="0">
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'id'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'id'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'domain_id'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'domain_id'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'template_id'); ?>
</td>
	<td class="formSearch" ><?php
        $htmlOptions = array();
        $htmlOptions['prompt'] = '-- select --';
        echo $form->dropDownList($model, 'template_id', $fttemplates, $htmlOptions);
    ?></td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'from'); ?>
</td>
	<td class="formSearch" ><?php
        $htmlOptions = array();
        $htmlOptions['prompt'] = '-- select --';
        echo $form->dropDownList($model, 'from', $ftfromes, $htmlOptions);
    ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'to'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'to',array('size'=>60,'maxlength'=>255)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'cc'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'cc'); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'subject'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'subject',array('size'=>60,'maxlength'=>1000)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'content'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textArea($model,'content',array('rows'=>6, 'cols'=>50)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'status'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'status'); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'send_time'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'send_time'); ?>
</td>
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
	<td class="txtfrm" height="50" ><?php //echo $form->label($model,'event'); ?></td>
	<td class="formSearch" ><?php
        /* $htmlOptions = array();
        $htmlOptions['prompt'] = '-- select --';
        echo $form->dropDownList($model, 'event', $events, $htmlOptions);*/
    ?></td>
 </tr>
  <tr>
    <td>
        <div class="form">
            <div class="row buttons"> 
            <?php echo CHtml::submitButton('Search', array('id' => 'searchEmails', 'type' => 'submit' , 'value' => 'Search')); ?>
            </div>
        </div>
    </td>
    <td>
        <div class="form">
            <div class="row buttons"> 
            <?php echo CHtml::Button('Download', array('id' => 'downloadEmails', 'type' => 'button', 'value' => 'Download')); ?>
            </div>
        </div>
    </td>
  </tr>
</table>
<?php $this->endWidget(); ?>
<!-- search-form -->