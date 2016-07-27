<?php
/*
D:\WEBIDE\Language\php>php -q K:\NewHtdocs\yii\yii1.1.8.dev\sites\com.connectionseeker\cron.php SyncTrail2ContentIO $content_step $num $offset
the $args will returns as following
array(
    [0] => $iostatus    //such as 3, means approved
    [1] => $num         //such as 10, get 10 records
    [1] => $offset
)
*/
Yii::import('application.vendors.*');

class SyncTrail2ContentIOCommand extends CConsoleCommand {
    public function run($args) {
        //ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set("memory_limit", "128M");

        $content_step = 0;
        $num      = 30;
        $offset   = 0;
        if (!empty($args)) {
            $content_step = $args[0];
            if (isset($args[1])) $num    = (int) $args[1];
            if (isset($args[2])) $offset = (int) $args[2];
        }
        //print_r($args);


        $_nvs = array();
        $_nvs[0] = 's:12:"content_step";i:'.$content_step.';';
        $_nvs[1] = 's:12:"content_step";s:'.strlen($content_step).':"'.$content_step.'";';
        $condition = "(new_value LIKE '%".implode("%') OR (new_value LIKE '%", $_nvs)."%')";

        
        for ($i=0; $i<=200; $i++) {
            $offset = $i * 30;
            $ios = Yii::app()->db->createCommand()->select('old_value,model_id,created')
                ->from('{{operation_trail}}')
                ->where("($condition) AND model = 'Task' AND id>690000")
                ->order('id ASC')
                ->limit($num)
                ->offset($offset)
                ->queryAll();

            if (!empty($ios)) {
                $datelabel = "date_step".$content_step;
                $timelabel = "time2step".$content_step;

                foreach ($ios as $iv) {
                    $dcols = array();
                    $dcols[$datelabel] = $iv["created"];
                    $dcols["task_id"] = $iv["model_id"];

                    echo $q = "SELECT * FROM {{contentio_historic_reporting}} WHERE task_id = '".$iv["model_id"]."'";
                    $chr = Yii::app()->db->createCommand($q)->queryRow();
                    if ($chr) {
                        //do nothing for now;
                    } else {
                        continue;
                    }

                    $prevvalue = $content_step>0 ? ($content_step-1) : 0;
                    $prevdatelable = "date_step".$prevvalue;
                    //$prevtimelable = "time2step".$prevvalue;
                    if ($iv["old_value"]) {
                        preg_match_all('/"content_step";s:1:\D?(\d+)\D?;/i', $iv["old_value"], $matches);
                        if ($matches && $matches[1]) {
                            $prevdatelable = "date_step".$matches[1][0];
                            //$prevtimelable = "time2step".$matches[1][0];
                        }
                    }
                    if ($chr[$prevdatelable]) {
                        echo $timediff = strtotime($iv["created"]) - strtotime($chr[$prevdatelable]);
                        if ($timediff > 0) $dcols[$timelabel] = $timediff;
                    }

                    print_r($dcols);

                    if ($content_step == 0) {//step0, sometimes 0 means denied;
                        Yii::app()->db->createCommand()->update('{{contentio_historic_reporting}}', $dcols,
                            "(task_id=:task_id AND ($datelabel > :datelabel OR ($datelabel IS NULL)) )",
                              array(':task_id'   => $iv['model_id'],
                                    ':datelabel' => $iv["created"]));
                    } else {
                        Yii::app()->db->createCommand()->update('{{contentio_historic_reporting}}', $dcols,
                            "(task_id=:task_id)", array(':task_id'   => $iv['model_id']));
                    }

                }
            }
        }//end for;
    }
}
?>