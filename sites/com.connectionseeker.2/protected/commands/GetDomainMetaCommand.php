<?php
//http://www.yiiframework.com/wiki/91/implementing-cron-jobs-with-yii/
//http://www.yiiframework.com/forum/index.php?/topic/26551-setting-up-cronjob/
//How to call this script in crontab: php /path/to/cron.php syncTopBacklinks
//http://hudeyong926.iteye.com/blog/1283125 路径引用总结
/*
D:\WEBIDE\Language\php>php -q K:\NewHtdocs\yii\yii1.1.8.dev\sites\com.connectionseeker\cron.php GetDomainMeta p1 p2
the $args will returns as following
array(
    [0] => p1
    [1] => p2
)

http://apiwiki.seomoz.org/free-api-explained
http://apiwiki.seomoz.org/url-metrics#urlmetricsbitflags
http://theeasyapi.com/docs/services/seomoz/responsevariables
*/
Yii::import('application.vendors.*');

class GetDomainMetaCommand extends CConsoleCommand {
    public function run($args) {
        //ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set("memory_limit", "128M");

        $num = 10;
        if (!empty($args)) {
            $num = (int) $args[0];
        }


        //####Yii::app()->db->createCommand("SET NAMES utf8 ;")->execute();
        $domains = Yii::app()->db->createCommand()->select('d.id, d.domain')->from('{{domain}} d')
            //->join('{{inventory}} i', 'i.domain_id = d.id')
            ->where("meta_keywords IS NULL")
            ->limit($num)
            ->queryAll();

        if (!empty($domains)) {
            foreach ($domains as $dv) {
                $domain  = preg_replace('#^https?://#', '', $dv["domain"]);
                $metas = @get_meta_tags("http://".$domain);
                $dv["meta_keywords"] = "";
                $dv["meta_description"] = "";
                print_r($metas);
                if ($metas) {
                    if (isset($metas["keywords"]) && !empty($metas["keywords"])) {
                        $dv["meta_keywords"] = mb_convert_encoding(trim($metas["keywords"]), "UTF-8", "auto");
                        $dv["meta_keywords"] = mb_substr($dv["meta_keywords"], 0, 1000);
                    }
                    if (isset($metas["description"]) && !empty($metas["description"])) {
                        $dv["meta_description"] = mb_convert_encoding(trim($metas["description"]), "UTF-8", "auto");
                        $dv["meta_description"] = mb_substr($dv["meta_description"], 0, 1000);
                    }
                }
                print_r($dv);
                try {
                    Yii::app()->db->createCommand()->update('{{domain}}', $dv, 'id=:id', array(':id'=>$dv['id']));
                } catch (Exception $e) {
                    echo 'Error - ' . $e->getMessage();
                    $dv["meta_keywords"] = "";
                    $dv["meta_description"] = "";
                    Yii::app()->db->createCommand()->update('{{domain}}', $dv, 'id=:id', array(':id'=>$dv['id']));
                }
            }
        }
    }

}
?>