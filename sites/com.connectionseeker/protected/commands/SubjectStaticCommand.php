<?php
/*
D:\WEBIDE\Language\php>php -q K:\NewHtdocs\yii\yii1.1.8.dev\sites\com.connectionseeker\cron.php SubjectStaticCommand $num $offset
the $args will returns as following
array(
    [0] => $iostatus    //such as 3, means approved
    [1] => $num         //such as 10, get 10 records
    [1] => $offset
)
*/
Yii::import('application.vendors.*');

class SubjectStaticCommand extends CConsoleCommand {
    public function run($args) {
        //ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set("memory_limit", "512M");

        /*
        $num      = 50;
        $offset   = 0;
        if (!empty($args)) {
            if (isset($args[0])) $num    = (int) $args[0];
            if (isset($args[1])) $offset = (int) $args[1];
        }
        //print_r($args);
        */


        //1subject,2how many sent,3how many open,4how many response
        $rsstr = "%s,%s,%s,%s\r";
        $filename = dirname(dirname(__FILE__)) . "/runtime/topsubjectstatistic_".time().".txt";
        

        $num = 101;
        $offset = 0;
        //Get Top 100 Subject.
        $emails = Yii::app()->db->createCommand()->select('count(subject) AS nofsent, subject')
            ->from('{{email_queue}}')
            ->where("(is_reply = 0) AND (status=1) AND (domain_id>0) AND (created_by>0) AND (send_time>0)")
            ->group('subject')
            ->order('nofsent DESC')
            ->limit($num)
            ->offset($offset)
            ->queryAll();

        if (!$emails) exit;

        foreach ($emails as $em) {
            if (empty($em["subject"])) continue;
            $subject = $em["subject"];
            $nofsent = $em["nofsent"];
            $nofopen = 0;
            $nofreply = 0;
            for ($i=0; $i<$nofsent; $i++) {
                //$num = 1;
                //$offset = $i;
                $curremail = Yii::app()->db->createCommand()->select('id,opened,email_from')
                ->from('{{email_queue}}')
                ->where("(is_reply = 0) AND (status=1) AND (domain_id>0) AND (created_by>0) AND (send_time>0) AND (`subject`=:subject)", array(':subject'=>$subject))
                ->order('id ASC')->limit(1)->offset($i)->queryAll();
                //print_r($curremail);
                //if ($i == 10) exit;
                if ($curremail) {
                    $pid = $curremail[0]["id"];
                    $efrom = $curremail[0]["email_from"];
                    $existreply = Yii::app()->db->createCommand()->select('id')->from('{{email_queue}}')
                        ->where("parent_id=$pid AND (created_by IS NULL) AND (send_time>0) AND `to`='$efrom'")->queryRow();
                    if ($curremail[0]["opened"] || $existreply) {
                        $nofopen++;
                        if ($existreply) $nofreply++;
                    }
                    unset($existreply);
                }
                unset($curremail);
            }//end for;


            $_rsstr = sprintf($rsstr, $subject, $nofsent, $nofopen, $nofreply);
            file_put_contents($filename, $_rsstr, FILE_APPEND | LOCK_EX);
        }//end of foreach
        unset($emails);
    }
}
?>