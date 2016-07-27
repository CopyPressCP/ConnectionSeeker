<?php
//http://www.yiiframework.com/wiki/91/implementing-cron-jobs-with-yii/
//http://www.yiiframework.com/forum/index.php?/topic/26551-setting-up-cronjob/
//How to call this script in crontab: php /path/to/cron.php syncTopBacklinks
//http://hudeyong926.iteye.com/blog/1283125 路径引用总结
/*
D:\WEBIDE\Language\php>php -q K:\NewHtdocs\yii\yii1.1.8.dev\sites\com.connectionseeker\cron.php SyncOnPageMetrics $limit
the $args will returns as following
array(
    [0] => p1
    [1] => p2
)

php -q K:\NewHtdocs\yii\yii1.1.8.dev\sites\com.connectionseeker\cron.php SyncOnPageMetrics 10 0
This feature will Sync the replied emails from different EMail ISP account.
*/

Yii::import('application.vendors.*');
define('DS', DIRECTORY_SEPARATOR);
error_reporting(E_ALL);

class SyncDiscoveryOnPageMetricsCommand extends CConsoleCommand {

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

        /*
        $offsetfile = dirname(dirname(__FILE__)) . DS . "runtime" . DS . "currentid4getting.txt";

        $currentid = @file_get_contents($offsetfile);
        if (!$currentid) $currentid = 0;
        */

        $domains = Yii::app()->db->createCommand()->select('dbd.domain_id, dbd.domain')->from('{{discovery_backdomain}} dbd')
            ->join('{{domain_onpage}} dp', 'dbd.domain_id=dp.domain_id')
            ->where("((dp.lastcrawled IS NULL) OR dp.lastcrawled = '') AND dbd.status != '-2'")
            //####->order('dp.domain_id DESC')
            ->limit($num)
            ->queryAll();

        print_r($domains);
        if ($domains) {
            $current = 0;
            $adds = array();
            foreach ($domains as $dv) {
                $current = $dv["domain_id"];
                $adds["domain"][] = array($dv["domain"]);
            }
            $dm = json_encode($adds);
            $postdata = http_build_query(
                array(
                    'param' => $dm,
                )
            );

            //$dm = urlencode($dm);

            $url = "http://199.91.65.138:8080/Crawler/info/getMultiDomain.do";
            $ctx = stream_context_create(array(
               'http' => array(
                    'method'  => 'POST',
                    'header'  => 'Content-type: application/x-www-form-urlencoded',
                    'content' => $postdata,
                    'timeout' => 600
                    )
                )
            );

            $response = file_get_contents($url, false, $ctx);
            echo $response;
            if ($response) {
                $rs = json_decode($response, true);
                if (isset($rs["Data"]) && $rs["Data"]) {
                    $opmodel = new DomainOnpage;
                    foreach ($rs["Data"] as $v) {
                        $domain = $v["Domain"];

                        $opmdl = $opmodel->findByAttributes(array('domain'=>$domain));
                        if ($opmdl && empty($v["Info"])) {
                            $q = "UPDATE {{discovery_backdomain}} SET status='-2' WHERE domain_id = '".$opmdl->domain_id."'";
                            Yii::app()->db->createCommand($q)->execute();
                        }
                        if ($opmdl && !empty($v["Info"])) {
                            $opmdl->setIsNewRecord(false);
                            $opmdl->setScenario('update');
                            $opmdl->attributes = $v["Info"][0];
                            $opmdl->lastcrawled = strtotime($opmdl->lastcrawled);
                            $opmdl->lastcrawled = date("Y-m-d H:i:s", $opmdl->lastcrawled);
                            $opmdl->save();

                            $q = "UPDATE {{discovery_backdomain}} SET lastcrawled='".$opmdl->lastcrawled.
                                 "' WHERE domain_id = '".$opmdl->domain_id."'";
                            Yii::app()->db->createCommand($q)->execute();

                            unset($opmdl);
                        }
                    }
                }
                //print_r($rs);
            }

            //###file_put_contents($offsetfile, $current);
        }
    }

}

?>