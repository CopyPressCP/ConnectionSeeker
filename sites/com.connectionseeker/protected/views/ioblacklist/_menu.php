<?php
$this->widget('zii.widgets.CMenu', array(
    'firstItemCssClass'=>'first',
    'lastItemCssClass'=>'last',
    'htmlOptions'=>array('class'=>'actions'),
    'items'=>array(
        array(
            'label'=>Menus::t('core', 'Add a New Site'),
            'url'=>array('ioblacklist/create'),
        ),
        array(
            'label'=>Menus::t('core', 'Manage Blacklist'),
            'url'=>array('ioblacklist/index'),
        ),
    )
));
?>
<div class="clear"></div>