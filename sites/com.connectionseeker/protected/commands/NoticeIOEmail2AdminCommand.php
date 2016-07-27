<?php
//http://www.yiiframework.com/wiki/91/implementing-cron-jobs-with-yii/
//http://www.yiiframework.com/forum/index.php?/topic/26551-setting-up-cronjob/
//How to call this script in crontab: php /path/to/cron.php syncTopBacklinks
//http://hudeyong926.iteye.com/blog/1283125 路径引用总结
/*
D:\WEBIDE\Language\php>php -q K:\NewHtdocs\yii\yii1.1.8.dev\sites\com.connectionseeker\cron.php NoticeIOEmail2Admin p1 p2
the $args will returns as following
array(
    [0] => p1
    [1] => p2
)
*/

Yii::import('application.vendors.*');
define('DS', DIRECTORY_SEPARATOR);
error_reporting(E_ALL);

class NoticeIOEmail2AdminCommand extends CConsoleCommand {

    public function run($args) {
        //ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set("memory_limit", "512M");

        /*
        $admins = array("lliu@steelcast.com", "kzipp@copypress.com", "jseedhouse@copypress.com",
                        "twyher@copypress.com", "ilucea@copypress.com");
        */
        $admins = array("yesterdaysdata@copypress.com");

        $now = time();
        $onedayago = $now - 86400;
        $yesterday = date("Y-m-d H:i:s", $onedayago);

        $content = "** All the data below is for the past 24 hours **<br />";

        $q = "SELECT COUNT( * ) AS total
            FROM lkm_inventory
            WHERE acquireddate >= '{$yesterday}'";
        $totalacquired = Yii::app()->db->createCommand($q)->queryRow();
        $totalac = 0;
        if ($totalacquired) $totalac = $totalacquired['total'];
        $content .= "Total Sites Acquired: $totalac <br />";

        $q = "SELECT COUNT( * ) AS iocount, oldiostatus, iostatus
            FROM lkm_io_history
            WHERE created >= '{$yesterday}'
            GROUP BY oldiostatus, iostatus";
        $taskscount = Yii::app()->db->createCommand($q)->queryAll();
        if ($taskscount) {
            foreach ($taskscount as $tc) {
                if ($tc["iostatus"] == 21) {
                    $content .= "Total Sites moved into pending: ".$tc["iocount"]." <br />";
                } else if($tc["oldiostatus"] == 21 && $tc["iostatus"] == 3) {
                    $content .= "Total Sites moved FROM pending TO Approved: ".$tc["iocount"]." <br />";
                } else if($tc["oldiostatus"] == 21 && $tc["iostatus"] == 2) {
                    $content .= "Total Sites moved FROM pending TO Accepted: ".$tc["iocount"]." <br />";
                } else if($tc["oldiostatus"] == 3 && $tc["iostatus"] == 31) {
                    $content .= "Total Sites moved FROM Approved TO Pre QA: ".$tc["iocount"]." <br />";
                } else if($tc["oldiostatus"] == 3 && $tc["iostatus"] == 2) {
                    $content .= "Total Sites moved FROM Approved TO Accepted: ".$tc["iocount"]." <br />";
                }
            }
        }

        $q = "SELECT COUNT( DISTINCT model_id ) AS total  FROM `lkm_operation_trail` 
            WHERE created>'{$yesterday}' 
            AND `new_value` LIKE '%s:8:".'"sentdate";s:10:"2%'."' AND model='Task'";
        $contentsent = Yii::app()->db->createCommand($q)->queryRow();
        $totalcs = 0;
        if ($contentsent) $totalcs = $contentsent['total'];
        $content .= "Total pieces of content sent: $totalcs <br />";

        //##24 hours of Emails Sent, Open, and Received
        $q = "SELECT COUNT( * ) AS total  FROM `lkm_email_queue` 
            WHERE send_time>'{$yesterday}' AND (template_id>0) AND (is_reply=0 OR (is_reply IS NULL)) 
            AND (parent_id=0 OR (parent_id IS NULL)) AND (created_by>0)";
        $emailsent = Yii::app()->db->createCommand($q)->queryRow();
        $totales = 0;
        if ($emailsent) $totales = $emailsent['total'];
        $content .= "24 hours of Emails Sent: $totales <br />";

        $q = "SELECT COUNT( * ) AS total  FROM `lkm_email_queue` 
            WHERE (template_id>0) AND (is_reply=0 OR (is_reply IS NULL)) AND (parent_id=0 OR (parent_id IS NULL))
            AND (created_by>0) AND (opened>'{$onedayago}')";
        $emailopen = Yii::app()->db->createCommand($q)->queryRow();
        $totaleo = 0;
        if ($emailopen) $totaleo = $emailopen['total'];
        $content .= "24 hours of Emails Open: $totaleo <br />";

        $q = "SELECT count(DISTINCT `email_from`) AS total  FROM `lkm_email_queue` 
            WHERE (is_reply = 1) AND (domain_id>0) AND (parent_id>0) AND (ccreply_ordering<=1) 
            AND (reply_created_by>0) AND (created_by IS NULL) AND (send_time>'{$yesterday}')";
        $emailreply = Yii::app()->db->createCommand($q)->queryRow();
        $totaler = 0;
        if ($emailreply) $totaler = $emailreply['total'];
        $content .= "24 hours of Emails Received: $totaler <br />";


        Utils::notice(array('content'=>$content, 'tos'=>$admins, 'cc'=>false,
                            'subject'=>'Review of Yesterdays Data'));

    }

}

?>