<?php
/*
D:\WEBIDE\Language\php>php -q K:\NewHtdocs\yii\yii1.1.8.dev\sites\com.connectionseeker\cron.php DomainResponse $num $offset
the $args will returns as following
array(
    [0] => $iostatus    //such as 3, means approved
    [1] => $num         //such as 10, get 10 records
    [1] => $offset
)
*/
Yii::import('application.vendors.*');

class DomainResponseCommand extends CConsoleCommand {
    public function run($args) {
        //ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set("memory_limit", "512M");

        $num      = 50;
        $offset   = 0;
        if (!empty($args)) {
            if (isset($args[0])) $num    = (int) $args[0];
            if (isset($args[1])) $offset = (int) $args[1];
        }
        //print_r($args);
/*
SELECT i.domain, i.domain_id, d.email, d.primary_email
FROM `lkm_inventory` i
LEFT JOIN `lkm_domain` d ON ( d.id = i.domain_id )
WHERE i.ispublished >0
*/

        //1domain_id,2domain,3email,4initialsentdate,5firstopendate,6firstreply
        $rsstr = "%s,%s,%s,%s,%s,%s\r";
        $filename = dirname(dirname(__FILE__)) . "/runtime/domainresponse_".time().".txt";
        

        for ($i=0; $i<=1000; $i++) {
            $num = 50;
            $offset = $i * 50;
            /*
            $domains = Yii::app()->db->createCommand()->select("DISTINCT(e.domain_id),d.domain,e.send_time,e.opened")
                ->from('{{email_queue}} e')
                ->join('{{domain}} d', 'd.id=e.domain_id')
                ->where("e.status=1 AND e.from>0 AND e.domain_id>0 AND (e.is_reply=0 OR (e.is_reply IS NULL))")
                ->group('e.domain_id')
                ->order('e.domain_id ASC')
                ->limit($num)
                ->offset($offset)
                ->queryAll();
                */
            $domains = Yii::app()->db->createCommand()->select('domain_id,domain')
                ->from('{{inventory}}')
                ->where("ispublished > 0")
                ->order('domain_id ASC')
                ->limit($num)
                ->offset($offset)
                ->queryAll();

            if (!empty($domains)) {
                foreach ($domains as $pv) {
                    $domain_id = $pv["domain_id"];
                    $domain    = $pv["domain"];
                    $realto    = "";
                    $toarr     = array();
                    /*
                    $initsent  = $pv["send_time"];
                    $firstopen = $pv["opened"] ? date("Y-m-d H:i:s", $pv["opened"]) : "";
                    */
                    $initsent  = 0;
                    $firstopen = 0;
                    $firstreply = 0;

                    $eqs = Yii::app()->db->createCommand()->select('*')
                        ->from('{{email_queue}}')
                        ->where("domain_id = '".$domain_id."' AND status = 1")
                        ->order('id ASC')
                        ->queryAll();
                    if ($eqs) {
                        foreach ($eqs as $e) {
                            $fo = Mailer::model()->findByAttributes(array('email_from'=>$e['email_from']));
                            if ($fo) {
                                $toarr[$e['to']] = 0;
                                if (empty($initsent)) $initsent = $e["send_time"];
                                if (empty($firstopen) && !empty($e["opened"])) $firstopen = date("Y-m-d H:i:s", $e["opened"]);
                            } else {
                                if (empty($firstopen)) $firstopen = $e["send_time"];//think about the email who open many times
                                if (empty($firstreply)) $firstreply = $e["send_time"];
                            }
                            unset($fo);
                        }
                    }
                    if ($toarr) $realto = implode("|", array_keys($toarr));
                    $_rsstr = sprintf($rsstr, $domain_id, $domain, $realto, $initsent, $firstopen, $firstreply);
                    file_put_contents($filename, $_rsstr, FILE_APPEND | LOCK_EX);
                    unset($eqs);
                }
            } else {
                break;
            }

            unset($domains);
        }//end for;
    }
}
?>