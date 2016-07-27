<?php
//http://www.yiiframework.com/wiki/91/implementing-cron-jobs-with-yii/
//http://www.yiiframework.com/forum/index.php?/topic/26551-setting-up-cronjob/
//How to call this script in crontab: php /path/to/cron.php syncTopBacklinks
//http://hudeyong926.iteye.com/blog/1283125 路径引用总结
/*
D:\WEBIDE\Language\php>php -q K:\NewHtdocs\yii\yii1.1.8.dev\sites\com.connectionseeker\cron.php CrawlDesiredURL $step
the $args will returns as following
array(
    [0] => $field
    [1] => $num
)
*/
Yii::import('application.vendors.*');

class CrawlDesiredURLCommand extends CConsoleCommand {
    public function run($args) {
        //ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set("memory_limit", "256M");

        $step = 30;//30 days
        if (!empty($args)) {
            $step = $args[0];
            if (isset($args[1])) $num = (int) $args[1];
        }


        $now = time();
        $yesterday = date("Y-m-d", $now - 86400);
        $coupledayago = $now - 86400 * 90;
        $threemonth = date("Y-m-d", $coupledayago);
        $onemonth = date("Y-m-d", $now - 86400 * 30);

        $ctx = stream_context_create(array(
           'http' => array(
               'timeout' => 3600
               )
           )
        );

        /*
        $q = "SELECT id, anchortext, channel_id, iostatus, sourceurl, targeturl 
            FROM lkm_inventory_building_task
            WHERE iostatus = 5 AND livedate > '$threemonth' 
            AND MOD(  FLOOR( ( unix_timestamp() - unix_timestamp(livedate)) /86400), $step)=0";
            */
        //    WHERE iostatus = 5 AND livedate >= '$threemonth'

        $q = "SELECT id, anchortext, channel_id, iostatus, sourceurl, targeturl, livedate 
            FROM lkm_inventory_building_task
            WHERE iostatus = 5 AND livedate > 0 AND (desired_check_date < '$onemonth' OR desired_check_date IS NULL)
            AND MOD(  DATEDIFF('$yesterday', livedate), $step)=0 LIMIT 0, 20";
        //echo $q;
        $tasks = Yii::app()->db->createCommand($q)->queryAll();

        print_r($tasks);
        if (!empty($tasks)) {
            foreach ($tasks as $dv) {
                $crawlerarr = array();
                if (strlen($dv["sourceurl"]) == 0 || strlen($dv["sourceurl"]) == 0) {
                    $crawlerarr["desired_check_date"] = date("Y-m-d H:i:s", $now);
                } else {
                    if (!filter_var($dv["sourceurl"], FILTER_VALIDATE_URL)) {
                        $crawlerarr["desired_check_date"] = date("Y-m-d H:i:s", $now);
                        Yii::app()->db->createCommand()->update('{{inventory_building_task}}',
                            $crawlerarr, 'id=:id', array(':id'=>$dv['id']));
                        continue;
                    }
                    $contents = "";
                    $contents = @file_get_contents($dv["sourceurl"], 0, $ctx);
                    if ($contents) {
                        $anchor = trim($dv["anchortext"]);
                        $targeturl = trim($dv["targeturl"]);
                        //$crawlerarr["desired_check"] = 0;
                        if(stripos($contents, $anchor)!== false) { 
                            //anchortext is found.  Now check for TargetURL
                            if(stripos($contents, $targeturl)!== false) { 
                                //ACTION: Do Nothing. - Both Anchor Text and TargetURL Are Found.
                                $crawlerarr["desired_check"] = 0;
                            } else { 
                                //ACTION: Email channel with a body of "When scanning $publishedurl for $anchortext and $targeturl.  The anchor text was found however the targeturl link was not."
                                $crawlerarr["desired_check"] = 1;
                            }
                        } else { 
                            //Anchor Text Not Found.  Now check for TargetURL";
                            if(stripos($contents, $targeturl)!== false) { 
                                //ACTION: Email channel with a body of "When scanning $publishedurl for $anchortext and $targeturl.  The anchor text was not found however the targeturl link was."
                                $crawlerarr["desired_check"] = 2;
                            } else { 
                                $crawlerarr["desired_check"] = 3;
                                //ACTION: Email channel with a body of "When scanning $publishedurl for $anchortext and $targeturl.  Both the targeturl and anchortext are missing."
                            } 
                        }
                    } else {
                        $crawlerarr["desired_check"] = -1;
                    }
                    $crawlerarr["desired_check_date"] = date("Y-m-d H:i:s", $now);
                }

                print_r($dv);
                Yii::app()->db->createCommand()->update('{{inventory_building_task}}',
                    $crawlerarr, 'id=:id', array(':id'=>$dv['id']));
            }
        }
    }

}
?>