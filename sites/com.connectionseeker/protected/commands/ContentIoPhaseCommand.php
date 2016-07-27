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

class ContentIoPhaseCommand extends CConsoleCommand {

    public function run($args) {
        //ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set("memory_limit", "512M");
        $contentios = array("-1"=>"Not Ordering","0"=>"Ideation","1"=>"Idea Approval","2"=>"Place Order",
                            "3"=>"Ordered","4"=>"Content Approval","5"=>"Delivered");

        $types = Types::model()->bytype(array("channel"))->findAll();
        $gtps = CHtml::listData($types, 'refid', 'typename', 'type');
        $channels = $gtps['channel'] ? $gtps['channel'] : array();
        //natcasesort($channels);
        //$channelstr = Utils::array2String($channels);

        $yesterday = date("Y-m-d H:i:s", time() - 86400);

        $content = "";
        for ($i=0; $i<6; $i++) {
            $q = "SELECT t.*, c.name FROM {{inventory_building_task}} AS t
                LEFT JOIN {{campaign}} AS cmp ON (t.campaign_id = cmp.id)
                LEFT JOIN {{client}} AS c ON (cmp.client_id = c.id)
                WHERE t.content_step = '$i' AND t.step_date >= '$yesterday' ";
            $stepios = Yii::app()->db->createCommand($q)->queryAll();

            if ($stepios) {
                $nofnew = count($stepios);
                $steplabel = $contentios[$i];
                $content .= "There are $nofnew new Content IO in $steplabel: <br />";
                $content .= "<table><tr><td>Task #</td><td>Client</td><td>Team Lead</td><td>Anchor Text</td><td>Target URL</td></tr>";
                foreach ($stepios as $s) {
                    //$teamlead = Utils::getValue($channelstr, $s['channel_id'], true);
                    $teamlead = $channels[$s['channel_id']];
                    $content .= "<tr><td>".$s['id']."</td><td>".$s['id']."</td><td>".$teamlead."</td><td>".$s['anchortext']."</td><td>".$s['targeturl']."</td></tr>";
                }

                $content .= "</table><br />";
            }
        }

        Utils::notice(array('content'=>$content, 'tos'=>array("cpconnections@copypress.com"), 'cc'=>false,
                            'subject'=>'Daily Content IO Update'));
    }

}

?>