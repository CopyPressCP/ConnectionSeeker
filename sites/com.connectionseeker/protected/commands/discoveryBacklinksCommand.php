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

class discoveryBacklinksCommand extends CConsoleCommand {
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
            ->where("dxd.historic_called IS NULL AND dxd.status = 0 AND dd.status = 0")
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
            $mjurl = "http://enterprise.majesticseo.com/api_command?app_api_key=F4823AB4B88259A221E162F9865986E7&cmd=GetTopBackLinks&MaxSourceURLs=500&GetRootDomainData=1&AnalysisResUnits=10000&ShowDomainInfo=1&GetUrlData=0&UseResUnits=1&URL=".urlencode($cptdomain);
        } else {
            $mjurl = "http://enterprise.majesticseo.com/api_command?app_api_key=F4823AB4B88259A221E162F9865986E7&cmd=GetTopBackLinks&MaxSourceURLs=250&GetRootDomainData=1&AnalysisResUnits=10000&ShowDomainInfo=1&GetUrlData=0&UseResUnits=1&URL=".urlencode($cptdomain);
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
            $rs = simplexml_load_string($fstr);
            $datatables = $rs->DataTables->DataTable;
            $dataattr = $datatables->attributes();
            //echo $dataattr['RowsCount'];
            //print_r($datatables->attributes());
            //if ($dataattr['RowsCount'] > 0) {
            if ($datatables) {
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

                    //本来可以放在同一个foreach循环里面，但是他们返回的结果集的位置是先rootdomain的信息，然后才是domains info
                    //为了让插入数据库更高效，由于不清楚他们是否未来还会改变其返回位置的先后顺序，因此，这里做了2次foreach。
                    $dn = array();

                    $bkltableheader = array("domain","url","domain_id","googlepr","acrank","anchortext","date",
                                            "flagredirect","flagframe","flagnofollow","flagimages",
                                            "flagdeleted","flagalttext","flagmention","targeturl");

                    if ($dataattr['RowsCount'] > 0) {
                        foreach ($datatables as $row) {
                            if (strtolower($row['Name']) == 'domainsinfo') {
                                //echo $i."Here";
                                $dsheader = strtolower($row['Headers']);
                                //$dbks = str_replace("|", "`,`", $dsheader);
                                $hds = explode("|", $dsheader);
                                //print_r($hds);

                                foreach($row->Row as $r) {
                                    $vs = explode("|", $r);
                                    //print_r($vs);
                                    $dinfo = array_combine($hds, $vs);
                                    //print_r($dinfo);
                                    //old domain
                                    $do = Yii::app()->db->createCommand()->select('id')->from('{{domain}}')
                                        ->where('domain=:domain', array(':domain'=>$dinfo['domain']))
                                        ->queryRow();
                                    $idx = $dinfo['domainid'];

                                    $__dinfo = array();
                                    $__dinfo['domain'] = $dinfo['domain'];
                                    $__dinfo['rootdomain'] = $dinfo['domain'];
                                    $__dinfo['alexarank'] = $dinfo['alexarank'];
                                    $__dinfo['indexedurls'] = $dinfo['indexedurls'];
                                    $__dinfo['ip'] = $dinfo['ip'];
                                    $__dinfo['subnet'] = $dinfo['subnet'];
                                    $__dinfo['tld'] = $dinfo['tld'];
                                    $__dinfo['country'] = $dinfo['countrycode'];
                                    $__dinfo['linkingdomains'] = $dinfo['refdomains'];
                                    $__dinfo['inboundlinks'] = $dinfo['extbacklinks'];
                                    unset($dinfo);
                                    $dinfo = $__dinfo;

                                    /*
                                    unset($dinfo['domainid']);
                                    unset($dinfo['crawledurls']);
                                    unset($dinfo['firstcrawled']);
                                    unset($dinfo['lastsuccessfulcrawl']);
                                    $dinfo['country'] = $dinfo['countrycode'];
                                    unset($dinfo['countrycode']);
                                    $dinfo['linkingdomains'] = $dinfo['refdomains'];
                                    unset($dinfo['refdomains']);
                                    $dinfo['inboundlinks'] = $dinfo['extbacklinks'];
                                    unset($dinfo['extbacklinks']);
                                    */

                                    if ($do) {
                                        $dn[$idx]['id'] = $do['id'];
                                        unset($dinfo['alexarank']);//cause we have another cronjob to get the alexarank
                                        Yii::app()->db->createCommand()->update('{{domain}}', $dinfo, 'id=:id', array(':id'=>$do['id']));
                                    } else {
                                        $did = Yii::app()->db->createCommand()->insert('{{domain}}', $dinfo);
                                        $dn[$idx]['id'] = Yii::app()->db->getLastInsertID();
                                    }
                                    $dn[$idx]['domain'] = $dinfo['domain'];
                                    $dn[$idx]['hubcount'] = 0;
                                    $dn[$idx]['max_acrank'] = 0;

                                    $cptbd = array();
                                    $cptbd['competitor_id'] = $competitor_id;
                                    $cptbd['discovery_id'] = $competitor['discovery_id'];
                                    $cptbd['domain_id'] = $dn[$idx]['id'];
                                    $cptbd['domain'] = $dn[$idx]['domain'];
                                    if ($datasource == 'historic') {
                                        $cptbd['historic_called'] = $calledtime;
                                    } else {
                                        $cptbd['fresh_called'] = $calledtime;
                                    }
                                    //print_r($dinfo);
                                    //print_r($cptbd);
                                    Yii::app()->db->createCommand()->insert('{{discovery_backdomain}}', $cptbd);
                                    $dn[$idx]['__id'] = Yii::app()->db->getLastInsertID();

                                    syncDomainToMetrics(array("id"=>$cptbd['domain_id'], "domain"=>$cptbd['domain']));
                                }
                                //print_r($dn);
                            }

                        }//end foreach


                        foreach ($datatables as $row) {
                            if (strtolower($row['Name']) == 'rootdomain') {
                                $dsheader = "";
                                $dsheader = str_replace("SourceURL", "url", $row['Headers']);
                                $dsheader = str_replace("DomainID", "domain_id", $dsheader);
                                $dsheader = strtolower($dsheader);
                                //$dbks = str_replace("|", "`,`", $dsheader);
                                $hds = explode("|", $dsheader);
                                //print_r($hds);
                                $hdcount = count($hds);
                                //$acrankkey = array_search("acrank", $hds);
                                //unset($hds['domain_id']);
                                $tmphds = $hds;
                                foreach ($hds as $_hk => $_hv) {
                                    if (!in_array($_hv, $bkltableheader)) {
                                        unset($hds[$_hk]);
                                    }
                                }
                                $dbks = implode("`,`", $hds);
                                $dbks = "`competitor_id`,`discovery_id`,`domain`,`fresh_called`,`historic_called`,`".$dbks."`";

                                $total = $row['RowsCount'];
                                $i = 1;
                                $qv = "";

                                //$yiidb = Yii::app()->db;

                                $q = "INSERT INTO {{discovery_backlink}} ($dbks) VALUES ";
                                foreach ($row->Row as $r) {
                                    //###$r = Yii::app()->db->quoteValue($r);
                                    $r = str_replace("'", "&#39;", $r);
                                    $r = str_replace('"', "&#34;", $r);
                                    $r = str_replace('\\', "", $r);
                                    /*
                                    $r = str_replace("’", "&#39;", $r);
                                    $r = str_replace("‘", "&#39;", $r);
                                    $r = str_replace('“', "&#34;", $r);
                                    $r = str_replace('”', "&#34;", $r);
                                    */
                                    //###########################$vs = explode("|", $r);//the same affection as following code
                                    $vs = array();
                                    $vs = preg_split("/(?<!\|)\|(?!\|)/", $r, -1);

                                    for ($_i = 0; $_i < count($vs); $_i++) {
                                        $vs[$_i] = str_replace("||", "|", $vs[$_i]);
                                    }

                                    $vcount = count($vs);
                                    //if ($vcount > $hdcount) {
                                    if ($vcount != $hdcount) {
                                        continue;
                                        /*
                                        if (stripos("http", $vs[$vcount-2]) === false) {
                                            $vs[$vcount-3] = $vs[$vcount-3]."|".$vs[$vcount-2];
                                            unset($vs[$vcount-2]);
                                        }
                                        */
                                    }
                                    //print_r($vs);

                                    $urlinfo = array_combine($tmphds, $vs);
                                    foreach ($urlinfo as $_uk => $_uv) {
                                        if (!in_array($_uk, $bkltableheader)) {
                                            unset($urlinfo[$_uk]);
                                        }
                                    }
                                    //下面2行顺序一定不能乱，不然会出现domain无法获取的问题
                                    $intdid = $urlinfo['domain_id'];
                                    $domain = $dn[$intdid]['domain'];
                                    $urlinfo['domain_id'] = $dn[$intdid]['id'];
                                    /*
                                    $urlinfo['anchortext'] = str_replace("'", "&#39;", $urlinfo['anchortext']);
                                    $urlinfo['anchortext'] = str_replace('"', "&#34;", $urlinfo['anchortext']);
                                    */
                                    $dn[$intdid]['hubcount'] += 1;
                                    if ($dn[$intdid]['max_acrank'] < $urlinfo['acrank']) 
                                        $dn[$intdid]['max_acrank'] = $urlinfo['acrank'];

                                    //$comma = ($i > 1) ? ", " : " ";
                                    $comma = ($i % 500 == 1) ? " " : ", ";
                                    $qv .= $comma;
                                    //##$dbv = str_replace("|", "','", $r);
                                    $dbv = implode("','", $urlinfo);
                                    $qv .= "('{$competitor_id}','".$competitor['discovery_id']
                                          ."', '{$domain}','{$fresh_called}','{$historic_called}','{$dbv}')";

                                    if ($i % 500 == 0) {
                                        echo $i;
                                        //echo $q . $qv;
                                        Yii::app()->db->createCommand($q . $qv)->execute();
                                        $qv = "";
                                    }
                                    $i ++;
                                }

                                /*
                                In my experience, the max# of inserts depends on the setting of the MAX_ALLOWED_PACKET variable.
                                If you create a packet that is too large (exceeds that value) you will run into problems like you are seeing.
                                To check the current value for your server, you can do:
                                SHOW VARIABLES like 'max%';
                                Make sure when you are creating your INSERT statements that you do not exceed that value for each statement. 
                                */
                                //file_put_contents($mjcachepath . time() . ".sql", $q . $qv);
                                if (!empty($qv)) {
                                    Yii::app()->db->createCommand($q . $qv)->execute();
                                }
                            }

                        }//end foreach
                    }//END OF if ($dataattr['RowsCount'] > 0)


                    if (!empty($dn)) {
                        echo "Step 3: Update competitor_backdomain!\n";
                        foreach ($dn as $v) {
                            Yii::app()->db->createCommand()->update('{{discovery_backdomain}}',
                                                        array('hubcount' => $v['hubcount'], 'max_acrank' =>$v['max_acrank']),
                                                        'id=:id', array(':id'=>$v['__id']));
                        }
                    }

                    // Commit the transaction
                    $transaction->commit();
                    echo "Step 4: Done";
                } catch (Exception $e) {
                    // Was there an error?
                    // Error, rollback transaction
                    print_r($e);
                    echo "Sync Top Backlinks Failure, Please Try It Again.";
                    $transaction->rollback();
                }//end transaction

                if ($isnewcache) file_put_contents($mjcachefile, $fstr);

                /*
                if ($rs->GlobalVars[0]['RemainingRetrievalResUnits'] <= 200000) {
                    Utils::notice(array('content'=>"There is no more credits in your account, please charge it"));
                }
                */

            } else { // end of if ($datatables)
                if (isset($rs[0]['Code'])) {
                    $_content = $rs[0]['Code'].":".$rs[0]['ErrorMessage'];
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