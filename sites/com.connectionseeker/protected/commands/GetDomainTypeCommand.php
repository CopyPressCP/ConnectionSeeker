<?php
//http://www.yiiframework.com/wiki/91/implementing-cron-jobs-with-yii/
//http://www.yiiframework.com/forum/index.php?/topic/26551-setting-up-cronjob/
//How to call this script in crontab: php /path/to/cron.php GetDomainType
//http://hudeyong926.iteye.com/blog/1283125 路径引用总结
/*
I:\xampp\php>php I:\htdocs\connectionseeker\cron.php GetDomainType p1
the $args will returns as following
array(
    [0] => p1
)
Please query this sql before start to run this cronjob
#added by nancy xu 2012-08-31 11:19
ALTER TABLE `lkm_domain` ADD `sitetype` VARCHAR( 255 ) NULL AFTER `street` ,
ADD `scaned` DATETIME NULL AFTER `sitetype` ;
#end
*/
Yii::import('application.vendors.*');
require_once(Yii::app()->BasePath . "/vendors/simplehtmldom/simple_html_dom.php");
class GetDomainTypeCommand extends CConsoleCommand {
    public function run($args) {
        //ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set("memory_limit", "256M");

        $yesterday = time() - 86400;
        $yesterday = date("Y-m-d H:i:s", $yesterday);

        $q = "UPDATE lkm_domain AS d LEFT JOIN lkm_domain_onpage AS do ON(d.id=do.domain_id) SET d.stype = 6 WHERE do.lastcrawled > '$yesterday'";
        Yii::app()->db->createCommand($q)->execute();

        $q = "UPDATE lkm_domain AS d LEFT JOIN lkm_domain_onpage AS do ON(d.id=do.domain_id) SET d.stype = 5 WHERE char_length(do.magento)>3 AND do.lastcrawled > '$yesterday'";
        Yii::app()->db->createCommand($q)->execute();

        $q = "UPDATE lkm_domain AS d LEFT JOIN lkm_domain_onpage AS do ON(d.id=do.domain_id) SET d.stype = 4 WHERE char_length(do.drupal)>3 AND do.lastcrawled > '$yesterday'";
        Yii::app()->db->createCommand($q)->execute();

        $q = "UPDATE lkm_domain AS d LEFT JOIN lkm_domain_onpage AS do ON(d.id=do.domain_id) SET d.stype = 2 WHERE char_length(do.wordpress)>3 AND do.lastcrawled > '$yesterday'";
        Yii::app()->db->createCommand($q)->execute();

        $q = "UPDATE lkm_domain AS d LEFT JOIN lkm_domain_onpage AS do ON(d.id=do.domain_id) SET d.stype = 1 WHERE (do.wordpress_registration IS NOT NULL AND do.wordpress_registration != '' AND do.wordpress_registration != 0) AND do.lastcrawled > '$yesterday'";
        Yii::app()->db->createCommand($q)->execute();
    }

}
?>