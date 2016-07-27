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

class GetMozauthorityCommand extends CConsoleCommand {
    public function run($args) {
        //ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set("memory_limit", "128M");

        $num = 20;
        if (!empty($args)) {
            $num = (int) $args[0];
        }
        //$initdate = ;

        //#$accessID = "member-41b9bb683d";
        //#$secretKey = "5e07bbb01774fee92ed680bc62e56c8d";
	    $accessID = "mozscape-41b9bb683d";
	    $secretKey = "a4547778d49a662fa8525e84d4d2965e";
        $expires = time() + 300;
        $stringToSign = $accessID."\n".$expires;
        $binarySignature = hash_hmac('sha1', $stringToSign, $secretKey, true);
        $urlSafeSignature = urlencode(base64_encode($binarySignature));
        $cols = "68719591424";//please reference http://apiwiki.seomoz.org/url-metrics#urlmetricsbitflags

        $scaned = date("Y-m-d H:i:s");

        $domains = Yii::app()->db->createCommand()->select()->from('{{domain}}')
            ->where('status = 1')
            ->order('scaned ASC')
            ->limit($num)
            ->queryAll();

        //####$metrics = array('sgooglepr','salexarank','ssemrushkeywords','smozrank','sip','sspa');
        $metrics = array('sgooglepr','salexarank','sip','sspa');

        /*
        $domains = Yii::app()->db->createCommand()->select('ds.id, ds.domain')->from('{{domain_summary}} ds')
            ->where('ds.mozauthority = 0')
            ->join('{{inventory}} i', 'i.domain_id = ds.domain_id')
            ->limit($num)
            ->queryAll();
        */
        if (!empty($domains)) {
            foreach ($domains as $dv) {
                $domain  = preg_replace('#^https?://#', '', $dv["domain"]);
                //$domain = "www.".$domain;
                //$domain = "dev.mysql.com";
                $req = "http://lsapi.seomoz.com/linkscape/url-metrics/".urlencode($domain)."?Cols=".$cols."&AccessID=".$accessID."&Expires=".$expires."&Signature=".$urlSafeSignature;
                echo $req;
                $mozrs = file_get_contents($req, true);
                if ($mozrs) {
                    $moz = json_decode($mozrs, true);
                    print_r($moz);

                    $newdv = array();

                    if (isset($moz{"pda"})) {
                        $newdv["mozauthority"] = $moz{"pda"};
                        Yii::app()->db->createCommand()->update('{{blogger_program}}',$newdv, 'domain_id=:id', array(':id'=>$dv['id']));

                        $subdomain = SeoUtils::getDomain($domain);
                        if ($subdomain == $domain) {
                            if (isset($moz{"pmrp"}) && $moz{"pmrp"} > 0) $newdv["mozrank"] = $moz{"pmrp"};
                        } else {
                            $newdv["mozrank"] = $moz{"fmrp"};
                        }

                        Yii::app()->db->createCommand()->update('{{domain_summary}}', $newdv, 'domain_id=:id', array(':id'=>$dv['id']));
                    }
                }

                Yii::app()->db->createCommand()->update('{{domain}}', array("scaned"=>$scaned), 'id=:id', array(':id'=>$dv['id']));

                foreach ($metrics as $func) {
                    CrawlMetrics($func, $dv);
                }
            }
        }
    }

}

function CrawlMetrics($func, $dmarr){
    echo "====Geting ".$func."=============\r\n";
    $summaryattrs = Summary::attributeLabels();
    $currfield = substr($func, 1);

    $dmfields = array('googlepr','onlinesince','linkingdomains','inboundlinks','indexedurls','alexarank');
    $smraddtional = array('mozrank','acrank','mozauthority','uniquevisitors','facebookshares','twittershares','linkedinshares','semrushor');
    $now = time();

    $dv = array();
    $_domain = $dmarr["domain"];
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

    $domain_id = $dmarr['id'];
    Yii::app()->db->createCommand()->update('{{domain_crawler}}', $crawlerarr, 'domain_id=:id', array(':id'=>$domain_id));

    if (in_array($currfield, $dmfields)) {
        if (count($dv) >= 1) {
            Yii::app()->db->createCommand()->update('{{domain}}', $dv, 'id=:id', array(':id'=>$domain_id));

            //!!###Get the intersect between the cralwer result and summary attributes.#####
            $dv = array_intersect_key($dv, $summaryattrs);
            Yii::app()->db->createCommand()->update('{{domain_summary}}', $dv, 'domain_id=:domain_id', array(':domain_id'=>$domain_id));
        }
    } elseif (in_array($currfield, $smraddtional)) {
        Yii::app()->db->createCommand()->update('{{domain_summary}}', $dv, 'domain_id=:domain_id', array(':domain_id'=>$domain_id));
    } else {
        if (count($dv) >= 1) {
            Yii::app()->db->createCommand()->update('{{domain}}', $dv, 'id=:id', array(':id'=>$domain_id));
        }
    }
    print_r($dv);
}
?>