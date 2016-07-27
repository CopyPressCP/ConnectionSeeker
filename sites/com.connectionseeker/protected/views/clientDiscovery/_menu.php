<?php
if ($this->action->id == "index") {
    $this->widget('zii.widgets.CMenu', array(
        'firstItemCssClass'=>'first',
        'lastItemCssClass'=>'last',
        'htmlOptions'=>array('class'=>'actions'),
        'items'=>array(
            array(
                'label'=>Menus::t('core', 'Create New Email Task'),
                'url'=>array('clientDiscovery/create'),
                //'itemOptions'=>array('class'=>'item1'),
            ),
        )
    ));
} else {
    $this->widget('zii.widgets.CMenu', array(
        'firstItemCssClass'=>'first',
        'lastItemCssClass'=>'last',
        'htmlOptions'=>array('class'=>'actions'),
        'items'=>array(
            array(
                'label'=>Menus::t('core', 'Manage Email Tasks'),
                'url'=>array('clientDiscovery/index'),
            ),
            array(
                'label'=>Menus::t('core', 'Clone This Email Tasks'),
                'url'=>array('clientDiscovery/cloneit','id'=>$_GET['id']),
                'visible'=>($this->action->id=='update'),
            ),
        )
    ));
}
?>
<div class="clear"></div>