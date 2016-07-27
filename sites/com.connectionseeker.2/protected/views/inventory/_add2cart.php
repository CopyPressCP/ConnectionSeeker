<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("cart/add"),
	'method'=>'post',
	'id'=>'addDomain2CartForm',
));
?>

<div class="form">
  <div class="errorSummary" id="add2carterror" style="display:none;">
  <p>Please fix the following input errors:</p>
  <ul></ul>
  </div>
</div>

<input type="hidden" value="" name="Cart[inventory_ids]" id="Cart_inventory_ids">

<table border="0" align="left" cellpadding="0" cellspacing="0">
  <tr>
	<td class="formSearch" >
        <?php //echo CHtml::label('Clients', 'Task_client_id');?>
        <?php
        if (isset($roles['Marketer'])) {
            echo "<input type='hidden' value='{$clients}' name='Cart[client_id]' id='Cart_client_id'>";
        } else {
            $htmlOptions = array();
            $htmlOptions['prompt'] = '-- Select Client --';
            $htmlOptions['ajax'] = array(
                'type'=>'GET', //request type
                'url'=>Yii::app()->createUrl('clientDomain/domains'),
                //'url'=>Yii::app()->createUrl('client/campaigns'),
                'dataType'=>"json",
                'data'=>array(Yii::app()->request->csrfTokenName => Yii::app()->request->getCsrfToken(),
                              'client_id' =>'js:$("#Cart_client_id").val()',
                              'format'  => 'html4dropdown'),
                //leave out the data key to pass all form values through
                'success' => 'function(html){jQuery("#Cart_client_domain_id").html(html.domains);}',
            );
            echo CHtml::dropDownList('Cart[client_id]', $_GET['Cart']['client_id'], CHtml::listData($clients,'id','company'),$htmlOptions);
        }
        ?>
    </td>
	<td class="formSearch" >
      <?php echo CHtml::dropDownList('Cart[client_domain_id]', $_GET['Cart']['client_domain_id'], $domains, array('prompt'=>'-- Select Domain --')); ?></td>

    <td>
       <div class="buttons"> &nbsp;&nbsp;
       <?php //echo CHtml::submitButton('Add to Cart', array('id' => 'add2cart', 'type' => 'submit', 'value' => 'Add to Cart')); ?>
       <?php echo CHtml::Button('Add to Cart', array('id' => 'add2cart', 'type' => 'button', 'value' => 'Add to Cart')); ?>
       </div>
    </td>
  </tr>
</table>

<?php $this->endWidget(); ?>
<!-- search-form -->
