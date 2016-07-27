<?php
//http://www.yiiframework.com/wiki/91/implementing-cron-jobs-with-yii/
//http://www.yiiframework.com/forum/index.php?/topic/26551-setting-up-cronjob/
//How to call this script in crontab: php /path/to/cron.php syncTopBacklinks
//http://hudeyong926.iteye.com/blog/1283125 路径引用总结
/*
D:\WEBIDE\Language\php>php -q K:\NewHtdocs\yii\yii1.1.8.dev\sites\com.connectionseeker\cron.php SyncCopypressArticles p1 p2
the $args will returns as following
array(
    [0] => p1
    [1] => p2
)
*/
Yii::import('application.vendors.*');

class SyncCopypressArticlesCommand extends CConsoleCommand {
    public function run($args) {
        //ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set("memory_limit", "128M");

        $ts = array_flip(Task::$status);
        //print_r($ts);

        $num = 20;
        if (!empty($args)) {
            $num = (int) $args[0];
        }
        $checkouted = time();

        $aids = Yii::app()->db->createCommand()->select('content_article_id')->from('{{inventory_building_task}}')
            //->where("((content_article_id > 0) AND taskstatus IN ('0','1','2','3','4'))", array(':checkouted'=>$checkouted))
            //->where(array("and", "content_article_id > 0", "taskstatus IN (0,1,2,3,4)"))
            ->where('((content_article_id > 0) AND taskstatus IN (0,1,2,3,4))')
            ->order('checkouted ASC')
            ->limit($num)
            ->queryAll();
        print_r($aids);

        if (!empty($aids)) {
            /*
            foreach ($aids as $dv) {
                Yii::app()->db->createCommand()->update('{{inventory_building_task}}', $dcols, 'id=:id', array(':id'=>$dv['id']));
            }
            */

            foreach ($aids as $dv) {
                $ids[] = $dv['content_article_id'];
            }

            //call api and update the status.
            $sss_ids = array('ids' => $ids);
            $remotearticles = array();
            $response = Utils::sendCmd2SSSAPI("getarticlestatus", $sss_ids);

            if ($response->isSuccessful()) {
                $fnids = array();//finish articles id
                $responsebody = $response->getBody();
                //echo $responsebody;
                $rbodys = simplexml_load_string(utf8_encode($responsebody));
                foreach ($rbodys->articlestatus as $_r) {
                    print_r($_r);
                    $articleid = $_r->articleid;
                    $articleid = (int)$articleid;
                    $status = (string)$_r->status;

                    $iarr = array();
                    $iarr['taskstatus'] = $ts[$status];
                    $iarr['checkouted'] = $checkouted;
                    Yii::app()->db->createCommand()->update('{{inventory_building_task}}', $iarr, 
                                                'content_article_id=:article_id', array(':article_id'=>$articleid));

                    if (strtolower($status) == 'completed') {
                        $fnids[] = $articleid;
                    }
                }

                if ($fnids) {
                    //call api and update the status.
                    $fn_ids = array('ids' => $fnids);
                    //$remotearticles = array();
                    unset($response);
                    $response = Utils::sendCmd2SSSAPI("downloadarticles", $fn_ids);
                    if ($response->isSuccessful()) {
                        $responsebody = $response->getBody();
                        $rbodys = simplexml_load_string(utf8_encode($responsebody));

                        $aprids = array();
                        foreach ($rbodys->articlestatus as $_r) {
                            //print_r($_r);
                            $articleid = $_r->articleid;
                            $articleid = (int)$articleid;
                            $status    = (string)$_r->status;

                            //we no need this one acctually, cause we filter this above already.
                            if (strtolower($status) == 'completed') {
                                $fnids[] = $articleid;
                                $lts = array();
                                $lts['id']     = $articleid;
                                $lts['title']  = (string)$_r->title;
                                $lts['length'] = (int)$_r->length;
                                $lts['text']   = (string)$_r->textBody;
                                $lts['html']   = (string)$_r->htmlBody;
                                Yii::app()->db->createCommand()->insert('{{copypress_content}}', $lts);
                                //Yii::app()->db->getLastInsertID();
                            }
                        }
                    }

                }//end of $fnids;

            }
        }

        //print_r($domains);
    }

}
?>