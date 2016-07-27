<?php $this->breadcrumbs = array(
	'Menus'=>Menus::getBaseUrl(),
	Menus::t('core', 'Generate items'),
);
?>

<div id="generator">

	<h2><?php echo Menus::t('core', 'Generate items'); ?></h2>

	<p><?php echo Menus::t('core', 'Please select which items you wish to generate.'); ?></p>

	<div class="form">

		<?php $form=$this->beginWidget('CActiveForm'); ?>

			<div class="row">

				<table class="items generate-item-table" border="0" cellpadding="0" cellspacing="0">

					<tbody>

						<tr class="application-heading-row">
							<th colspan="3"><?php echo Menus::t('core', 'Application'); ?></th>
						</tr>

						<?php $this->renderPartial('tab/_generateItems', array(
							'model'=>$model,
							'form'=>$form,
							'items'=>$items,
							'authItems'=>$authItems,
							'existingItems'=>$existingItems,
							'displayModuleHeadingRow'=>true,
							'basePathLength'=>strlen(Yii::app()->basePath),
						)); ?>

					</tbody>

				</table>

			</div>

			<div class="row">

   				<?php echo CHtml::link(Menus::t('core', 'Select all'), '#', array(
   					'onclick'=>"jQuery('.generate-item-table').find(':checkbox').attr('checked', 'checked'); return false;",
   					'class'=>'selectAllLink')); ?>
   				/
				<?php echo CHtml::link(Menus::t('core', 'Select none'), '#', array(
					'onclick'=>"jQuery('.generate-item-table').find(':checkbox').removeAttr('checked'); return false;",
					'class'=>'selectNoneLink')); ?>

			</div>

   			<div class="row">

				<?php echo CHtml::submitButton(Menus::t('core', 'Generate')); ?>

			</div>

		<?php $this->endWidget(); ?>

	</div>

</div>