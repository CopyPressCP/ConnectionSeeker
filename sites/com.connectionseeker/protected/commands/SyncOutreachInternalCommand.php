<?php
//http://www.yiiframework.com/wiki/91/implementing-cron-jobs-with-yii/
//http://www.yiiframework.com/forum/index.php?/topic/26551-setting-up-cronjob/
//How to call this script in crontab: php /path/to/cron.php syncTopBacklinks
//http://hudeyong926.iteye.com/blog/1283125 路径引用总结
/*
D:\WEBIDE\Language\php>php -q K:\NewHtdocs\yii\yii1.1.8.dev\sites\com.connectionseeker\cron.php SyncOutreachInternal 30 0
the $args will returns as following
array(
    [0] => 30
    [1] => 0
)
*/

Yii::import('application.vendors.*');
define('DS', DIRECTORY_SEPARATOR);
error_reporting(E_ALL);

class SyncOutreachInternalCommand extends CConsoleCommand {

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

        $offsetfile = dirname(dirname(__FILE__)) . DS . "runtime" . DS . "currentqid4sync.txt";
        $currentid = @file_get_contents($offsetfile);
        if (!$currentid) $currentid = 0;

        $accounts = Yii::app()->db->createCommand()->select('id,username')->from('{{mailer_account}}')
            ->where('(pop3_host IS NOT NULL) AND (pop3_port IS NOT NULL) AND (pop3_host != "")')
            ->queryAll();

        if (!empty($accounts)) {
            $mes = array();
            $mesrev = array();
            foreach ($accounts as $av) {
                //print_r($av); 
                $mes[$av['id']] = $av['username'];
                $mesrev[$av['username']] = $av['id'];
            }
        }
        print_r($mes);

        $model = new Domain;
        $oemodel = new OutreachEmail;
        $num = 30;
        //for($i=0;$i<200;$i++) {
        for($i=0;$i<5;$i++) {
            $emails = Yii::app()->db->createCommand()
                ->select('id,domain_id,from,status,to,email_from,opened,is_reply,created_by,template_id,send_time')
                ->from('{{email_queue}}')
                ->where("id > $currentid")
                ->order('id ASC')
                ->limit($num)
                //##->offset($offset) We no need use offset, cause we already have id > currentid;
                ->queryAll();
            if ($emails) {
                foreach($emails as $e){
                    //print_r($e);
                    $currentid = $e["id"];
                    $opentime = 0;
                    if (!empty($e["opened"]) && $e["opened"]>0) {
                        $opentime = date("Y-m-d H:i:s", $e["opened"]);
                    }

                    if ($e["domain_id"]>0 && $e["template_id"]>0 && $e["from"]>0 && $e["status"]==1) {
                        if (isset($mes[$e["from"]])) {
                            $repliedfor = $mes[$e["from"]];
                        } else {
                            unset($e);
                            continue;
                        }

                        $els = array();
                        $dmodel = $model->findByPk($e["domain_id"]);
                        $e["email_from"] = strtolower($e["email_from"]);
                        if ($dmodel && $repliedfor) {
                            $repliedfor = strtolower($repliedfor);
                            //###$oemdl = $oemodel->findByAttributes(array('queue_id'=>$e["id"]));
                            if (isset($oemdl)) unset($oemdl);
                            $oemdl = $oemodel->findByAttributes(array('template_id'=>$e["template_id"],
                                               'domain_id'=>$e["domain_id"],'mailer_id'=>$e["from"]));
                            if ($oemdl) {
                                if ($oemdl->queue_id == $e["id"]) {
                                    unset($e);
                                    unset($oemdl);
                                    continue;
                                }

                                $oemdl->setIsNewRecord(false);
                                $oemdl->setScenario('update');
                                if ($repliedfor == $e["email_from"]) {
                                    $oemdl->nofextsent += 1;
                                    if (empty($oemdl->extsent)) {
                                        $oemdl->extsent = $e["id"];
                                    } else {
                                        $oemdl->extsent = $oemdl->extsent.",".$e["id"];
                                    }
                                } else {
                                    if (empty($oemdl->open_time)) {
                                        $oemdl->open_time = $e["send_time"];
                                    }
                                    $oemdl->latest_reply_time = $e["send_time"];
                                    if (empty($oemdl->first_reply_time)) $oemdl->first_reply_time = $e["send_time"];
                                    if (empty($oemdl->extreplied)) {
                                        $oemdl->extreplied = $e["id"];
                                    } else {
                                        $oemdl->extreplied = $oemdl->extreplied.",".$e["id"];
                                    }
                                    $oemdl->nofextreplied += 1;
                                }
                            } else if (!$oemdl && $repliedfor == $e["email_from"] 
                                      && $e["is_reply"] == 0 && !empty($e["created_by"])) {
                                $oemdl = $oemodel;
                                //insert it into ...
                                $oemdl->setIsNewRecord(true);
                                $oemdl->id = NULL;
                                $oemdl->domain    = $dmodel->domain;
                                $oemdl->domain_id = $e["domain_id"];
                                $oemdl->queue_id  = $e["id"];
                                $oemdl->template_id = $e["template_id"];
                                $oemdl->mailer_id  = $e["from"];
                                $oemdl->send_time  = $e["send_time"];
                                if ($opentime != 0) {
                                    //echo $opentime;
                                    $oemdl->open_time = $opentime;
                                    $opentime = 0;
                                }

                                $oemdl->efrom      = $e["email_from"];
                                $oemdl->eto        = $e["to"];
                                $oemdl->created_by = $e["created_by"];
                                //$oemdl->save();
                            }

                            if ($oemdl) {
                                $oemdl->save();
                                $oemodel->unsetAttributes();// this line is really important
                                unset($oemdl);
                            }
                            unset($e);
                        }
                    }
                    unset($e);
                }//end foreach

                if ($i % 100 == 0) file_put_contents($offsetfile, $currentid);
            } else {
                break;
            }
        }//end for()

        file_put_contents($offsetfile, $currentid);

    }//end of function

}//end of class

?>