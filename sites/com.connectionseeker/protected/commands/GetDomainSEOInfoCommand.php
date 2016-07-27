<?php
//http://www.yiiframework.com/wiki/91/implementing-cron-jobs-with-yii/
//http://www.yiiframework.com/forum/index.php?/topic/26551-setting-up-cronjob/
//How to call this script in crontab: php /path/to/cron.php syncTopBacklinks
//http://hudeyong926.iteye.com/blog/1283125 路径引用总结
/*
D:\WEBIDE\Language\php>php -q K:\NewHtdocs\yii\yii1.1.8.dev\sites\com.connectionseeker\cron.php GetDomainSEOInfo p1 p2
the $args will returns as following
array(
    [0] => p1
    [1] => p2
)
*/
Yii::import('application.vendors.*');

class GetDomainSEOInfoCommand extends CConsoleCommand {
    public function run($args) {
        //ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set("memory_limit", "128M");

        //if (!class_exists("SEOstats")) {
            require_once(Yii::app()->BasePath . "/vendors/SEOstats/src/class.seostats.php");
        //}

        $num = 3;
        if (!empty($args)) {
            $num = (int) $args[0];
        }
        $modified = time();
        //$expired = date('Y-m-d H:i:s', $modified - 7776000); // 7776000 means 90 days
        //$expired = date('Y-m-d H:i:s', $modified - 2592000); // 2592000 means 30 days
        $expired = date('Y-m-d H:i:s', $modified - 1296000); // 1296000 means 15 days
        //$expired = date('Y-m-d H:i:s', $modified - 864000); // 864000 means 10 days

        //'id, domain, googlepr, onlinesince, alexarank'
        $domains = Yii::app()->db->createCommand()->select()->from('{{domain}}')
            //->where('((onlinesince = 658454400 OR onlinesince = -1) OR (alexarank < 0) OR (modified IS NULL) OR (modified <= :modified))', array(':modified'=>$expired))
            //->where('((onlinesince = 658454400) OR (modified IS NULL) OR (modified <= :modified)) AND (touched_status>0)', array(':modified'=>$expired))
            ->where('((onlinesince = 658454400) OR (modified IS NULL) OR (modified <= :modified))', array(':modified'=>$expired))
            ->limit($num)
            ->queryAll();
            //->queryRow();

        if (!empty($domains)) {

            foreach ($domains as $dv) {
                $dcols = array();
                //$googlepr = SeoUtils::getGooglePageRank($dv['domain']);
                if ($dv['domain'] == $dv['rootdomain']) {
                    $homepage = "http://www." . $dv['domain'];
                } else {
                    $homepage = "http://" . $dv['domain'];
                }
                $s = new SEOstats($homepage);
                $googlepr = $s->Google_Page_Rank();
                echo $googlepr;
                echo $homepage;
                if (is_numeric($googlepr)) {
                    $dcols['googlepr'] = $googlepr;
                    if ($googlepr < 0 && $dv['googlepr'] >= 0) {
                        $dcols['googlepr'] = $dv['googlepr'];
                    }
                }
                $dcols['modified'] = date('Y-m-d H:i:s', $modified);
                //$dcols['modified_by'] = 99999999;
                $dcols['onlinesince'] = $dv['onlinesince'];

                $alexa = SeoUtils::getAlexaDomainInfo($dv['domain']);
                if (!empty($alexa)) {
                    if (isset($alexa['title'])) $dcols['title'] = $alexa['title'];
                    if (isset($alexa['email']) && empty($dv['email'])) $dcols['email'] = $alexa['email'];
                    if (isset($alexa['number']) && empty($dv['telephone'])) $dcols['telephone'] = $alexa['number'];
                    if (isset($alexa['owner'])) $dcols['owner'] = $alexa['owner'];
                    if (isset($alexa['street'])) $dcols['street'] = $alexa['street'];
                    if (isset($alexa['city'])) $dcols['city'] = $alexa['city'];
                    if (isset($alexa['zip'])) $dcols['zip'] = $alexa['zip'];
                    if (isset($alexa['state'])) $dcols['state'] = $alexa['state'];
                    if (isset($alexa['country'])) $dcols['country'] = $alexa['country'];
                    if (isset($alexa['traffic'])) $dcols['alexarank'] = $alexa['traffic'];

                    if (isset($alexa['date']) && (empty($dv['onlinesince']) || $dv['onlinesince'] == 658454400 || $dv['onlinesince'] == -1)) $dcols['onlinesince'] = strtotime($alexa['date']);

                    if (isset($alexa['cats'])) {
                        $dcols['awis_category_str'] = implode(",", $alexa['cats']);
                        $cscats = Utils::categoryMapping($alexa['cats']);

                        if ($dv['category']) {
                            $_existcat = substr($dv['category'], 1, -1);
                            //echo $_existcat;
                            if (!empty($_existcat)) {
                                $_es = explode("|", $_existcat);
                                //print_r($cscats);
                                //print_r($_es);
                                $cscats = array_merge($cscats, $_es);
                                $cscats = array_unique($cscats);
                                //print_r($cscats);
                            }
                        }
                        if ($cscats) {
                            $dcols['category'] = "|".implode("|", $cscats)."|";
                            $_types = Types::model()->bytype(array("category"))->findAllByAttributes(array("refid"=>$cscats));
                            $_cats = CHtml::listData($_types, 'refid', 'typename');
                            $dcols['category_str'] = implode(",", $_cats);
                        }
                    }
                }

                if ($dcols['onlinesince'] == 658454400 || $dv['onlinesince'] == -1) {

                    $onlinesince = SeoUtils::getDomainCreatedOn($dv['rootdomain']);
                    if ($onlinesince > 0) {
                        $dcols['onlinesince'] = strtotime($onlinesince);
                    } else {
                        $dcols['onlinesince'] = $onlinesince;
                    }
                }

                /*
                if ($dcols['linkingdomains'] == 0 && $dcols['inboundlinks'] == 0) {
                    $mjexpr = SeoUtils::getMajesticseExplorer($dv['domain']);
                    if ($mjexpr) {
                        $dcols['linkingdomains'] = $mjexpr['linkingdomains'];
                        $dcols['inboundlinks'] = $mjexpr['inboundlinks'];
                        $dcols['indexedurls'] = $mjexpr['indexedurls'];
                    }
                }
                */

                print_r($dcols);

                Yii::app()->db->createCommand()->update('{{domain}}', $dcols, 'id=:id', array(':id'=>$dv['id']));
            }

        }
        //print_r($domains);
    }

}
?>