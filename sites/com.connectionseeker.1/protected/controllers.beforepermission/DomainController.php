<?php

class DomainController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using one-column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view','audit','topbacklinks','outreach'),
				//'users'=>array('*'),
				'users'=>array('@'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','setattr','note'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
        if (Yii::app()->request->getQuery("ajax") == true) {
            $rs = array();
            $rs = Yii::app()->db->createCommand()->select()->from('{{domain}}')
                    ->where('id=:id', array(':id'=>$id))
                    ->queryRow();
            $rs['creation'] = "-1";
            if ($rs['onlinesince'] == "-1" || $rs['onlinesince'] == "658454400") 
                $rs['creation'] = date("Y-m-d", $rs['onlinesince']);

            echo CJSON::encode($rs);
            Yii::app()->end();
        } else {
            $this->render('view',array(
                'model'=>$this->loadModel($id),
            ));
        }
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Domain;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Domain']))
		{
			$model->attributes=$_POST['Domain'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	public function actionAudit()
	{
        set_time_limit(3600);
        ini_set("memory_limit", "128M");
        $ctx = stream_context_create(array(
           'http' => array(
               'timeout' => 3600
               )
           )
        );

		$model=new Domain;
        $auditmodel = new Audit;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Domain']['domain'])) {
            $rtn = array();

            $calledtime = time();
            if ($_GET['Domain']['use_historic_index']) {
                $datasource = 'historic';
                $fresh_called = 0;
                $historic_called = $calledtime;
            } else {
                $datasource = 'fresh';
                $fresh_called = $calledtime;
                $historic_called = 0;
            }

			//$model->attributes=$_GET['Domain'];
            $_domain = $_GET['Domain']['domain'];
            $_domain = str_replace(" ", "", $_domain);
            if (strlen($_domain) <= 3) 
                throw new CHttpException(400,'Invalid request. Please type the correct domains.');
            list($rd, $cpt) = explode("|", $_domain);
            $ds = array();
            if (isset($rd)) {
                $dms = explode(",", $rd);
            }
            if (isset($cpt)) {
                $cpts = explode(",", $cpt);
            }
            $ds = explode(",", str_replace("|", ",", $_domain));

            if ($ds) {
                /*
                $domainstr = implode(",", $ds);
                $mjurl = "http://enterprise.majesticseo.com/api_command?app_api_key=F4823AB4B88259A221E162F9865986E7&cmd=GetDomainBackLinksHistory&Domains=".rawurlencode($domainstr);

                $items = count($ds);
                $mjurl = "http://www.majesticseo.com/api_command.php?app_api_key=F4823AB4B88259A221E162F9865986E7&cmd=GetIndexItemInfo&datasource={$datasource}&items={$items}";
                foreach($ds as $k => $v) {
                    $mjurl .= "&item{$k}=".urlencode($v);
                }
                */

                foreach($ds as $k => $v) {
                    //we need valid the domain format before we call mjseo api.
                    //app_api_key=2F2DE59CC1A7DC7D88149BB6D525FC8C For BlueGlass
                    $mjurl = "http://enterprise.majesticseo.com/api_command?app_api_key=F4823AB4B88259A221E162F9865986E7&cmd=GetDomainBackLinksHistory&Domains=".rawurlencode($v);

                    $callapi = false;
                    $daysoffset = 86400 * 90;//90 days

                    $ai = $auditmodel->findByAttributes(array('domain' => $v));

                    if (!empty($ai)) {
                        $fstr = $ai->backlinkshistory;
                        if ($datasource == 'historic') {
                            if (empty($ai->historic_called) || $ai->historic_called + $daysoffset < time()) {
                                $callapi = true;
                            }
                        } else {
                            if (empty($ai->fresh_called) || $ai->fresh_called + $daysoffset < time()) {
                                $callapi = true;
                            }
                        }

                    } else {
                        //echo $mjurl;
                        //$fstr = file_get_contents($mjurl);
                        //$fstr = file_get_contents("http://sites.com/sites/com.connectionseeker/audit2.txt");
                        //file_put_contents("K:/NewHtdocs/yii/yii1.1.8.dev/sites/com.connectionseeker/audit.txt", $fstr);
                        $callapi = true;
                    }
                    if ($callapi) {
                        $fstr = file_get_contents($mjurl, 0, $ctx);
                        ////die("logic error");
                    }

                    if ($fstr) {
                        //$calledtime = time();//this is version control,we need lock this competitor domain
                        $rs = simplexml_load_string($fstr);
                        $datatables = $rs->DataTables->DataTable;
                        if ($datatables) {
                            //print_r($datatables);
                            foreach ($datatables as $row) {
                                //$dsheader = strtolower($row['Headers']);
                                //$hds = explode("|", $dsheader);
                                //print_r($hds);
                                if (strtolower($row['Name']) == 'domaincharttable_'.$v) {
                                    //$dsheader = strtolower($row['Headers']);
                                    $dsheader = $row['Headers'];
                                    $hds = explode("|", $dsheader);
                                    foreach($row->Row as $r) {
                                        $vs = explode("|", $r);
                                        if (strtolower($vs[0]) == 'totallinks') {
                                            $history = array_combine($hds, $vs);
                                            $totalhis = 0;
                                            for ($i=1; $i<=12; $i++) {
                                                if ($i == 1) {
                                                    $starttime = strtotime("-{$i} month");
                                                } else {
                                                    $starttime = strtotime("-{$i} months");
                                                }
                                                $startmonth = date("M Y", $starttime);
                                                $totalhis += $history[$startmonth];
                                            }
                                            $rtn[$v]['avghis'] = round(($totalhis/12), 3);
                                            break;
                                        }
                                    }
                                }
                                if (strtolower($row['Name']) == 'domaininfo') {
                                    foreach($row->Row as $r) {
                                        $vs = explode("|", $r);
                                        //print_r($vs);
                                        $rtn[$v]['extbacklinks'] = $vs[6];
                                        $rtn[$v]['refdomains'] = $vs[7];
                                        $rtn[$v]['indexedurls'] = $vs[3] + $vs[4];
                                        $rtn[$v]['datasource'] = $datasource;

                                        if (isset($dms) && in_array($v, $dms)) {
                                            $rtn[$v]['category'] = "Domain";
                                        } else {
                                            $rtn[$v]['category'] = "Competitor";
                                        }

                                        $di = $model->findByAttributes(array('domain' => $v));

                                        if (!empty($di)) {
                                            $di->setIsNewRecord(false);
                                            $di->setScenario('update');
                                            $di->modified = date('Y-m-d H:i:s');
                                            $di->modified_by = Yii::app()->user->id;
                                        } else {
                                            $di = $model;
                                            $di->setIsNewRecord(true);
                                            $di->id=NULL;
                                            $di->domain=$v;
                                            $di->created = date('Y-m-d H:i:s');
                                            $di->created_by = Yii::app()->user->id;
                                        }

                                        $di->linkingdomains=$rtn[$v]['refdomains'];
                                        $di->inboundlinks=$rtn[$v]['extbacklinks'];
                                        $di->indexedurls=$rtn[$v]['indexedurls'];
                                        $di->tld = substr(strrchr($v, "."), 1);
                                        $di->save();

                                    }//end of foreach $row->Row
                                }

                            }
                        }

                        if ($rs->GlobalVars[0]['RemainingRetrievalResUnits'] <= 200000) {
                            Utils::notice(array('content'=>"There is no more credits in your account, please charge it"));
                        }
                    }

                    if ($callapi) {
                        $profile = array();
                        if (!empty($ai)) {
                            $ai->setIsNewRecord(false);
                            $ai->setScenario('update');
                            if(!empty($ai->profile)) $profile = CJSON::decode($ai->profile, true);
                        } else {
                            $ai = $auditmodel;
                            $ai->setIsNewRecord(true);
                            $ai->id=NULL;
                            $ai->domain=$v;
                            $ai->domain_id=$di->id;
                        }
                        $profile[$datasource]['refdomains'] = $rtn[$v]['refdomains'];
                        $profile[$datasource]['extbacklinks'] = $rtn[$v]['extbacklinks'];
                        $profile[$datasource]['indexedurls'] = $rtn[$v]['indexedurls'];
                        $profile[$datasource]['avghis'] = $rtn[$v]['avghis'];
                        //$profile[$datasource]['calledtime'] = $calledtime;//no need record this info here,
                        $ai->historic_called = $historic_called;
                        $ai->fresh_called = $fresh_called;
                        $ai->backlinkshistory = $fstr;
                        $ai->profile = CJSON::encode($profile);//json_encode
                        $ai->save();
                        //print_r($profile);
                    }
                }

            }

            //Do NOT Put this line at the top of this function.cause it will be overwrite by $model->save(); 
            $model->attributes=$_GET['Domain'];
            //print_r($ds);
        }//end if isset($_GET['Domain']['domain'])

		$this->render('audit',array(
			'model'=>$model,
			'result'=>$rtn,
		));
	}

	public function actionTopbacklinks()
	{
        set_time_limit(0);
        ini_set("memory_limit", "512M");
        $ctx = stream_context_create(array(  
           'http' => array(  
               'timeout' => 3600
               )  
           )  
        );

        $rtn = array();
        $rtn['success'] = true;
        $rtn['msg'] = $_GET['domain'];
        $datasource = 'fresh';
        if (isset($_GET['domain'])) {
            $_domain = $_GET['domain'];
            $rtn['audit']['domain'] = $_domain;

            $callapi = false;
            $daysoffset = 86400 * 90;//90 days

            //update the table.competitor's last call api time
            $cptmodel = new Competitor;
            $cpti = $cptmodel->findByAttributes(array('domain' => $_domain));
            if ($_GET['datasource']) {
                if (empty($cpti->historic_called) || $cpti->historic_called + $daysoffset < time()) {
                    $callapi = true;
                } else {
                    $calledtime = $cpti->historic_called;
                }
                $datasource = 'historic';
            } else {
                if (empty($cpti->fresh_called) || $cpti->fresh_called + $daysoffset < time()) {
                    $callapi = true;
                } else {
                    $calledtime = $cpti->fresh_called;
                }
                $datasource = 'fresh';
            }

            $mjurl = "http://enterprise.majesticseo.com/api_command.php?app_api_key=F4823AB4B88259A221E162F9865986E7&cmd=GetTopBackLinks&MaxSourceURLs=2000&GetRootDomainData=1&AnalysisResUnits=10000&ShowDomainInfo=1&GetUrlData=0&UseResUnits=1&datasource={$datasource}&URL=".urlencode($_domain);
            if ($callapi) {
                //echo $mjurl;
                $fstr = file_get_contents($mjurl, 0, $ctx);
            }

            if (!empty($fstr)) {
                $calledtime = time();//this is version control,we need lock this competitor domain
                $rs = simplexml_load_string($fstr);
                $datatables = $rs->DataTables->DataTable;
                if ($datatables) {
                    //##### Transaction Start ######//
                    $transaction = Yii::app()->db->beginTransaction();
                    try {
                        if ($datasource == 'historic') {
                            $fresh_called = 0;
                            $historic_called = $calledtime;
                        } else {
                            $fresh_called = $calledtime;
                            $historic_called = 0;
                        }

                        if (!empty($cpti)) {
                            $cpti->setIsNewRecord(false);
                            $cpti->setScenario('update');
                            $cpti->modified = date('Y-m-d H:i:s');
                            $cpti->modified_by = Yii::app()->user->id;
                        } else {
                            $cpti = $cptmodel;
                            $cpti->setIsNewRecord(true);
                            $cpti->id=NULL;
                            $cpti->domain=$_domain;
                            $cpti->created = date('Y-m-d H:i:s');
                            $cpti->created_by = Yii::app()->user->id;
                        }
                        $cpti->historic_called = $historic_called;
                        $cpti->fresh_called = $fresh_called;
                        $cpti->save();
                        $competitor_id = $cpti->id;


                        //本来可以放在同一个foreach循环里面，但是他们返回的结果集的位置是先rootdomain的信息，然后才是domains info
                        //为了让插入数据库更高效，由于不清楚他们是否未来还会改变其返回位置的先后顺序，因此，这里做了2次foreach。
                        $dn = array();

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
                                    $cptbd['domain_id'] = $dn[$idx]['id'];
                                    if ($datasource == 'historic') {
                                        $cptbd['historic_called'] = $calledtime;
                                    } else {
                                        $cptbd['fresh_called'] = $calledtime;
                                    }
                                    //print_r($dinfo);
                                    //print_r($cptbd);
                                    Yii::app()->db->createCommand()->insert('{{competitor_backdomain}}', $cptbd);
                                    $dn[$idx]['__id'] = Yii::app()->db->getLastInsertID();
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
                                $dbks = implode("`,`", $hds);
                                $dbks = "`competitor_id`,`domain`,`fresh_called`,`historic_called`,`".$dbks."`";

                                $total = $row['RowsCount'];
                                $i = 1;
                                $qv = "";

                                //$yiidb = Yii::app()->db;

                                $q = "INSERT INTO {{competitor_backlink}} ($dbks) VALUES ";
                                foreach ($row->Row as $r) {
                                    //###$r = Yii::app()->db->quoteValue($r);
                                    $r = str_replace("'", "&#39;", $r);
                                    $r = str_replace('"', "&#34;", $r);
                                    $vs = explode("|", $r);
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

                                    $urlinfo = array_combine($hds, $vs);
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

                                    $comma = ($i > 1) ? ", " : " ";
                                    $qv .= $comma;
                                    //##$dbv = str_replace("|", "','", $r);
                                    $dbv = implode("','", $urlinfo);
                                    $qv .= "('{$competitor_id}','{$domain}','{$fresh_called}','{$historic_called}','{$dbv}')";
                                    $i ++;
                                }

                                //echo $q . $qv;
                                Yii::app()->db->createCommand($q . $qv)->execute();
                            }

                        }//end foreach


                        if (!empty($dn)) {
                            foreach ($dn as $v) {
                                Yii::app()->db->createCommand()->update('{{competitor_backdomain}}',
                                                            array('hubcount' => $v['hubcount'], 'max_acrank' =>$v['max_acrank']),
                                                            'id=:id', array(':id'=>$v['__id']));
                            }
                        }

                        // Commit the transaction
                        $transaction->commit();
                    } catch (Exception $e) {
                        // Was there an error?
                        // Error, rollback transaction
                        //print_r($e);
                        $rtn['success'] = false;
                        $rtn['msg'] = "Get Top Backlinks Failure, Please Try It Again.";
                        $transaction->rollback();
                    }//end transaction

                    if ($rs->GlobalVars[0]['RemainingRetrievalResUnits'] <= 200000) {
                        Utils::notice(array('content'=>"There is no more credits in your account, please charge it"));
                    }
                } else { // end of if ($datatables)
                    $rtn['success'] = false;
                    $rtn['msg'] = $rs[0]['Code'].":".$rs[0]['ErrorMessage'];
                    Utils::notice(array('content'=>$rtn['msg']));
                }
            }//if ($fstr) {

            /*
            if (!$callapi) {
            }
            */

            $cpti = $cptmodel->findByAttributes(array('domain' => $_domain));
            if (!empty($cpti)) {
                //$yiicmd = Yii::app()->db->createCommand();
                //var_dump($yiicmd);
                if ($datasource == 'historic') {
                    $w = "historic_called = '".$cpti->historic_called."'";
                } else {
                    $w = "fresh_called = '".$cpti->fresh_called."'";
                }
                $competitor_id = $cpti->id;

                $ac = Yii::app()->db->createCommand()->select('COUNT(*) AS count')->from('{{competitor_backlink}}')->where(
                        array('and', "competitor_id = {$competitor_id}", $w)
                        )->queryRow();
                $rtn['audit']['actotal'] = $ac['count'];

                $acmax = Yii::app()->db->createCommand()->select('MAX(acrank) AS max')->from('{{competitor_backlink}}')->where(
                        array('and', "competitor_id = {$competitor_id}", $w)
                        )->queryRow();
                //var_dump($acmax);
                $rtn['audit']['acmax'] = $acmax['max'];

                $ac1 = Yii::app()->db->createCommand()->select('COUNT(*) AS count')->from('{{competitor_backlink}}')->where(
                        array('and',
                            "competitor_id = {$competitor_id}", $w, 'acrank >= 1'
                        ))->queryRow();
                $rtn['audit']['ac1'] = $ac1['count'];

                $ac1to4 = Yii::app()->db->createCommand()->select('COUNT(*) AS count')->from('{{competitor_backlink}}')->where(
                        array('and',
                            "competitor_id = {$competitor_id}", $w, 'acrank >= 1', 'acrank <= 4'
                        ))->queryRow();
                $rtn['audit']['ac1to4'] = $ac1to4['count'];

                $ac5 = Yii::app()->db->createCommand()->select('COUNT(*) AS count')->from('{{competitor_backlink}}')->where(
                        array('and',
                            "competitor_id = {$competitor_id}", $w, 'acrank >= 5'
                        ))->queryRow();
                $rtn['audit']['ac5'] = $ac5['count'];

                $acavg = Yii::app()->db->createCommand()->select('AVG(acrank) AS avg')->from('{{competitor_backlink}}')->where(
                        array('and',
                            "competitor_id = {$competitor_id}", $w, 'acrank >= 1'
                        ))->queryRow();
                $rtn['audit']['acavg'] = $acavg['avg'];

                $rtn['audit']['quality'] = ($rtn['audit']['actotal']) ? ($rtn['audit']['ac5']/$rtn['audit']['actotal']) : 0;
                $rtn['audit']['quality'] = $rtn['audit']['quality'] * 100;
                $rtn['audit']['quality'] = round($rtn['audit']['quality'], 3) . "%";

                /*
                SELECT count( * ) AS count, anchortext
                FROM lkm_competitor_backlink
                GROUP BY anchortext
                ORDER BY count DESC
                LIMIT 0 , 30
                */
                $toptext = Yii::app()->db->createCommand()->select('COUNT(*) AS count, anchortext')
                                                      ->from('{{competitor_backlink}}')->where(
                        array('and',
                            "competitor_id = {$competitor_id}", $w
                        ))->group("anchortext")->order("count DESC")->limit(10)->queryAll();

                $rtn['audit']['toptext'] = "";
                if ($toptext) {
                    foreach ($toptext as $tk => $tt) {
                        $tpt = ($tt['count'] * 100) / $rtn['audit']['actotal'];
                        $tpt = round($tpt, 3);
                        $rtn['audit']['toptext'] .= $tt['anchortext']."(".$tt['count'].", {$tpt}%)<br />";
                    }
                }
                //$rtn['audit']['toptext'] = $toptext['count'];

                ///////////////////////////////////////////////////////////////
                if ($callapi) {
                    $profile = array();
                    $auditmodel = new Audit;
                    $ai = $auditmodel->findByAttributes(array('domain' => $_domain));
                    if (!empty($ai)) {
                        $ai->setIsNewRecord(false);
                        $ai->setScenario('update');
                        if(!empty($ai->profile)) $profile = CJSON::decode($ai->profile, true);
                    } else {
                        $ai = $auditmodel;
                        $ai->setIsNewRecord(true);
                        $ai->id=NULL;
                        $ai->domain=$v;
                        $ai->domain_id=$di->id;
                    }
                    $profile[$datasource] += $rtn['audit'];
                    $profile[$datasource]['calledtime'] = $calledtime;
                    $ai->profile = CJSON::encode($profile);//json_encode
                    $ai->save();
                }
                ///////////////////////////////////////////////////////////////
            }
        }

        echo CJSON::encode($rtn);
        Yii::app()->end();
    }

    private function _acrank($select, $from, $where){
        //do nothing for now;
    }

	/**
	 * Updates a particular model.
     * AJAX Updately the attributes
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionSetattr($id)
	{
        if ($id > 0) {
		    $model=$this->loadModel($id);
        } else {
            $bdid = $_GET['bdid'];
            $discovery = Discovery::model()->findByPk($bdid);
		    $model=$this->loadModel($discovery->domain_id);
        }

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

        $rs = array('success' => true, 'msg' => Yii::t('Domain', 'Updated was Successful.'));

		if(isset($_GET['attrname']) && isset($_GET['attrvalue']))
		{
            if (stripos($_GET['attrname'], "stype") !== false) {
			    $model->stype = $_GET['attrvalue'];
            } elseif (stripos($_GET['attrname'], "otype") !== false) {
			    $model->otype = $_GET['attrvalue'];
            } elseif (stripos($_GET['attrname'], "status") !== false) {
			    $model->touched_status = $_GET['attrvalue'];
            }

			if($model->save()) {
                //do nothing;
            } else {
                $rs['success'] = false;
                $rs['msg'] = Yii::t('Domain', 'Updated was Failure.');
            }
		}

        echo CJSON::encode($rs);

        Yii::app()->end();
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Domain']))
		{
			$model->attributes=$_POST['Domain'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
            // we couldn't delete any domain, this is very important
			//$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Manages all models.
	 */
	public function actionIndex()
	{
        if (isset($_GET['pageSize'])) {
            Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
            unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
        }

		$model=new Domain('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Domain']))
			$model->attributes=$_GET['Domain'];

		$this->render('index',array(
			'model'=>$model,
		));
	}

	/**
	 * Manages all Outreach domains. We seperate it from the Discovery page
	 */
	public function actionOutreach()
	{
        if (isset($_GET['pageSize'])) {
            Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
            unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
        }

        if (isset($_GET['client_id']) && $_GET['client_id']) {
            $_firstone = ClientDomain::model()->find("client_id=:client_id AND status=1",
                                                  array(':client_id'=>$_GET['client_id']));
            if ($_firstone) {
                $_GET['client_domain_id'] = $_firstone->id;
            }
        }

        if ( (empty($_GET['client_domain_id']) && empty($_GET['competitor_id']))
          || (!empty($_GET['client_domain_id']) && !isset($_GET['competitor_id'])) ) {
            if (!empty($_GET['client_domain_id'])) {
                $w = array("AND", "domain_id=:cdcid",
                                  array("OR", "cdc.fresh_called > 0", "cdc.historic_called > 0"));
            } else {
                $w = array("OR", "cdc.fresh_called > 0", "cdc.historic_called > 0");
            }
            //we can put the lasted one competitor as the default search parameters when we didn't;
            $lastcpt = Yii::app()->db->createCommand()
                ->select("cdc.domain_id, cdc.competitor_id, cdc.fresh_called, cdc.historic_called")
                ->from('{{client_domain_competitor}} cdc')
                ->join('{{client_domain}} cd', '(cd.id=cdc.domain_id AND cd.status=1)')
                ->join('{{client}} c', '(c.id=cd.client_id AND c.status=1)')
                ->where($w, array(':cdcid'=>$_GET['client_domain_id']))->order('cdc.id DESC')->limit(1)
                ->queryRow();
            if ($lastcpt) {
                $cmpids = $lastcpt['competitor_id'];
                if ($lastcpt['fresh_called'] > 0) {
                    $_GET['fresh_called'] = $lastcpt['fresh_called'];
                } else {
                    $_GET['historic_called'] = $lastcpt['historic_called'];
                }
                $_GET['client_domain_id'] = $lastcpt['domain_id'];
                $_GET['competitor_id'] = $lastcpt['competitor_id'];
            }
        }

		$model=new Discovery('search');

		//$model=new Domain('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Domain']))
			$model->attributes=$_GET['Domain'];

		$this->render('index',array(
			'model'=>$model,
		));
	}

	public function actionNote($domain_id)
	{
		$model = new Note;

		if(isset($_POST['Note']))
		{
			$model->attributes=$_POST['Note'];
			if($model->save()) {
				// $this->redirect(array('view','id'=>$model->id));
                /*$this->render('note',array(
                    'model'=>$model));*/
                $this->renderPartial('_note', array('model'=>$model)); 
                Yii::app()->end();
            }
            $domain_id = $_POST['Note']['domain_id'];
		} else if ($domain_id > 0) {
            $model->domain_id = $domain_id;
        }
        $data = $model->with('rcreatedby')->findAll('domain_id=' . $model->domain_id);
        $model->attributes = null;
		$this->renderPartial('note',array(
			'model'=>$model,
            'notes' => $data
		));
        Yii::app()->end();
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Domain::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='domain-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
