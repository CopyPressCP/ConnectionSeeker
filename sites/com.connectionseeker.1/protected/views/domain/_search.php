<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
<table border="0" align="left" cellpadding="0" cellspacing="0">
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'id'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'id',array('size'=>20,'maxlength'=>20)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'domain'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'domain',array('size'=>60,'maxlength'=>255)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'tld'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'tld',array('size'=>10,'maxlength'=>10)); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'googlepr'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'googlepr'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'onlinesince'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'onlinesince'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'linkingdomains'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'linkingdomains',array('size'=>20,'maxlength'=>20)); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'inboundlinks'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'inboundlinks',array('size'=>20,'maxlength'=>20)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'indexedurls'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'indexedurls',array('size'=>20,'maxlength'=>20)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'alexarank'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'alexarank',array('size'=>20,'maxlength'=>20)); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'ip'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'ip',array('size'=>32,'maxlength'=>32)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'subnet'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'subnet',array('size'=>32,'maxlength'=>32)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'title'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>255)); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'owner'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'owner',array('size'=>60,'maxlength'=>255)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'email'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>255)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'telephone'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'telephone',array('size'=>60,'maxlength'=>255)); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'country'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'country',array('size'=>60,'maxlength'=>64)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'state'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'state',array('size'=>60,'maxlength'=>128)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'city'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'city',array('size'=>60,'maxlength'=>128)); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'zip'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'zip',array('size'=>60,'maxlength'=>64)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'street'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'street',array('size'=>60,'maxlength'=>64)); ?>
    <?php //echo $form->textArea($model,'street',array('rows'=>6, 'cols'=>50)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'stype'); ?>
</td>
	<td class="formSearch" ><?php
    $htmlOptions = array();
    $htmlOptions['prompt'] = '[Site Type]';
    echo $form->dropDownList($model, 'stype', $stypes, $htmlOptions);
    ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'otype'); ?>
</td>
	<td class="formSearch" ><?php
    $htmlOptions = array();
    $htmlOptions['prompt'] = '[Outreach Type]';
    echo $form->dropDownList($model, 'otype', $otypes, $htmlOptions);
    ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'touched'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'touched'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'touched_status'); ?>
</td>
	<td class="formSearch" ><?php
    $htmlOptions = array();
    $htmlOptions['prompt'] = '[Status]';
    echo $form->dropDownList($model, 'touched_status', $touchedstatus, $htmlOptions);
    ?>
</td>
  </tr>
  <!-- <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'touched_by'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'touched_by'); ?>
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
  </tr> -->
  <tr>
    <td><?php echo CHtml::submitButton('Search', array('id' => 'button', 'type' => 'submit' , 'value' => 'Search')); ?>
</td>
  </tr>
</table>
<?php $this->endWidget(); ?>
<!-- search-form -->