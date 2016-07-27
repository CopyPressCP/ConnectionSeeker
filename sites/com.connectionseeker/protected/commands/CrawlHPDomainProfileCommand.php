<?php
//http://www.yiiframework.com/wiki/91/implementing-cron-jobs-with-yii/
//http://www.yiiframework.com/forum/index.php?/topic/26551-setting-up-cronjob/
//How to call this script in crontab: php /path/to/cron.php syncTopBacklinks
//http://hudeyong926.iteye.com/blog/1283125 路径引用总结
/*
//Crawl the highest Priority Domain's profile.
D:\WEBIDE\Language\php>php -q K:\NewHtdocs\yii\yii1.1.8.dev\sites\com.connectionseeker\cron.php CrawlHPDomainProfile $field $num
the $args will returns as following
array(
    [0] => $field
    [1] => $num
)
*/
Yii::import('application.vendors.*');

class CrawlHPDomainProfileCommand extends CConsoleCommand {
    public function run($args) {
        //ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set("memory_limit", "128M");

        $func = "sgooglepr";
        $num = 10;
        if (!empty($args)) {
            $func = $args[0];
            if (isset($args[1])) $num = (int) $args[1];
        }

        $labels = Crawler::attributeLabels();
        $fields = array_keys($labels);
        if (!in_array($func, $fields)) {
            //echo $num;
            exit;
        }

        $summaryattrs = Summary::attributeLabels();

        $dmfields = array('googlepr','onlinesince','linkingdomains','inboundlinks','indexedurls','alexarank');
        $currfield = substr($func, 1);
        $smraddtional = array('mozrank','acrank','mozauthority','uniquevisitors','facebookshares','twittershares','linkedinshares','semrushor');
        $now = time();
        $frequence = Yii::app()->params['cronfrq'];
        $sfrq = 4;
        if (isset($frequence[$func])) {
            $sfrq = $frequence[$func];
        }
        //1 year = 365 days = 365 * 86400 = 31536000 second
        $period = round(31536000 / $sfrq);
        $offsetsec = $now - $period;

        $domains = Yii::app()->db->createCommand()->select('dc.id, dc.domain_id, dc.domain')->from('{{domain_crawler}} dc')
            ->where("(`$func` <= $offsetsec) OR (`$func` IS NULL) OR (`$func` = 0)")
            ->join('{{inventory}} i', 'i.domain_id = dc.domain_id')
            ->limit($num)
            //->offset($offset)
            ->queryAll();
        if (empty($domains)) {
            $domains = Yii::app()->db->createCommand()->select('dc.id, dc.domain_id, dc.domain')->from('{{domain_crawler}} dc')
                ->where("(`$func` <= $offsetsec) OR (`$func` IS NULL) OR (`$func` = 0)")
                ->join('{{client_domain}} i', 'i.domain_id = dc.domain_id')
                ->limit($num)
                //->offset($offset)
                ->queryAll();
        }

        if (!empty($domains)) {
            foreach ($domains as $dv) {
                echo $_domain = $dv["domain"];
                if ($_domain[0] == ".") {
                    $dv["domain"] = $_domain = substr($_domain, 1);
                }
                $dpf = CrawlerUtils::$func($_domain);
                $crawlerarr[$func] = $now;
                if ($dpf) {
                    $dv += $dpf;
                } else {
                    //$dv[$func] = $now;
                }

                print_r($dv);
                Yii::app()->db->createCommand()->update('{{domain_crawler}}', $crawlerarr, 'id=:id', array(':id'=>$dv['id']));

                $domain_id = $dv['domain_id'];
                if (in_array($currfield, $dmfields)) {
                    unset($dv['domain_id']);
                    unset($dv['id']);
                    if (count($dv) >= 1) {
                        Yii::app()->db->createCommand()->update('{{domain}}', $dv, 'id=:id', array(':id'=>$domain_id));

                        //!!###Get the intersect between the cralwer result and summary attributes.#####
                        $dv = array_intersect_key($dv, $summaryattrs);
                        Yii::app()->db->createCommand()->update('{{domain_summary}}', $dv, 'domain_id=:domain_id', array(':domain_id'=>$domain_id));
                    }
                } elseif (in_array($currfield, $smraddtional)) {
                    unset($dv["id"]);
                    Yii::app()->db->createCommand()->update('{{domain_summary}}', $dv, 'domain_id=:domain_id', array(':domain_id'=>$domain_id));
                } else {
                    unset($dv['domain_id']);
                    unset($dv['id']);
                    //do nothing;
                    if (count($dv) >= 1) {
                        Yii::app()->db->createCommand()->update('{{domain}}', $dv, 'id=:id', array(':id'=>$domain_id));
                    }
                }
            }
        }
    }

}
?>