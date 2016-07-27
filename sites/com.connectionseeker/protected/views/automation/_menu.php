<?php
if (isset($roles["Admin"])) {
    $this->widget('zii.widgets.CMenu', array(
        'firstItemCssClass'=>'first',
        'lastItemCssClass'=>'last',
        'htmlOptions'=>array('class'=>'actions'),
        'items'=>array(
            array(
                'label'=>Menus::t('core', 'Create Automation'),
                'url'=>array('automation/create'),
            ),
            array(
                'label'=>Menus::t('core', 'Manage Automation'),
                'url'=>array('automation/index'),
            ),
        )
    ));
} else {

}
?>
<div class="clear"></div>