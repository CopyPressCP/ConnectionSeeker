<?php
/*
D:\WEBIDE\Language\php>php -q K:\NewHtdocs\yii\yii1.1.8.dev\sites\com.connectionseeker\cron.php SyncTrail2IO $iostatus $num $offset
the $args will returns as following
array(
    [0] => $iostatus    //such as 3, means approved
    [1] => $num         //such as 10, get 10 records
    [1] => $offset
)
*/
Yii::import('application.vendors.*');

class SyncTrail2IOCommand extends CConsoleCommand {
    public function run($args) {
        //ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set("memory_limit", "128M");

        $iostatus = 1;
        $num      = 10;
        $offset   = 0;
        if (!empty($args)) {
            $iostatus = $args[0];
            if (isset($args[1])) $num    = (int) $args[1];
            if (isset($args[2])) $offset = (int) $args[2];
        }
        //print_r($args);

        $_iostatuses = Task::$iostatuses;
        //if (in_array($iostatus, Task::$iostatuses)) {
        if (in_array($iostatus, $_iostatuses)) {
            echo "Wrong IO Status.";
            return false;
        }

        $_nvs = array();
        $_nvs[0] = 's:8:"iostatus";i:'.$iostatus.';';
        $_nvs[1] = 's:8:"iostatus";s:'.strlen($iostatus).':"'.$iostatus.'";';
        $condition = "(new_value LIKE '%".implode("%') OR (new_value LIKE '%", $_nvs)."%')";

        
        for ($i=0; $i<=300; $i++) {
            $offset = $i * 100;
            $ios = Yii::app()->db->createCommand()->select('model_id,created')
                ->from('{{operation_trail}}')
                ->where("($condition) AND model = 'Task'")
                ->order('id ASC')
                ->limit($num)
                ->offset($offset)
                ->queryAll();

            if (!empty($ios)) {
                if ($iostatus == 5) {
                    $iolabel = "completed";
                } else {
                    $iolabel = $_iostatuses[$iostatus];
                }

                foreach ($ios as $iv) {
                    $dcols = array();

                    $iolabel = strtolower($iolabel);
                    $datelabel = "date_".$iolabel;
                    $dcols[$datelabel] = $iv["created"];
                    $dcols["task_id"] = $iv["model_id"];
                    print_r($dcols);

                    if ($iostatus == 1) {//current
                        Yii::app()->db->createCommand()->update('{{io_historic_reporting}}', $dcols,
                            "(task_id=:task_id AND ($datelabel > :datelabel OR ($datelabel IS NULL)) )",
                              array(':task_id'   => $iv['model_id'],
                                    ':datelabel' => $iv["created"]));
                    } else {
                        Yii::app()->db->createCommand()->update('{{io_historic_reporting}}', $dcols,
                            "(task_id=:task_id)", array(':task_id'   => $iv['model_id']));
                        /*
                        Yii::app()->db->createCommand()->update('{{io_historic_reporting}}', $dcols,
                            "(task_id=:task_id AND $datelabel < :datelabel)", array(':task_id'   => $iv['model_id'],
                                                                               ':datelabel' => $iv["created"]));
                        */
                    }

                }
            }
        }//end for;
    }
}
?>