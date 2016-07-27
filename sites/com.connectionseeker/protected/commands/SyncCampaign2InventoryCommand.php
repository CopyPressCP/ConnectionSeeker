<?php
//http://www.yiiframework.com/wiki/91/implementing-cron-jobs-with-yii/
//http://www.yiiframework.com/forum/index.php?/topic/26551-setting-up-cronjob/
//How to call this script in crontab: php /path/to/cron.php syncTopBacklinks
//http://hudeyong926.iteye.com/blog/1283125 路径引用总结
/*
D:\WEBIDE\Language\php>php -q K:\NewHtdocs\yii\yii1.1.8.dev\sites\com.connectionseeker\cron.php SyncCampaign2Inventory $num $offset
the $args will returns as following
array(
    [0] => $field
    [1] => $num
)
*/
Yii::import('application.vendors.*');

class SyncCampaign2InventoryCommand extends CConsoleCommand {
    public function run($args) {
        //ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set("memory_limit", "128M");

        $num = 10;
        $offset = 0;
        if (!empty($args)) {
            $num = $args[0];
            if (isset($args[1])) $offset = (int) $args[1];
        }
        print_r($args);


        //for ($i=0; $i<1; $i++) {
        for ($i=0; $i<110; $i++) {
            $offset = $i * $num;
            $domains = Yii::app()->db->createCommand()->select('id, domain_id, domain')->from('{{inventory}}')
                ->where("ispublished=1")
                ->limit($num)
                ->offset($offset)
                ->queryAll();

            if (!empty($domains)) {
                foreach ($domains as $dv) {
                    $domain = $dv["domain"];
                    $domain_id = $dv["domain_id"];
                    $campaigns = Yii::app()->db->createCommand()->select('t.campaign_id, c.name')
                        ->from('{{inventory_building_task}} t')->join('{{campaign}} c', 'c.id=t.campaign_id')
                        ->where("t.desired_domain_id='$domain_id' AND t.iostatus=5")
                        ->group("t.campaign_id")
                        //->limit($num)
                        ->queryAll();

                    print_r($campaigns);

                    if ($campaigns) {
                        $cmpids = array();
                        $cmpnames = array();
                        foreach ($campaigns as $cmp) {
                            $cmpids[] = $cmp["campaign_id"];
                            $cmpnames[] = $cmp["name"];
                        }
                        $cmpidstr = implode("|", $cmpids);
                        echo $cmpidstr = "|" . $cmpidstr . "|";
                        echo $cmpnamestr = implode(", ", $cmpnames);
                        $ivtarr = array();
                        $ivtarr["campaign_id"] = $cmpidstr;
                        $ivtarr["campaign_str"] = $cmpnamestr;
                        Yii::app()->db->createCommand()->update('{{inventory}}', $ivtarr, 'id=:id', array(':id'=>$dv['id']));
                        unset($cmpids);
                        unset($cmpnames);
                    }
                }
            }
        }//end for;
    }

}
?>