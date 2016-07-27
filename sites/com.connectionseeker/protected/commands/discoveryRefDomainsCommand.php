<?php
//http://www.yiiframework.com/wiki/91/implementing-cron-jobs-with-yii/
//http://www.yiiframework.com/forum/index.php?/topic/26551-setting-up-cronjob/
//How to call this script in crontab: php /path/to/cron.php syncTopBacklinks
/*
D:\WEBIDE\Language\php>php -q K:\NewHtdocs\yii\yii1.1.8.dev\sites\com.connectionseeker\cron.php discoveryBacklinks p1 p2
the $args will returns as following
array(
    [0] => p1
    [1] => p2
)
*/
Yii::import('application.vendors.*');
define('DS', DIRECTORY_SEPARATOR);

class discoveryRefDomainsCommand extends CConsoleCommand {
    public function run($args) {
        //ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set("memory_limit", "512M");

        $mjcachepath = dirname(dirname(__FILE__)) . DS . runtime . DS . "mjseo" . DS;
        //Create the cache majesticseo dir
        if (!file_exists($mjcachepath)) {
            if (!mkdir($mjcachepath, 0777)) {
                $_content =  "Unable to generate a cache folder for  - $mjcachepath.";
                Utils::notice(array('content'=>$_content, 'tos'=>'leo@infinitenine.com'));
                die();
            }
        }

        //Yii::app()->db->createCommand("SET NAMES 'utf8'")->execute();

        // here we are doing what we need to do
        //echo "it returns!";
        //print_r($args);

        $competitor = Yii::app()->db->createCommand()
            ->select('dd.*, dxd.discovery_id, dxd.id AS dxdid, cd.domain_id AS main_domain_id')
            ->from('{{discovery_xref_domain}} dxd')
            ->join('{{discovery_domain}} dd', 'dd.domain_id=dxd.domain_id')
            ->join('{{client_discovery}} cd', 'cd.id=dxd.discovery_id')
            ->where("dxd.historic_called IS NULL AND dxd.status = 0 AND dd.status = 0 AND cd.status=1")
            ->queryRow();
        //print_r($competitor);

        if (!$competitor) {
            echo "No new competitors!";
            return false;
        }
        $competitor_id = $competitor['id'];

        $callapi = false;
        //##$daysoffset = 86400 * 90;//90 days
        if ($competitor['use_historic_index']) {
            //data expired,we need call the api again;
            $datasource = 'historic';
            $callapi = true;
        } else {
            $datasource = 'fresh';
            $callapi = true;
        }

        if (!$callapi) return false;

        $cptdomain = trim($competitor['domain']);
        if ($competitor['main_domain_id'] == $competitor['domain_id']) {
            //If it is client Domain, then get top 500 domains.
            //###$mjurl = "http://enterprise.majesticseo.com/api_command?app_api_key=F4823AB4B88259A221E162F9865986E7&cmd=GetTopBackLinks&MaxSourceURLs=500&GetRootDomainData=1&AnalysisResUnits=10000&ShowDomainInfo=1&GetUrlData=0&UseResUnits=1&URL=".urlencode($cptdomain);
            $mjurl = "http://enterprise.majesticseo.com/api/json?app_api_key=F4823AB4B88259A221E162F9865986E7&cmd=GetRefDomains&Count=1000&item0=".urlencode($cptdomain);
        } else {
            //###$mjurl = "http://enterprise.majesticseo.com/api_command?app_api_key=F4823AB4B88259A221E162F9865986E7&cmd=GetTopBackLinks&MaxSourceURLs=250&GetRootDomainData=1&AnalysisResUnits=10000&ShowDomainInfo=1&GetUrlData=0&UseResUnits=1&URL=".urlencode($cptdomain);
            $mjurl = "http://enterprise.majesticseo.com/api/json?app_api_key=F4823AB4B88259A221E162F9865986E7&cmd=GetRefDomains&Count=1000&item0=".urlencode($cptdomain);
        }
        $mjurl .= "&datasource={$datasource}";

        $isnewcache = true;
        $mjcachefile = $mjcachepath . $cptdomain . "." . $datasource . ".xml";
        if (file_exists($mjcachefile)) {
            $mjcachestat = stat($mjcachefile);
            //7776000 means 90 days
            if (time() - $mjcachestat['mtime'] < 7776000) {
                //if the last modified date is less than 90days, then call the cache file.
                $mjurl = $mjcachefile;
                $isnewcache = false;
            }
        }
        echo $mjurl;

        $fstr = file_get_contents($mjurl);

        $total = 0;
        if ($fstr) {
            $calledtime = time();//this is version control,we need lock this competitor domain
            $calledtime = date("Y-m-d H:i:s", $calledtime);

            $rs = json_decode($fstr);
            if ($rs->Code == "OK") {
                if ($rs->DataTables) {
                    //##### Transaction Start ######//
                    $transaction = Yii::app()->db->beginTransaction();
                    try {
                        if ($datasource == 'historic') {
                            $callarr = array('historic_called' => $calledtime,);
                            $historic_called = $calledtime;
                            $fresh_called = NULL;
                        } else {
                            $callarr = array('fresh_called' => $calledtime,);
                            $fresh_called = $calledtime;
                            $historic_called = NULL;
                        }
                        $update = Yii::app()->db->createCommand()
                            ->update('{{discovery_xref_domain}}', $callarr, 'id=:dxdid', array(':dxdid'=>$competitor['dxdid']));

                        //update the table.competitor's last call api time
                        $update = Yii::app()->db->createCommand()
                            ->update('{{discovery_domain}}', $callarr, 'id=:id', array(':id'=>$competitor_id));

                        //update discovery current progress
                        $update = Yii::app()->db->createCommand()
                            ->update('{{client_discovery}}', array("progress"=>1), 'id=:id', array(':id'=>$competitor['discovery_id']));

                        //echo $rs ->DataTables->Request->Data[0]->TotalRefDomains;
                        if ($rs->DataTables->Request->Data[0]->TotalRefDomains > 0) {
                            $rsdata = $rs->DataTables->Results->Data;
                            echo "Step 2: batch Update lkm_competitor_backdomain and lkm_domain!\n";
                            foreach ($rsdata as $rsd) {
                                $do = Yii::app()->db->createCommand()->select('id')->from('{{domain}}')
                                    ->where('domain=:domain', array(':domain'=>$rsd->Domain))
                                    ->queryRow();
                                $idx = $rsd->Position;

                                $__dinfo = array();
                                $__dinfo['domain'] = $rsd->Domain;
                                $__dinfo['rootdomain'] = SeoUtils::getDomain($rsd->domain);
                                $__dinfo['alexarank'] = $rsd->AlexaRank;
                                $__dinfo['indexedurls'] = $rsd->IndexedURLs;
                                $__dinfo['ip'] = $rsd->IP;
                                $__dinfo['subnet'] = $rsd->SubNet;
                                $__dinfo['tld'] = $rsd->TLD;
                                $__dinfo['country'] = $rsd->CountryCode;
                                $__dinfo['linkingdomains'] = $rsd->RefDomains;
                                $__dinfo['inboundlinks'] = $rsd->ExtBackLinks;

                                if ($do) {
                                    $_domainid = $do['id'];
                                    if ( (time()-strtotime($rsd->LastSuccessfulCrawl)<7776000) && !empty($do["alexarank"])) {
                                        unset($__dinfo['alexarank']);//cause we have another cronjob to get the alexarank
                                    }
                                    Yii::app()->db->createCommand()->update('{{domain}}', $__dinfo, 'id=:id', array(':id'=>$do['id']));
                                } else {
                                    $did = Yii::app()->db->createCommand()->insert('{{domain}}', $__dinfo);
                                    $_domainid = Yii::app()->db->getLastInsertID();
                                }

                                $cptbd = array();
                                $cptbd['competitor_id'] = $competitor_id;
                                $cptbd['discovery_id'] = $competitor['discovery_id'];
                                $cptbd['domain_id'] = $_domainid;
                                $cptbd['domain'] = $__dinfo['domain'];
                                $cptbd['hubcount'] = $rsd->MatchedLinks;
                                $cptbd['max_acrank'] = 0;
                                if ($datasource == 'historic') {
                                    $cptbd['historic_called'] = $calledtime;
                                } else {
                                    $cptbd['fresh_called'] = $calledtime;
                                }
                                //print_r($__dinfo);
                                //print_r($cptbd);

                                Yii::app()->db->createCommand()->insert('{{discovery_backdomain}}', $cptbd);
                                $backdomain_id = Yii::app()->db->getLastInsertID();

                                syncDomainToMetrics(array("id"=>$cptbd['domain_id'], "domain"=>$cptbd['domain']));
                            }
                        }

                        // Commit the transaction
                        $transaction->commit();
                        echo "Step 3: Done";
                    } catch (Exception $e) {
                        // Was there an error?
                        // Error, rollback transaction
                        print_r($e);
                        echo "Sync Top Backlinks Failure, Please Try It Again.";
                        $transaction->rollback();
                    }//end transaction

                    if ($isnewcache) file_put_contents($mjcachefile, $fstr);
                }
            } else {
                if (isset($rs->Code)) {
                    $_content = $rs->Code.":".$rs->ErrorMessage;
                } else {
                    $_content = "Sync MJSEO Top Backlinks Failure, Please contact system admin.";
                }
                Utils::notice(array('content'=>$_content));
            }

        }//end if

    }

}


//sync domain into other metrics table. such as 
function syncDomainToMetrics($r) {
    //check this domain exist in Summary or not, if not exist, then insert it into.
    $sumodel = new Summary;
    $sudomain = $sumodel->find('domain_id=:domain_id',array(':domain_id'=>$r["id"]));
    if (!$sudomain) {
        // insert new data;
        $sumodel->setIsNewRecord(true);
        $sumodel->id=NULL;
        $sumodel->domain_id=$r["id"];
        $sumodel->domain=$r["domain"];
        $sumodel->save();
    }

    //check this domain exist in Crawler or not, if not exist, then insert it into.
    $clmodel = Crawler::model()->findByAttributes(array('domain_id' => $r["id"]));
    if (!$clmodel) {
        //insert a new record into tbl.lkm_domain_craler
        $clmodel = new Crawler;
        $clmodel->setIsNewRecord(true);
        $clmodel->id=NULL;
        $clmodel->domain_id=$r["id"];
        $clmodel->domain=$r["domain"];
        $clmodel->save();
    }

    //check this domain exist in DomainOnpage or not, if not exist, then insert it into.
    $opmodel = DomainOnpage::model()->findByAttributes(array('domain_id' => $r["id"]));
    if (!$opmodel) {
        $opmodel = new DomainOnpage;
        $opmodel->setIsNewRecord(true);
        $opmodel->id=NULL;
        $opmodel->domain_id=$r["id"];
        $opmodel->domain=$r["domain"];
        $opmodel->save();
    }
    //check this domain exist or not, if not exist, then insert it into.
    //check this domain exist or not, if not exist, then insert it into.
}
?>