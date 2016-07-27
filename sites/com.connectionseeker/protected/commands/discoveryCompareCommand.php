<?php
//http://www.yiiframework.com/wiki/91/implementing-cron-jobs-with-yii/
//http://www.yiiframework.com/forum/index.php?/topic/26551-setting-up-cronjob/
//How to call this script in crontab: php /path/to/cron.php syncTopBacklinks
/*
D:\WEBIDE\Language\php>php -q K:\NewHtdocs\yii\yii1.1.8.dev\sites\com.connectionseeker\cron.php discoveryCompare p1 p2
the $args will returns as following
array(
    [0] => p1
    [1] => p2
)
*/
Yii::import('application.vendors.*');
define('DS', DIRECTORY_SEPARATOR);

class discoveryCompareCommand extends CConsoleCommand {
    public function run($args) {
        //ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set("memory_limit", "512M");

        $competitor = Yii::app()->db->createCommand()
            ->select('cd.*, dxd.discovery_id, dxd.id AS dxdid, dxd.domain_id AS dxddomainid,dd.id AS cmpid')
            ->from('{{discovery_xref_domain}} dxd')
            ->join('{{client_discovery}} cd', 'cd.id=dxd.discovery_id')
            ->join('{{discovery_domain}} dd', 'dd.domain_id=dxd.domain_id')
            ->where("dxd.status = 0 AND dxd.historic_called IS NOT NULL")
            ->queryRow();
        //print_r($competitor);

        if (!$competitor) {
            echo "No new competitors!";
            return false;
        }

        //update discovery current progress
        $update = Yii::app()->db->createCommand()
            ->update('{{client_discovery}}', array("progress"=>2), 'id=:id', array(':id'=>$competitor['discovery_id']));

        $competitor_id = $competitor['id'];
        if ($competitor["domain_id"] == $competitor["dxddomainid"]) {
            $callarr = array();
            //That means it is the client domain, so we need hide all of their backdomains & backlinks

            $callarr["status"] = -1;
            $update = Yii::app()->db->createCommand()
                ->update('{{discovery_backdomain}}', $callarr, 'discovery_id=:disid AND competitor_id=:cmpid',
                        array(':disid'=>$competitor['discovery_id'],':cmpid'=>$competitor['cmpid']));

            /*
            $update = Yii::app()->db->createCommand()
                ->update('{{discovery_backlink}}', $callarr, 'discovery_id=:disid AND competitor_id=:cmpid',
                        array(':disid'=>$competitor['discovery_id'],':cmpid'=>$competitor['cmpid']));
            */

            $callarr["status"] = 1;
            $update = Yii::app()->db->createCommand()
                        ->update('{{discovery_xref_domain}}', $callarr, 'id=:dxdid', array(':dxdid'=>$competitor['dxdid']));
        } else {
            /*
            select dxd.id AS dxdid FROM lkm_discovery_xref_domain dxd LEFT JOIN lkm_client_discovery cd 
            ON (cd.id=dxd.discovery_id) WHERE cd.domain_id = dxd.domain_id AND dxd.historic_called IS NOT NULL;
            */
            $client = Yii::app()->db->createCommand()
                ->select('dd.id AS cmpid')
                ->from('{{discovery_xref_domain}} dxd')
                ->join('{{client_discovery}} cd', 'cd.id=dxd.discovery_id')
                ->join('{{discovery_domain}} dd', 'dd.domain_id=dxd.domain_id')
                ->where("cd.domain_id = dxd.domain_id AND dxd.historic_called IS NOT NULL")
                ->queryRow();
            print_r($client);
            if ($client) {
                //Select ALL OF THE back DOMAINS of the client domain, and use these back domain to compare their competitor backdomain
                //UPDATE discovery_backdomain SET status = 1 WHERE discovery_id=:disid AND competitor_id=:cmpid AND domain_id = (select domain_id where ...)
                /*
                $q = "UPDATE discovery_backdomain SET status = 1 WHERE discovery_id=".$competitor['discovery_id'].
                     " AND competitor_id=".$competitor['id']." AND domain_id=(SELECT domain_id FROM WHERE discovery_id=".$competitor['discovery_id'].
                     " AND competitor_id=".$client['cmpid']." )";
                */
                echo $q = "UPDATE {{discovery_backdomain}} dbd1
                    LEFT OUTER JOIN {{discovery_backdomain}} dbd2 
                    ON (dbd1.domain_id=dbd2.domain_id AND dbd2.discovery_id=dbd1.discovery_id)
                    SET dbd1.status = 1
                    WHERE dbd1.discovery_id = ".$competitor['discovery_id'].
                        " AND dbd2.competitor_id=".$client["cmpid"].
                        " AND dbd1.competitor_id=".$competitor['cmpid'];
                Yii::app()->db->createCommand($q)->execute();

                /*
                echo $q = "UPDATE {{discovery_backlink}} dbd1
                    LEFT OUTER JOIN {{discovery_backdomain}} dbd2 
                    ON (dbd1.domain_id=dbd2.domain_id AND dbd2.discovery_id=dbd1.discovery_id)
                    SET dbd1.status = 1
                    WHERE dbd1.discovery_id = ".$competitor['discovery_id'].
                        " AND dbd2.competitor_id=".$client["cmpid"].
                        " AND dbd1.competitor_id=".$competitor['cmpid'];
                Yii::app()->db->createCommand($q)->execute();
                */

                $callarr["status"] = 1;
                $update = Yii::app()->db->createCommand()
                            ->update('{{discovery_xref_domain}}', $callarr, 'id=:dxdid', array(':dxdid'=>$competitor['dxdid']));
            }
        }

    }

}
?>