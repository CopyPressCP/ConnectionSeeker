<?php
//http://www.yiiframework.com/wiki/91/implementing-cron-jobs-with-yii/
//http://www.yiiframework.com/forum/index.php?/topic/26551-setting-up-cronjob/
//How to call this script in crontab: php /path/to/cron.php syncTopBacklinks
//http://hudeyong926.iteye.com/blog/1283125 路径引用总结
/*
D:\WEBIDE\Language\php>php -q K:\NewHtdocs\yii\yii1.1.8.dev\sites\com.connectionseeker\cron.php AddDomain2Crawler $limit
the $args will returns as following
array(
    [0] => p1
    [1] => p2
)

php -q K:\NewHtdocs\yii\yii1.1.8.dev\sites\com.connectionseeker\cron.php AddDomain2Crawler 10 0
This feature will Sync the replied emails from different EMail ISP account.
*/

Yii::import('application.vendors.*');
define('DS', DIRECTORY_SEPARATOR);
error_reporting(E_ALL);

class AddDomain2CrawlerCommand extends CConsoleCommand {

    public function run($args) {
        //ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set("memory_limit", "512M");

        $num = 10;
        if (!empty($args)) {
            $num = (int) $args[0];
        }

        /*
        $offset = 0;
        if (!empty($args)) {
            $offset = (int) $args[1];
        }
        */

        $offsetfile = dirname(dirname(__FILE__)) . DS . "runtime" . DS . "currentid4adding.txt";

        $currentid = @file_get_contents($offsetfile);
        if (!$currentid) $currentid = 0;

        $domains = Yii::app()->db->createCommand()->select('id, domain')->from('{{domain}}')
            ->where("id > $currentid")
            ->order('id ASC')
            ->limit($num)
            ->queryAll();
        if ($domains) {
            $current = 0;
            $adds = array();
            //$i = 0;
            foreach ($domains as $dv) {
                $current = $dv["id"];
                $adds["domain"][] = array($dv["domain"], 1);
            }

            $dm = json_encode($adds);
            $postdata = http_build_query(
                array(
                    'param' => $dm,
                )
            );

            //$dm = urlencode($dm);
            //$url = "http://199.91.65.138:8080/Crawler/info/addDomain.do?param=".$dm;
            $url = "http://199.91.65.138:8080/Crawler/info/addDomain.do";
            $ctx = stream_context_create(array(
               'http' => array(
                    'method'  => "POST",
                    'header'  => 'Content-type: application/x-www-form-urlencoded',
                    'content' => $postdata,
                    'timeout' => 600
                    )
                )
            );
            echo $url;
            $response = file_get_contents($url, false, $ctx);
            //$response = file_get_contents($url);
            echo $response;
            if ($response === false) {
                echo "ERROR";
            } else {
                file_put_contents($offsetfile, $current);
            }
        }
    }

}

?>