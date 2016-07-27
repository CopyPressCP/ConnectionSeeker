<?php
/**
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
$i=0;
?>
<?php echo "<?php \$form=\$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl(\$this->route),
	'method'=>'get',
)); ?>\n"; ?>
<table border="0" align="left" cellpadding="0" cellspacing="0">
  <tr>
<?php foreach($this->tableSchema->columns as $column): ?>
<?php
    //$column['htmlOptions'] = array('class' => 'search');
	$field=$this->generateInputField($this->modelClass,$column);
	if(strpos($field,'password')!==false)
		continue;
    if ($i> 0 && $i%3==0) {
?>
  </tr>
  <tr>
<?php
    }
    $i++;
?>
	<td class="txtfrm" height="50" ><?php echo "<?php echo \$form->label(\$model,'{$column->name}'); ?>\n"; ?></td>
	<td class="formSearch" ><?php echo "<?php echo ".$this->generateActiveField($this->modelClass,$column)."; ?>\n"; ?></td>
<?php endforeach; ?>
  </tr>
  <tr>
    <td><?php echo "<?php echo CHtml::submitButton('Search', array('id' => 'button', 'type' => 'submit' , 'value' => 'Search')); ?>\n"; ?></td>
  </tr>
</table>
<?php echo "<?php \$this->endWidget(); ?>\n"; ?>
<!-- search-form -->