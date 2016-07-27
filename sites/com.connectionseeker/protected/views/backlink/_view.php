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

	<b><?php echo CHtml::encode($data->getAttributeLabel('url')); ?>:</b>
	<?php echo CHtml::encode($data->url); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('domain')); ?>:</b>
	<?php echo CHtml::encode($data->domain); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('googlepr')); ?>:</b>
	<?php echo CHtml::encode($data->googlepr); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('acrank')); ?>:</b>
	<?php echo CHtml::encode($data->acrank); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('anchortext')); ?>:</b>
	<?php echo CHtml::encode($data->anchortext); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('date')); ?>:</b>
	<?php echo CHtml::encode($data->date); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('flagredirect')); ?>:</b>
	<?php echo CHtml::encode($data->flagredirect); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('flagframe')); ?>:</b>
	<?php echo CHtml::encode($data->flagframe); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('flagnofollow')); ?>:</b>
	<?php echo CHtml::encode($data->flagnofollow); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('flagimages')); ?>:</b>
	<?php echo CHtml::encode($data->flagimages); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('flagdeleted')); ?>:</b>
	<?php echo CHtml::encode($data->flagdeleted); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('flagalttext')); ?>:</b>
	<?php echo CHtml::encode($data->flagalttext); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('flagmention')); ?>:</b>
	<?php echo CHtml::encode($data->flagmention); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('targeturl')); ?>:</b>
	<?php echo CHtml::encode($data->targeturl); ?>
	<br />

	*/ ?>

</div>