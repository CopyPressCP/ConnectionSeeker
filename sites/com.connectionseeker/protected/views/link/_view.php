<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('inventory_id')); ?>:</b>
	<?php echo CHtml::encode($data->inventory_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('sourceurl')); ?>:</b>
	<?php echo CHtml::encode($data->sourceurl); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('campaign_id')); ?>:</b>
	<?php echo CHtml::encode($data->campaign_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('targeturl')); ?>:</b>
	<?php echo CHtml::encode($data->targeturl); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('targetdomain')); ?>:</b>
	<?php echo CHtml::encode($data->targetdomain); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('anchortext')); ?>:</b>
	<?php echo CHtml::encode($data->anchortext); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('category_id')); ?>:</b>
	<?php echo CHtml::encode($data->category_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('tasktype_id')); ?>:</b>
	<?php echo CHtml::encode($data->tasktype_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('status')); ?>:</b>
	<?php echo CHtml::encode($data->status); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('checked')); ?>:</b>
	<?php echo CHtml::encode($data->checked); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('notes')); ?>:</b>
	<?php echo CHtml::encode($data->notes); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('created')); ?>:</b>
	<?php echo CHtml::encode($data->created); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('created_by')); ?>:</b>
	<?php echo CHtml::encode($data->created_by); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('modified')); ?>:</b>
	<?php echo CHtml::encode($data->modified); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('modified_by')); ?>:</b>
	<?php echo CHtml::encode($data->modified_by); ?>
	<br />

	*/ ?>

</div>