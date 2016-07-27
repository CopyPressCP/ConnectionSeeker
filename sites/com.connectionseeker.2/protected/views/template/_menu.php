<?php
if (isset($roles["Outreach"]) || isset($roles["InternalOutreach"])) {
    $this->widget('zii.widgets.CMenu', array(
        'firstItemCssClass'=>'first',
        'lastItemCssClass'=>'last',
        'htmlOptions'=>array('class'=>'actions'),
        'items'=>array(
            array(
                'label'=>Menus::t('core', 'Create Template'),
                'url'=>array('setting/createTemplate'),
                //'itemOptions'=>array('class'=>'item1'),
            ),
            array(
                'label'=>Menus::t('core', 'Manage Template'),
                'url'=>array('setting/template'),
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
                'label'=>Menus::t('core', 'Create Mailer'),
                'url'=>array('mailer/create'),
                //'itemOptions'=>array('class'=>'item1'),
            ),
            array(
                'label'=>Menus::t('core', 'Manage Mailer'),
                'url'=>array('mailer/index'),
            ),
            array(
                'label'=>Menus::t('core', 'Create Template'),
                'url'=>array('template/create'),
            ),
            array(
                'label'=>Menus::t('core', 'Manage Template'),
                'url'=>array('template/index'),
            )
        )
    ));
}
?>
<div class="clear"></div>