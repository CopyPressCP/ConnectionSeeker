<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('competitor_id')); ?>:</b>
	<?php echo CHtml::encode($data->competitor_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('domain_id')); ?>:</b>
	<?php echo CHtml::encode($data->domain_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('fresh_called')); ?>:</b>
	<?php echo CHtml::encode($data->fresh_called); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('historic_called')); ?>:</b>
	<?php echo CHtml::encode($data->historic_called); ?>
	<br />


</div>