<?php
//http://www.yiiframework.com/wiki/91/implementing-cron-jobs-with-yii/
//http://www.yiiframework.com/forum/index.php?/topic/26551-setting-up-cronjob/
//How to call this script in crontab: php /path/to/cron.php syncTopBacklinks
//http://hudeyong926.iteye.com/blog/1283125 路径引用总结
/*
D:\WEBIDE\Language\php>php -q K:\NewHtdocs\yii\yii1.1.8.dev\sites\com.connectionseeker\cron.php DiscoveryAutomationCommand p1 p2
the $args will returns as following
array(
    [0] => p1
    [1] => p2
)
*/
date_default_timezone_set('EST');

Yii::import('application.vendors.*');
Yii::import('ext.yii-mail.*');
define('DS', DIRECTORY_SEPARATOR);
error_reporting(E_ALL);

class GetAutomationQueueCommand extends CConsoleCommand {

    public function run($args) {
        //ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set("memory_limit", "512M");

        /*
        $num = 5;
        if (!empty($args)) {
            $num = (int) $args[0];
        }
        $offset = 0;
        if (!empty($args) && isset($args[1])) {
            $offset = (int) $args[1];
        }
        */

        $queuefile = dirname(dirname(__FILE__)) . DS . "runtime" . DS . "nofqueues.json";

        $nowtimestamp = time();
        $now = date("Y-m-d H:i:s", $nowtimestamp);

        $queue = array();
        $queue["total"] = 0;
        $queue["total_potential"] = 0;
        $queue["querytime"] = $now;

        $rules = Yii::app()->db->createCommand()->select()->from('{{client_discovery}}')
            ->where('(status = 1 AND progress>=2 AND progress<5 AND complete_with_automation = 1)')
            ->queryAll();
        if (empty($rules)) return false;

        foreach($rules as $rl) {
            $r = json_decode($rl["automation_setting"], true);
            $queue["total"] += genDomainQueue($r+array('discovery_id'=>$rl["id"]));
            $queue["total_potential"] += genDomainQueue($r+array('discovery_id'=>$rl["id"]), true);
        }//end of foreach

        echo "Put Number Into File";
        @file_put_contents($queuefile, json_encode($queue));

    }//end of function

}


function genDomainQueue($r, $is_potential=false) {
    if ($is_potential) {
        $where = "WHERE ldb.status=0 AND (dd.contactemail IS NULL AND dd.lastcrawled IS NULL)";
    } else {
        //##$where = "WHERE (t.owner IS NOT NULL OR t.owner != '') AND (t.primary_email IS NOT NULL OR t.primary_email != '')";
        //$where = "WHERE (t.primary_email IS NOT NULL OR t.primary_email != '')";
        $where = "WHERE ldb.status=0 AND ( (t.primary_email IS NOT NULL AND t.primary_email != '') OR (t.primary_email2 IS NOT NULL AND t.primary_email2 != '') OR (dd.contactemail IS NOT NULL AND dd.contactemail != '' AND dd.contactemail != '0'))";
    }

    if (isset($r["category"]) && $r["category"]) {
        $_category = explode("|", $r["category"]);
        $_whr = "";
        foreach ($_category as $v) {
            if ($_whr) $_whr .= " OR ";
            $_whr .= "t.category LIKE '%|".$v."|%'"; 
        }
        $where .= " AND (".$_whr.")";
    }

    if (isset($r["has_owner"])) {
        if ($r["has_owner"] == 1) {
            $where .= " AND (t.owner IS NOT NULL OR t.owner != '')";
        }
    }

    if (isset($r["touched_status"]) && $r["touched_status"]) {
        if (is_array($r["touched_status"])) {
            $_status = implode(",", $r["touched_status"]);
        } else {
            $_status = str_replace("|", ",", $r["touched_status"]);
        }
        $where .= " AND t.touched_status IN (".$_status.")";
    }

    if ($r["alexarank"]) {
        $alexavalue = $r["alexarank"];
        $op = "";
        if(preg_match('/^(?:\s*(<>|<=|>=|<|>|=))?(.*)$/',$alexavalue,$matches)) {
            $alexavalue = $matches[2];
            $op = $matches[1];
        }
        if (empty($op)) $op="=";
        if ($alexavalue) $where .= " AND (t.alexarank ".$op." '".$alexavalue."')";
    }

    if ($r["semrushkeywords"]) {
        if ($r["semrushkeywords"] > 0) {
            $where .= " AND (rsummary.semrushkeywords>'0')";
        } elseif ($r["semrushkeywords"] < 0) {
            $where .= " AND (rsummary.semrushkeywords<'0')";
        } else {
            $where .= " AND (rsummary.semrushkeywords IS NULL)";
        }
    }

    if (isset($r["discovery_id"]) && $r["discovery_id"]) {
        $where .= " AND (ldb.discovery_id = '".$r["discovery_id"]."')";
    }

    if (!empty($r["host_country"])) {
        $host_country = trim($r["host_country"]);
        $host_country = strtoupper($host_country);
        $host_country = str_replace(" ", "", $host_country);
        $host_country = str_replace(",", "','", $host_country);
        if ($host_country) $where .= " AND (t.host_country IN ('".$host_country."'))";
    }

    if ($r["mozauthority"]) {
        $mozauthority = $r["mozauthority"];
        $op = "";
        if(preg_match('/^(?:\s*(<>|<=|>=|<|>|=))?(.*)$/',$mozauthority,$matches)) {
            $mozauthority = $matches[2];
            $op = $matches[1];
        }
        if (empty($op)) $op="=";
        if ($mozauthority) $where .= " AND (rsummary.mozauthority ".$op." '".$mozauthority."')";
    }

    if ($r["mozauthority"] || $r["semrushkeywords"]) {
        echo $q = "SELECT COUNT(DISTINCT ldb.domain_id) AS qcount FROM `lkm_discovery_backdomain` AS ldb 
                   LEFT OUTER JOIN lkm_domain t ON (t.id=ldb.domain_id AND ldb.mailer_id=0)
                   LEFT OUTER JOIN lkm_domain_onpage AS dd ON (t.id = dd.domain_id)
                   LEFT OUTER JOIN lkm_domain_summary rsummary ON (t.id = rsummary.domain_id) ".$where;
    } else {
        echo $q = "SELECT COUNT(DISTINCT ldb.domain_id) AS qcount FROM `lkm_discovery_backdomain` AS ldb 
                   LEFT OUTER JOIN lkm_domain t ON (t.id=ldb.domain_id AND ldb.mailer_id=0)
                   LEFT OUTER JOIN lkm_domain_onpage AS dd ON (t.id = dd.domain_id) ".$where;
    }

    //echo $q;
    $rs = Yii::app()->db->createCommand($q)->queryRow();
    //print_r($rs);
    if ($rs) {
        return $rs["qcount"];
    } else {
        return 0;
    }

}

?>