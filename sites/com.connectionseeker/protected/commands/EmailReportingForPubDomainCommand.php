<?php
/*
D:\WEBIDE\Language\php>php -q K:\NewHtdocs\yii\yii1.1.8.dev\sites\com.connectionseeker\cron.php EmailReportingForPubDomain $num $offset
the $args will returns as following
array(
    [0] => $iostatus    //such as 3, means approved
    [1] => $num         //such as 10, get 10 records
    [1] => $offset
)
*/
Yii::import('application.vendors.*');

class EmailReportingForPubDomainCommand extends CConsoleCommand {
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

        //1domain_id,2domain,3realto,4nofinitial,5nofoutcs,6total,7nofviacs
        $rsstr = "%s,%s,%s,%s,%s,%s,%s\r";
        $filename = dirname(dirname(__FILE__)) . "/runtime/erfpd_".time().".txt";
        

        for ($i=0; $i<=1000; $i++) {
            $num = 50;
            $offset = $i * 50;
            $pubdomains = Yii::app()->db->createCommand()->select('domain_id,domain')
                ->from('{{inventory}}')
                ->where("ispublished > 0")
                ->order('domain_id ASC')
                ->limit($num)
                ->offset($offset)
                ->queryAll();

            if (!empty($pubdomains)) {
                foreach ($pubdomains as $pv) {
                    $domain_id = $pv["domain_id"];
                    $domain    = $pv["domain"];
                    $realto    = "";
                    $toarr     = array();
                    $nofinit   = 0;
                    $nofoutcs  = 0;
                    $total     = 0;
                    $nofviacs  = 0;

                    $eqs = Yii::app()->db->createCommand()->select('*')
                        ->from('{{email_queue}}')
                        ->where("domain_id = '".$domain_id."' AND status = 1")
                        ->order('id ASC')
                        ->queryAll();
                    if ($eqs) {
                        $nofinit = 1;
                        foreach ($eqs as $e) {
                            $fo = Mailer::model()->findByAttributes(array('email_from'=>$e['email_from']));
                            if ($fo) {
                                $nofviacs++;
                                $toarr[$e['to']] = 0;
                            } else {
                                $nofoutcs++;
                            }
                            $total++;
                            unset($fo);
                        }
                    }
                    if ($toarr) $realto = implode("|", array_keys($toarr));
                    $_rsstr = sprintf($rsstr, $domain_id, $domain, $realto, $nofinit, $nofoutcs, $total, $nofviacs);
                    file_put_contents($filename, $_rsstr, FILE_APPEND | LOCK_EX);
                    unset($eqs);
                }
            } else {
                break;
            }

            unset($pubdomains);
        }//end for;
    }
}
?>