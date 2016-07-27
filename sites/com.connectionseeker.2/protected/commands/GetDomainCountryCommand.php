<?php
//http://www.yiiframework.com/wiki/91/implementing-cron-jobs-with-yii/
//http://www.yiiframework.com/forum/index.php?/topic/26551-setting-up-cronjob/
//How to call this script in crontab: php /path/to/cron.php syncTopBacklinks
//http://hudeyong926.iteye.com/blog/1283125 路径引用总结
/*
D:\WEBIDE\Language\php>php -q K:\NewHtdocs\yii\yii1.1.8.dev\sites\com.connectionseeker\cron.php GetSeomozRank p1 p2
the $args will returns as following
array(
    [0] => p1
    [1] => p2
)

http://apiwiki.seomoz.org/free-api-explained
http://apiwiki.seomoz.org/url-metrics#urlmetricsbitflags
http://theeasyapi.com/docs/services/seomoz/responsevariables

Array
(
    [fmrr] => 1.23799804292E-10       Subdomain MozRank of raw score
    [fmrp] => 2.76473191438           Subdomain MozRank
    [umrp] => 4.19866458955           Page MozRank
    [pmrr] => 1.58283054227E-7        Root Domain MozRank of raw score
    [umrr] => 5.58724831916E-11       Page MozRank of raw score
    [pmrp] => 4.72661655635           Root Domain MozRank
    [pda] => 51.0460253411            Domain Authority
)
*/
Yii::import('application.vendors.*');

class GetDomainCountryCommand extends CConsoleCommand {
    public function run($args) {
        //ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set("memory_limit", "128M");

        $num = 10;
        if (!empty($args)) {
            $num = (int) $args[0];
        }
        $initdate = "2012-10-25 08:30:53";
        $initsec = strtotime($initdate);
        $now = time();
        $offset = round(($now - $initsec)/60);//how many minutes later..
        $offset = $offset * 10;

        $domains = Yii::app()->db->createCommand()->select('d.id, d.domain')->from('{{domain}} d')
            //->join('{{inventory}} i', 'i.domain_id = d.id')
            ->limit($num)
            ->offset($offset)
            ->queryAll();

        if (!empty($domains)) {
            foreach ($domains as $dv) {
                $domain  = preg_replace('#^https?://#', '', $dv["domain"]);
                $da = dns_get_record($domain, DNS_A);
                if ($da) {
                    foreach ($da as $d) {
                        if ($d["type"] == "A") {
                            $dv["ip"] = $ip = $d["ip"];
                            $hostips = SeoUtils::getCountryByIP($ip);
                            if ($hostips) {
                                $dv["host_country"] = trim($hostips["scn"]);
                                $dv["host_city"] = trim($hostips["city"]);
                            }
                            break;
                        } else {
                            continue;
                        }
                    }
                    print_r($dv);

                    Yii::app()->db->createCommand()->update('{{domain}}', $dv, 'id=:id', array(':id'=>$dv['id']));
                }

                /*
                if ($mozrs) {
                    $moz = json_decode($mozrs, true);
                    print_r($moz);
                    
                    $dv["mozauthority"] = $moz{"pda"};
                    $subdomain = SeoUtils::getDomain($domain);
                    if ($subdomain == $domain) {
                        $dv["mozrank"] = $moz{"pmrp"};
                    } else {
                        $dv["mozrank"] = $moz{"fmrp"};
                    }

                    Yii::app()->db->createCommand()->update('{{domain_summary}}', $dv, 'id=:id', array(':id'=>$dv['id']));
                }
                */
            }
        }
    }

}
?>