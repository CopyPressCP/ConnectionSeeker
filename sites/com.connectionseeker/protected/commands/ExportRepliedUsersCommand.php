<?php
//http://www.yiiframework.com/wiki/91/implementing-cron-jobs-with-yii/
//http://www.yiiframework.com/forum/index.php?/topic/26551-setting-up-cronjob/
//How to call this script in crontab: php /path/to/cron.php syncTopBacklinks
//http://hudeyong926.iteye.com/blog/1283125 路径引用总结
/*
D:\WEBIDE\Language\php>php -q K:\NewHtdocs\yii\yii1.1.8.dev\sites\com.connectionseeker\cron.php ExportRepliedUsers 30 0
the $args will returns as following
array(
    [0] => 30
    [1] => 0
)
*/

Yii::import('application.vendors.*');
define('DS', DIRECTORY_SEPARATOR);
error_reporting(E_ALL);

class ExportRepliedUsersCommand extends CConsoleCommand {

    public function run($args) {
        //ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set("memory_limit", "512M");

        $num = 10;
        if (!empty($args)) {
            $num = (int) $args[0];
        }

        $offset = 0;
        if (!empty($args)) {
            $offset = (int) $args[1];
        }

        $offsetfile = dirname(dirname(__FILE__)) . DS . "runtime" . DS . "currentqid4export.txt";
        $currentid = @file_get_contents($offsetfile);
        if (!$currentid) $currentid = 0;

        $exportfile = dirname(dirname(__FILE__)) . DS . "runtime" . DS . "exportemailpersona.csv";

        $accounts = Yii::app()->db->createCommand()->select('id,username')->from('{{mailer_account}}')
            ->where('(pop3_host IS NOT NULL) AND (pop3_port IS NOT NULL) AND (pop3_host != "")')
            ->queryAll();

        if (!empty($accounts)) {
            $mes = array();
            $mesrev = array();
            foreach ($accounts as $av) {
                print_r($av); 
                $mes[$av['id']] = $av['username'];
                $mesrev[$av['username']] = $av['id'];
            }
        }

        $fp = fopen($exportfile, 'a+');
        $model = new Domain;
        $num = 30;
        for($i=0;$i<2000;$i++) {
            $offset = $i * $num;
            $emails = Yii::app()->db->createCommand()
                ->select('id,domain_id,parent_id,from,to,email_from,parent_id,is_reply,reply_created_by,template_id,send_time')
                ->from('{{email_queue}}')
                ->where("id > $currentid")
                ->order('id ASC')
                ->limit($num)
                //##->offset($offset) We no need use offset, cause we already have id > currentid;
                ->queryAll();
            if ($emails) {
                foreach($emails as $e){
                    $currentid = $e["id"];
                    if ($e["domain_id"]>0 && $e["template_id"]>0 && $e["from"]>0 && $e["is_reply"]>0) {
                        $els = array();
                        $dmodel = $model->findByPk($e["domain_id"]);
                        $repliedfor = $mes[$e["from"]];
                        if ($dmodel && $repliedfor != $e["email_from"]) {
                            $els["sentfrom"] = $e["email_from"];
                            $els["repliedfor"] = $repliedfor;
                            $els["sentdate"] = $e["send_time"];
                            $els["domain"] = $dmodel->domain;
                            $els["owner"] = $dmodel->owner;
                            fputcsv($fp, $els);
                        }
                    }
                }
            }
        }
        fclose($fp);

        file_put_contents($offsetfile, $currentid);
    }

}

?>