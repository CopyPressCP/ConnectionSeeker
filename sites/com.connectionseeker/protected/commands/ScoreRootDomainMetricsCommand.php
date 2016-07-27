<?php
//http://www.yiiframework.com/wiki/91/implementing-cron-jobs-with-yii/
//http://www.yiiframework.com/forum/index.php?/topic/26551-setting-up-cronjob/
//How to call this script in crontab: php /path/to/cron.php syncTopBacklinks
//http://hudeyong926.iteye.com/blog/1283125 路径引用总结
/*
D:\WEBIDE\Language\php>php -q K:\NewHtdocs\yii\yii1.1.8.dev\sites\com.connectionseeker\cron.php ScoreRootDomainMetricsCommand p1 p2
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
define('DS', DIRECTORY_SEPARATOR);

class ScoreRootDomainMetricsCommand extends CConsoleCommand {
    public function run($args) {
        //ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set("memory_limit", "128M");

        $filepath = dirname(dirname(__FILE__)) . DS . "runtime" . DS;

        $fpr = fopen($filepath.'pendingurls.csv', 'r');
        $fpw = fopen($filepath.'rootdomainmetrics.csv', 'w');
        fputcsv($fpw, array("URL", "ROOT DOMAIN", "PR", "MOZ Rank", "Authority", "Alexa"));

        $accessID = "member-41b9bb683d";
        $secretKey = "5e07bbb01774fee92ed680bc62e56c8d";
        $expires = time() + 300;
        $stringToSign = $accessID."\n".$expires;
        $binarySignature = hash_hmac('sha1', $stringToSign, $secretKey, true);
        $urlSafeSignature = urlencode(base64_encode($binarySignature));
        $cols = "68719591424";//please reference http://apiwiki.seomoz.org/url-metrics#urlmetricsbitflags

        while (($data = fgetcsv($fpr, 1000, ",")) !== FALSE) {
            $num = count($data);
            $data = (array)$data;
            if (empty($data[0])) {
                continue;
            }
            $newdata = array();
            $newdata[0] = $data[0];
            $domain = SeoUtils::getDomain($data[0]);
            $domain = preg_replace('#^https?://#', '', $domain);
            $newdata[1] = $domain;
            $newdata[2] = "";
            $newdata[3] = "";
            $newdata[4] = "";
            $newdata[5] = "";

            $ggpr = CrawlerUtils::sgooglepr($domain);
            if (!empty($ggpr)) $newdata[2] = $ggpr["googlepr"];

            $req = "http://lsapi.seomoz.com/linkscape/url-metrics/".urlencode($domain)."?Cols=".$cols."&AccessID=".$accessID."&Expires=".$expires."&Signature=".$urlSafeSignature;
            echo $req;
            $mozrs = file_get_contents($req, true);
            if ($mozrs) {
                $moz = json_decode($mozrs, true);
                print_r($moz);
                $newdata[3] = $moz{"pmrp"};
                $newdata[4] = $moz{"pda"};
            }

            $salexa = CrawlerUtils::salexarank($domain);
            if (!empty($salexa)) $newdata[5] = $salexa["alexarank"];

            print_r($newdata);
            fputcsv($fpw, $newdata);

            echo "<p> $num fields in line $row: <br /></p>\n";
            $row++;
        }


        fclose($fpr);
        fclose($fpw);
    }

}
?>