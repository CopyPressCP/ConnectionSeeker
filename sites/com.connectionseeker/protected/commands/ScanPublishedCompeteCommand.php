<?php
//http://www.yiiframework.com/wiki/91/implementing-cron-jobs-with-yii/
//http://www.yiiframework.com/forum/index.php?/topic/26551-setting-up-cronjob/
//How to call this script in crontab: php /path/to/cron.php syncTopBacklinks
//http://hudeyong926.iteye.com/blog/1283125 路径引用总结
/*
D:\WEBIDE\Language\php>php -q K:\NewHtdocs\yii\yii1.1.8.dev\sites\com.connectionseeker\cron.php ScanPublishedCompete p1 p2
the $args will returns as following
array(
    [0] => p1
    [1] => p2
)

http://apps.compete.com/sites/biggerpockets.com/trended/uv/?apikey=c85db60ab72872951b94561a708b0127&start_date=201307&end_date=201307
*/
Yii::import('application.vendors.*');

class ScanPublishedCompeteCommand extends CConsoleCommand {
    public function run($args) {
        set_time_limit(0);
        ini_set("memory_limit", "128M");

        $num = 10;
        if (!empty($args)) {
            $num = (int) $args[0];
        }

        $url = "http://apps.compete.com/sites/";
        $apikey = "c85db60ab72872951b94561a708b0127";

        $chmodel = new CompeteHistory;

        $currday = date("j");
        if ($currday <= 15) {
            $querymonth = strtotime("-2 months");
            $querymonth = date("Ym", $querymonth);
        } else {
            $querymonth = strtotime("-1 month");
            $querymonth = date("Ym", $querymonth);
        }
        //echo $querymonth;

        for ($i=0; $i<50; $i++) {
        //#####for ($i=0; $i<1; $i++) {
            $domains = Yii::app()->db->createCommand()->select('id, domain_id, domain')->from('{{inventory}}')
                ->where("(compete_scaned IS NULL) AND ispublished=1")
                ->limit($num)
                ->queryAll();

            if (!empty($domains)) {
                foreach ($domains as $dv) {
                    $domain = $dv["domain"];
                    //echo $cpturl = $url.$domain."/trended/uv/?apikey=$apikey&start_date=201307&end_date=201307";
                    $cpturl = $url.$domain."/trended/uv/?apikey=$apikey&start_date=$querymonth&end_date=$querymonth";
                    //##for local testing $cpturl = "http://www.thenanogreen.com/t2.php";


                    $compete = @file_get_contents($cpturl);
                    $currvalue = -1;
                    if ($compete) {
                        $dc = json_decode($compete);
                        if ($dc->status == "OK") {
                            $uvs = $dc->data->trends->uv;
                            $month = $uvs[0]->date;
                            $currvalue = $uvs[0]->value;

                            $chmodel->unsetAttributes();
                            $chmodel->setIsNewRecord(true);
                            $chmodel->id        = NULL;
                            $chmodel->domain    = $dv["domain"];
                            $chmodel->domain_id = $dv["domain_id"];
                            $chmodel->inventory_id = $dv["id"];
                            $chmodel->month = $month;
                            $chmodel->value = $currvalue;
                            $chmodel->rawdata = $compete;
                            $chmodel->save();


                            //print_r($uvs);
                            //echo $uvs[0]->date;
                        }
                    }
                    //Update tbl.inventory.compete_scaned & tbl.inventory.compete_value

                    $crawlerarr = array();
                    $crawlerarr["compete_scaned"] = date('Y-m-d H:i:s');
                    if ($currvalue) $crawlerarr["compete_value"] = $currvalue;
                    Yii::app()->db->createCommand()->update('{{inventory}}', $crawlerarr, 'id=:id', array(':id'=>$dv['id']));
                }
            }
        }//end for;

    }

}
?>