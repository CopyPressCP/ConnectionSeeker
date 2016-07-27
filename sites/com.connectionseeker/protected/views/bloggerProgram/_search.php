<?php
$form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
));

$_mname = get_class($model);
$operators = array(
    '='  => '=',       // =
    '>'  => '>',    // >
    '>=' => '>=',   // >=
    '<'  => '<',    // <
    '<=' => '<=',   // <=
    '!=' => '!=',      // !=
    //'LIKE'     => 'LIKE',
    //'NOT LIKE' => 'NOT LIKE',
);

?>
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
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'cms_username'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'cms_username',array('size'=>20,'maxlength'=>20)); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'first_name'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'first_name',array('size'=>60,'maxlength'=>100)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'last_name'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'last_name',array('size'=>60,'maxlength'=>100)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'mozauthority'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'mozauthority',array('size'=>9,'maxlength'=>9)); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'syndication'); ?></td>
	<td class="formSearch" >
        <?php
        $htmlOptions = array();
        $htmlOptions['multiple'] = true;
        echo $form->dropDownList($model,'syndication',$syndicationes,$htmlOptions); ?>
    </td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'contact_email'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'contact_email',array('size'=>60,'maxlength'=>255)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'per_word_rate'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'per_word_rate',array('size'=>9,'maxlength'=>9)); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'activeprogram'); ?></td>
	<td class="formSearch" >
        <?php
        $htmlOptions = array();
        $htmlOptions['multiple'] = true;
        echo $form->dropDownList($model,'activeprogram',$_activeprogrames,$htmlOptions); ?>
    </td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'status'); ?></td>
	<td class="formSearch" >
        <?php
        $htmlOptions = array();
        $htmlOptions['multiple'] = true;
        echo $form->dropDownList($model,'status',$bpstatuses,$htmlOptions); ?>
    </td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'category'); ?></td>
	<td class="formSearch" >
        <?php
        $htmlOptions = array();
        $htmlOptions['multiple'] = true;
        echo $form->dropDownList($model,'category',$_bloggerprogrames,$htmlOptions); ?>
    </td>
  </tr>

  <tr>
	<td class="txtfrm" height="50" ><?php echo CHtml::label('Last Published', $_mname.'_last_published'); ?></td>
	<td class="formSearch" >
        <?php echo CHtml::dropDownList($_mname.'[last_publishedopr]',$_GET[$_mname]['last_publishedopr'],$operators); ?>
        <?php 
        $this->widget('zii.widgets.jui.CJuiDatePicker', array(
            'model'=>$model,
            'name' => 'BloggerProgram[last_published]',
            'value' => date("Y-m-d", time() - 2592000), 
            // additional javascript options for the date picker plugin
            'options'=>array(
                'showAnim'=>'fold',
                'dateFormat'=>'yy-mm-dd',
                'changeMonth' => 'true',
                'changeYear'=>'true',
                'constrainInput' => 'false',
            ),
            'htmlOptions'=>array(
                'style'=>'width:100px;',
            ),
            // DONT FORGET TO ADD TRUE this will create the datepicker return as string
        ));
        ?>
    </td>
	<td class="txtfrm" height="50" ></td>
	<td class="formSearch" ></td>
	<td class="txtfrm" height="50" ></td>
	<td class="formSearch" ></td>
  </tr>

  <tr>
    <td>
<div class="form">
<div class="row buttons">
<?php echo CHtml::submitButton('Search', array('id' => 'searchBloggerProgram', 'type' => 'submit' , 'value' => 'Search')); ?>
</div>
</div>
</td>
  </tr>
</table>
<?php $this->endWidget(); ?>
<!-- search-form -->