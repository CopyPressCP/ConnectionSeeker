<?php
//http://www.yiiframework.com/wiki/91/implementing-cron-jobs-with-yii/
//http://www.yiiframework.com/forum/index.php?/topic/26551-setting-up-cronjob/
//How to call this script in crontab: php /path/to/cron.php syncTopBacklinks
//http://hudeyong926.iteye.com/blog/1283125 路径引用总结
/*
D:\WEBIDE\Language\php>php -q K:\NewHtdocs\yii\yii1.1.8.dev\sites\com.connectionseeker\cron.php NoticeStep p1 p2
the $args will returns as following
array(
    [0] => p1
    [1] => p2
)
*/

Yii::import('application.vendors.*');
define('DS', DIRECTORY_SEPARATOR);
error_reporting(E_ALL);

class NoticeStepCommand extends CConsoleCommand {

    public function run($args) {
        //ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set("memory_limit", "512M");

        $q = "SELECT t.*, c.name FROM {{inventory_building_task}} AS t
            LEFT JOIN {{campaign}} AS cmp ON (t.campaign_id = cmp.id)
            LEFT JOIN {{client}} AS c ON (cmp.client_id = c.id)
            WHERE t.content_step = '1'";
        $stepios = Yii::app()->db->createCommand($q)->queryAll();

        if ($stepios) {
            $types = Types::model()->bytype(array("channel"))->findAll();
            $gtps = CHtml::listData($types, 'refid', 'typename', 'type');
            $channels = $gtps['channel'] ? $gtps['channel'] : array();
            //natcasesort($channels);
            //$channelstr = Utils::array2String($channels);

            $content = "<table><tr><td>Task #</td><td>Client</td><td>Team Lead</td><td>Anchor Text</td><td>Target URL</td></tr>";
            foreach ($stepios as $s) {
                //$teamlead = Utils::getValue($channelstr, $s['channel_id'], true);
                $teamlead = $channels[$s['channel_id']];
                $content .= "<tr><td>".$s['id']."</td><td>".$s['id']."</td><td>".$teamlead."</td><td>".$s['anchortext']."</td><td>".$s['targeturl']."</td></tr>";
            }

            $content .= "</table>";
            Utils::notice(array('content'=>$content, 'tos'=>array("contentio@copypress.com"), 'cc'=>false,
                                'subject'=>'Daily Content IO Update'));
        }

    }

}

?>