<?php $this->breadcrumbs = array(
	'Menus'=>Menus::getBaseUrl(),
	Menus::t('core', 'Create Menu'),
); ?>

<div class="createAuthItem">

	<h2><?php echo Menus::t('core', 'Create Menu'); ?></h2>

	<?php $this->renderPartial('tab/_form', array('model'=>$formModel)); ?>

</div>