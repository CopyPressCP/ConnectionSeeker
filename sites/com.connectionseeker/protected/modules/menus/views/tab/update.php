<?php $this->breadcrumbs = array(
	'Menus'=>Menus::getBaseUrl(),
	Menus::getMenuTypeNamePlural($model->type)=>Menus::getMenuRoute($model->type),
	$model->name,
); ?>

<div id="updatedAuthItem">

	<h2><?php echo Menus::t('core', 'Update :name', array(
		':name'=>$model->name,
		':type'=>Menus::getMenuTypeName($model->type),
	)); ?></h2>

	<?php $this->renderPartial('/tab/_form', array('model'=>$formModel)); ?>

	<div class="relations span-11 last">

		<h3><?php echo Menus::t('core', 'Relations'); ?></h3>

		<?php if( $model->name!==Menus::module()->superuserName ): ?>

			<div class="parents">

				<h4><?php echo Menus::t('core', 'Parents'); ?></h4>

				<?php $this->widget('zii.widgets.grid.CGridView', array(
					'dataProvider'=>$parentDataProvider,
					'template'=>'{items}',
					'hideHeader'=>true,
					'emptyText'=>Menus::t('core', 'This item has no parents.'),
					'htmlOptions'=>array('class'=>'grid-view parent-table mini'),
					'columns'=>array(
    					array(
    						'name'=>'name',
    						'header'=>Menus::t('core', 'Name'),
    						'type'=>'raw',
    						'htmlOptions'=>array('class'=>'name-column'),
    						'value'=>'$data->getNameLink()',
    					),
    					array(
    						'name'=>'type',
    						'header'=>Menus::t('core', 'Type'),
    						'type'=>'raw',
    						'htmlOptions'=>array('class'=>'type-column'),
    						'value'=>'$data->getTypeText()',
    					),
    					array(
    						'header'=>'&nbsp;',
    						'type'=>'raw',
    						'htmlOptions'=>array('class'=>'actions-column'),
    						'value'=>'',
    					),
					)
				)); ?>

			</div>
			<div class="children">

				<h4><?php  echo Menus::t('core', 'Children'); ?></h4>

				<?php $this->widget('zii.widgets.grid.CGridView', array(
					'dataProvider'=>$childDataProvider,
					'template'=>'{items}',
					'hideHeader'=>true,
					'emptyText'=>Menus::t('core', 'This item has no children.'),
					'htmlOptions'=>array('class'=>'grid-view parent-table mini'),
					'columns'=>array(
    					array(
    						'name'=>'name',
    						'header'=>Menus::t('core', 'Name'),
    						'type'=>'raw',
    						'htmlOptions'=>array('class'=>'name-column'),
    						'value'=>'$data->getNameLink()',
    					),
    					array(
    						'name'=>'type',
    						'header'=>Menus::t('core', 'Type'),
    						'type'=>'raw',
    						'htmlOptions'=>array('class'=>'type-column'),
    						'value'=>'$data->getTypeText()',
    					),
    					array(
    						'header'=>'&nbsp;',
    						'type'=>'raw',
    						'htmlOptions'=>array('class'=>'actions-column'),
    						'value'=>'$data->getRemoveChildLink()',
    					),
					)
				)); ?>

			</div>

			<div class="addChild">

				<h5><?php echo Menus::t('core', 'Add Child'); ?></h5>

				<?php if( $childFormModel!==null ): ?>

					<?php $this->renderPartial('/tab/_childForm', array(
						'model'=>$childFormModel,
						'itemnameSelectOptions'=>$childSelectOptions,
					)); ?>

				<?php else: ?>

					<p class="info"><?php echo Menus::t('core', 'No children available to be added to this item.'); ?>

				<?php endif; ?>

			</div>
		<?php else: ?>

			<p class="info">
				<?php echo Menus::t('core', 'No relations need to be set for the superuser role.'); ?><br />
				<?php echo Menus::t('core', 'Super users are always granted access implicitly.'); ?>
			</p>

		<?php endif; ?>

	</div>

</div>