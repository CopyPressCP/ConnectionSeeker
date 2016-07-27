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

class GetSubscriptionInfoCommand extends CConsoleCommand {
    public function run($args) {
        //ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set("memory_limit", "128M");
        $xml = file_get_contents('http://enterprise.majesticseo.com/api_command.php?app_api_key=F4823AB4B88259A221E162F9865986E7&cmd=GetSubscriptionInfo');
        $rs = simplexml_load_string($xml);
        $attributes = (array)$rs->GlobalVars;
        $totalMoney = $attributes['@attributes']['TotalRetrievalResUnits'];
        if ($totalMoney <= 200000) {
            Utils::notice(array('content'=>"There is no more credits in your account, please charge it"));
        }

        $datatable = (array)$rs->DataTables->DataTable[0];
        $headers = explode('|', $datatable['@attributes']['Headers']);
        $data = explode('|', $datatable['Row'][0]);
        $total = count($headers);
        for($i=0;$i<$total;$i++) {
            if (trim($headers[$i]) == 'Expires' ) {
                $expire = $data[$i];
                break;
            }
        }
        if ($expire) {
            $expiretime = strtotime($expire);
            $interval = $expiretime - time() ;
            $hours = ceil($interval/3600);
            if ($interval <= 86400 * 5) {
                if ($totalMoney > 200000) {
                    $notes = "Your account will be expired in " . $hours. " hours, but there are enough creadits there, please use it and charget it also.";
                    //echo $notes;
                    Utils::notice(array('content'=>$notes));
                }
            }
        }
    }

}
?>