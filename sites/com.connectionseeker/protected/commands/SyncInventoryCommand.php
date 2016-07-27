<?php
//http://www.yiiframework.com/wiki/91/implementing-cron-jobs-with-yii/
//http://www.yiiframework.com/forum/index.php?/topic/26551-setting-up-cronjob/
//How to call this script in crontab: php /path/to/cron.php syncTopBacklinks
//http://hudeyong926.iteye.com/blog/1283125 路径引用总结
/*
D:\WEBIDE\Language\php>php -q K:\NewHtdocs\yii\yii1.1.8.dev\sites\com.connectionseeker\cron.php SyncInventory $num $offset
the $args will returns as following
array(
    [0] => $field
    [1] => $num
)
*/
Yii::import('application.vendors.*');

class SyncInventoryCommand extends CConsoleCommand {
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

        $domains = Yii::app()->db->createCommand()->select('desired_domain_id,desired_domain,created,created_by,modified,modified_by,livedate,channel_id,iostatus,iodate')
            ->from('{{inventory_building_task}}')
            //->where("(`iostatus` = 5)")
            ->where("(`iostatus` IN (5, 21, 3))")
            ->order('id ASC')
            ->limit($num)
            ->offset($offset)
            ->queryAll();

        if (!empty($domains)) {
            foreach ($domains as $dv) {
                echo $__domain = $dv["desired_domain"];
                $__url = "http://".$__domain;
                if (!filter_var($__url, FILTER_VALIDATE_URL) || empty($dv["desired_domain_id"])) {
                    unset($dv);
                    unset($__domain);
                    continue;
                }
                if (!empty($__domain)) {
                    $ivtmodel = new Inventory;
                    $ivt = $ivtmodel->findByAttributes(array('domain' => $__domain));

                    $uid = $dv["modified_by"] ? $dv["modified_by"] : $dv["created_by"];
                    $_channel_id = $dv["channel_id"];


                    if(empty($_channel_id)){
                        $umodel = User::model()->findByPk($uid);
                        if ($umodel->channel_id) $_channel_id = $umodel->channel_id;
                    }
                    if ($ivt) {
                        //##$model->desired_domain_id = $ivt->domain_id;
                        //##$model->desired_domain = $__domain;
                        if ($_channel_id) {
                            $ivtarr = array();
                            if ($ivt->channel_id) {
                                $chlstr = substr($ivt->channel_id, 1, -1);
                                $_chls = explode("|", $chlstr);
                                if (!in_array($_channel_id, $_chls)) {
                                    array_push($_chls, $_channel_id);
                                }
                            } else {
                                $_chls = array($_channel_id);
                            }

                            if (empty($ivt->acquired_channel_id)) $ivtarr["acquired_channel_id"] = $_channel_id;

                            if ($dv["iostatus"] == 5) $ivtarr["ispublished"] = 1;
                            //$ivtarr["channel_id"] = $_chls;

                            $channels = Types::model()->actived()->bytype('channel')
                                                       ->findAllByAttributes(array('refid' => array_values($_chls)));
                            $data = array();
                            if ($channels) {
                                $data = CHtml::listData($channels, 'refid', 'typename');
                                if (!empty($data)) $ivtarr["channel_str"] = implode(", ", array_values($data));
                            }
                            $ivtarr["channel_id"] = "|".implode("|", array_values($_chls))."|";
                            if (empty($dv["created"])) {
                                $ivtarr["created"] = $dv["iodate"];
                                $ivtarr["created_by"] = $dv["modified_by"];
                            }
                            if (empty($dv["modified"])) {
                                $ivtarr["modified"] = $dv["iodate"];
                                $ivtarr["modified_by"] = $dv["modified_by"];
                            }

                            if (empty($ivt->acquireddate)) $ivtarr["acquireddate"] = $dv["iodate"];
                            if (empty($ivtarr["acquireddate"])) $ivtarr["acquireddate"] = $dv["modified"];

                            print_r($ivtarr);
                            Yii::app()->db->createCommand()->update('{{inventory}}', $ivtarr, 'id=:id', array(':id'=>$ivt->id));
                        }
                    } else {
                        $ivtarr["domain"]=$__domain;
                        $ivtarr["domain_id"]=$dv["desired_domain_id"];
                        if ($_channel_id) {
                            $channels = Types::model()->actived()->bytype('channel')
                                                       ->findAllByAttributes(array('refid' => array($_channel_id)));
                            $data = array();
                            if ($channels) {
                                $data = CHtml::listData($channels, 'refid', 'typename');
                                if (!empty($data)) $ivtarr["channel_str"] = implode(", ", array_values($data));
                            }
                            $ivtarr["channel_id"] = "|".$_channel_id."|";
                            $ivtarr["acquired_channel_id"] = $_channel_id;
                        }

                        $ivtarr["created_by"] = $dv["modified_by"];
                        $ivtarr["created"] = $dv["iodate"];
                        //##$ivtarr["ispublished"] = 1;
                        if ($dv["iostatus"] == 5) $ivtarr["ispublished"] = 1;
                        $ivtarr["acquireddate"] = $dv["iodate"];
                        if (empty($ivtarr["acquireddate"])) $ivtarr["acquireddate"] = $dv["modified"];
                        print_r($ivtarr);

                        Yii::app()->db->createCommand()->insert('{{inventory}}', $ivtarr);
                    }

                    unset($ivtmodel);
                    unset($ivt);
                }
            }
        }
    }

}
?>