<?php
/*
D:\WEBIDE\Language\php>php -q K:\NewHtdocs\yii\yii1.1.8.dev\sites\com.connectionseeker\cron.php SyncDeniedInventory $num $offset
the $args will returns as following
array(
    [0] => $iostatus    //such as 3, means approved
    [1] => $num         //such as 10, get 10 records
    [1] => $offset
)
*/
Yii::import('application.vendors.*');

class SyncDeniedInventoryCommand extends CConsoleCommand {
    public function run($args) {
        //ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set("memory_limit", "128M");

        $num      = 100;
        $offset   = 0;
        if (!empty($args)) {
            if (isset($args[0])) $num    = (int) $args[1];
            if (isset($args[1])) $offset = (int) $args[2];
        }

        $_nvs = array();
        $_nvs[0] = 's:8:"iostatus";i:1;';
        $_nvs[1] = 's:8:"iostatus";s:1:"1";';
        $_nvs[2] = 's:8:"iostatus";i:4;';
        $_nvs[3] = 's:8:"iostatus";s:1:"4";';
        $condition = "(new_value LIKE '%".implode("%') OR (new_value LIKE '%", $_nvs)."%')";

        
        for ($i=0; $i<=300; $i++) {
            $offset = $i * 100;
            $ios = Yii::app()->db->createCommand()->select('old_value,new_value,user_id,model_id,created')
                ->from('{{operation_trail}}')
                ->where("($condition) AND model = 'Task'")
                ->order('id ASC')
                ->limit($num)
                ->offset($offset)
                ->queryAll();

            if (!empty($ios)) {
                foreach ($ios as $iv) {
                    $ivtarr = array();
                    $oldvalue = $iv["old_value"];
                    if ($oldvalue) {
                        $oldvalue = unserialize($oldvalue);
                        if (!isset($oldvalue["desired_domain_id"]) || empty($oldvalue["desired_domain_id"])) {
                            continue;
                        }
                    } else {
                        continue;
                    }
                    $newvalue = $iv["new_value"];
                    if ($newvalue) $newvalue = unserialize($newvalue);
                    if (isset($newvalue["desired_domain_id"])) {
                        if ($oldvalue["desired_domain_id"]>0 && empty($newvalue["desired_domain_id"])) {
                            $old_domain_id = $oldvalue["desired_domain_id"];

                            $ivt = Inventory::model()->findByAttributes(array('domain_id'=>$old_domain_id));
                            $umdl = User::model()->findByPk($iv["user_id"]);
                            if ($ivt && $umdl) {
                                $ivtarr["isdenied"] = 1;
                                $ivtarr["denied_by"] = $ivt->denied_by;
                                $ivtarr["denied_by_str"] = $ivt->denied_by_str;
                                if ($ivt->denied_by) {
                                    if (strpos($ivt->denied_by, "|".$iv["user_id"]."|") === false) {
                                        $ivtarr["denied_by"] .= $iv["user_id"]."|";
                                        $ivtarr["denied_by_str"] .= "," . $umdl->username;
                                    }
                                } else {
                                    $ivtarr["denied_by"] = "|".$iv["user_id"]."|";
                                    $ivtarr["denied_by_str"] = $umdl->username;
                                }

                                Yii::app()->db->createCommand()->update('{{inventory}}', $ivtarr,
                                                                        'id=:id', array(':id'=>$ivt->id));
                            }
                            unset($ivt);
                            unset($umdl);
                        }
                    }
                }
            }
        }//end for;
    }
}
?>