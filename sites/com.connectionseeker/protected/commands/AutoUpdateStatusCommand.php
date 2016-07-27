<?php
//http://www.yiiframework.com/wiki/91/implementing-cron-jobs-with-yii/
//http://www.yiiframework.com/forum/index.php?/topic/26551-setting-up-cronjob/
//How to call this script in crontab: php /path/to/cron.php syncTopBacklinks
//http://hudeyong926.iteye.com/blog/1283125 路径引用总结
/*
D:\WEBIDE\Language\php>php -q F:\NewHtdocs\sites\yii\connectionseeker\cron.php AutoUpdateStatus
the $args will returns as following
array(
    [0] => $field
    [1] => $num
)
*/
Yii::import('application.vendors.*');

class AutoUpdateStatusCommand extends CConsoleCommand {
    public function run($args) {
        //ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set("memory_limit", "256M");

        $now = time();
        $coupledayago = $now - 86400 * 14;//2 weeks later
        $touched = date("Y-m-d H:i:s",$now);
        $thedate = date("Y-m-d H:i:s",$coupledayago);

        echo $q = "UPDATE {{domain}} SET touched_status='11', touched='".$touched."', modified_by='1', modified='".$touched
            ."' WHERE touched_status = '2' AND status='1' AND (touched<='".$thedate."' OR touched IS NULL)";
        Yii::app()->db->createCommand($q)->execute();

        echo "\r\n----------------------------------------------------\r\n";
        echo $q = "UPDATE {{domain}} SET touched_status='18', touched='".$touched."', modified_by='1', modified='".$touched
            ."' WHERE touched_status = '19' AND status='1' AND (touched<='".$thedate."' OR touched IS NULL)";
        Yii::app()->db->createCommand($q)->execute();
    }

}
?>