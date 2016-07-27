<?php

class CampaignController extends RController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using one-column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';

	/**
	 * @return array action filters
     * uncoment out the method filters, when you wanna override the rights.filters
	 */
	public function filters()
	{
		return array(
			//'accessControl',
			'rights', // perform access control for CRUD operations
			'accessOwn + view,update,delete,processing', // perform customize additional access control for CRUD operations
		);
	}

	/**
	 * @return array action filters
     * We can build one filter file, and put this function into the filter file
	 */
    public function filterAccessOwn($filterChain) {
        $allow = true;
        $cuid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($cuid);

        if(isset($roles['Marketer'])){
            //Do some stuff first, 
            if ($_GET['id']) {
                $umodel = User::model()->findByPk($cuid);
                $model = $this->loadModel($_GET['id']);
                //###########################//
                if ($umodel->type == 0) {
                    if ($umodel->client_id == $model->client_id) {
                        $filterChain->run();
                    } else {
                        $allow = false;
                    }
                } else {
                    $cmpids = array();
                    if ($umodel->duty_campaign_ids) {
                        $cmpids = unserialize($umodel->duty_campaign_ids);
                    }
                    if ($cmpids && in_array($_GET['id'], $cmpids)) {
                        $filterChain->run();
                    } else {
                        $allow = false;
                    }
                }
                //###########################//
                /*
                if ($umodel->client_id == $model->client_id) {
                    $filterChain->run();
                } else {
                    $allow = false;
                }
                */
            } else {
                $allow = false;
            }

            if ($allow === false) {
                $filterChain->controller->accessDenied();
                return false;
            }
        } else {
            $filterChain->run();
        }
    }

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	public function actionProcessing($id)
	{
        //

		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model   = new Campaign;
        $ctmodel = new CampaignTask;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Campaign']))
		{
			$model->attributes=$_POST['Campaign'];
            $domain = trim($_POST['Campaign']['domain']);
            //if (empty($domain)) throw new CHttpException(400,'Invalid request. Please provide correct domain.');
            if ($domain) {
                Yii::import('application.vendors.*');
                $domain = SeoUtils::getSubDomain($domain);
            }

            $taskmodel=new Task;
            $notemodel=new TaskNote;

            $transaction = Yii::app()->db->beginTransaction();
            try {
                $cdmodel = new ClientDomain;
                $cdi = $cdmodel->findByAttributes(array('domain' => $domain,'client_id'=>$model->client_id));
                if (!empty($cdi)) {
                    $model->domain_id = $cdi->id;
                } else {
                    $cdmodel->domain = $domain;
                    $cdmodel->client_id = $model->client_id;
                    if ($cdmodel->save()) {
                        $model->domain_id = $cdmodel->id;
                    }
                }

                if($model->save()) {
                    $keywords = array();
                    $target_urls = array();
                    if (isset($_POST['CampaignTask'])) {
                        $totalcount = 0;
                        $kwcount = $_POST['CampaignTask']['kwcount'];
                        $_urls = $_POST['CampaignTask']['targeturl'];
                        $tiers = $_POST['CampaignTask']['tierlevel'];
                        $anchortext = $_POST['CampaignTask']['keyword'];
                        $tasknotes = $_POST['CampaignTask']['tasknote'];
                        $others = $_POST['CampaignTask']['other'];
                        $i = 0;
                        ////foreach ($_POST['CampaignTask']['keyword'] as $k => $v) {
                        foreach ($_POST['CampaignTask']['kwcount'] as $k => $v) {
                            $v = trim($v);
                            if (empty($v)) $v = $kwcount[$k] = 1;
                            if (!empty($v) && $kwcount[$k] > 0 && !empty($_urls[$k])) {
                                $totalcount += (int)$kwcount[$k];
                                $keywords[$i]['kwcount'] = (int)$kwcount[$k];
                                $keywords[$i]['keyword'] = trim($anchortext[$k]);
                                $keywords[$i]['targeturl'] = $_urls[$k];
                                $keywords[$i]['tierlevel'] = $tiers[$k];
                                $keywords[$i]['used'] = 0;
                                $keywords[$i]['tasknote'] = trim($tasknotes[$k]);
                                $keywords[$i]['other'] = trim($others[$k]);
                                $keywords[$i]['duedate'] = $model->duedate;

                                $taskids = array();
                                for ($j = 0; $j < $kwcount[$k]; $j++) {
                                    $taskmodel->setIsNewRecord(true);
                                    $taskmodel->id = NULL;
                                    $taskmodel->campaign_id = $model->id;
                                    $taskmodel->duedate = $model->duedate;
                                    $taskmodel->anchortext  = trim($anchortext[$k]);
                                    $taskmodel->targeturl   = trim($_urls[$k]);
                                    $taskmodel->tierlevel   = $tiers[$k];
                                    $taskmodel->tierlevel_built = $tiers[$k];
                                    $taskmodel->other = trim($others[$k]);
                                    if ($taskmodel->save()) {
                                        //$taskids[$taskmodel->id] = $taskmodel->id;
                                        $taskids[] = $taskmodel->id;

                                        $tknote = trim($tasknotes[$k]);
                                        if (!empty($tknote)) {
                                            $notemodel->setIsNewRecord(true);
                                            $notemodel->id = NULL;
                                            $notemodel->task_id = $taskmodel->id;
                                            $notemodel->notes = $tknote;
                                            $notemodel->save();
                                        }
                                    }
                                }

                                $keywords[$i]['taskids'] = array_values($taskids);
                                $i++;
                            }
                        }

                        // ############## we can remove the following code for the currently requirement 5/8/2012 ############//
                        //for capability, cause i worry about they will seperate it from keyword again, so i keep the following code.
                        $i = 0;
                        foreach ($_urls as $k => $v) {
                            if (!empty($v)) {
                                $target_urls[$i]['targeturl'] = $v;
                                $target_urls[$i]['used'] = 0;
                                $i++;
                            }
                        }
                        // ############### end of remove 5/8/2012 #############################//

                        //$ctmodel->setIsNewRecord(true);
                        //$ctmodel->id=NULL;
                        $ctmodel->total_count = $totalcount;
                        $ctmodel->remaining_count = $totalcount;
                        $ctmodel->campaign_id = $model->id;
                        $ctmodel->keyword = serialize($keywords);
                        $ctmodel->targeturl = serialize($target_urls);
                        $ctmodel->save();
                    }
                }

                // Commit the transaction
                $transaction->commit();
                $isredirect = true;
            } catch (Exception $e) {
                // Was there an error? if there a error, rollback transaction
                //print_r($e);
                //$rtn['success'] = false;
                //$rtn['msg'] = "Create Campaign Failure, Please Try It Again.";
                $isredirect = false;
                $model->addErrors($e);
                $transaction->rollback();
            }//end transaction
		}

        if ($model && $model->id && isset($_POST['Campaign']) && isset($_POST['Campaign']['upfile'])) {
            $this->addStyleguide(&$model);
        }

        if ($isredirect) $this->redirect(array('view','id'=>$model->id));

		$this->render('create',array(
			'model'=>$model,
			'ctmodel'=>$ctmodel,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);
        $ctmodel = new CampaignTask;
        //$model->category = unserialize($model->category);

        $cti = $ctmodel->findByAttributes(array('campaign_id' => $model->id));
        if ($cti) {
            $ctmodel = $cti;
            $oldtasks = unserialize($cti->keyword);
        }

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);


		if(isset($_POST['Campaign']))
		{
            if (!isset($_POST['Campaign']["category"])) {
                $_POST['Campaign']["category"] = "";
                $_POST['Campaign']["category_str"] = "";
            }
			$model->attributes=$_POST['Campaign'];
            $domain = trim($_POST['Campaign']['domain']);
            if ($domain) {
                Yii::import('application.vendors.*');
                $domain = SeoUtils::getSubDomain($domain);
            }

            $taskmodel=new Task;

            $transaction = Yii::app()->db->beginTransaction();
            try {
                $cdmodel = new ClientDomain;
                $cdi = $cdmodel->findByAttributes(array('domain' => $domain, 'client_id'=>$model->client_id));
                if (!empty($cdi)) {
                    $model->domain_id = $cdi->id;
                } else {
                    $cdmodel->domain = $domain;
                    $cdmodel->client_id = $model->client_id;
                    if ($cdmodel->save()) {
                        $model->domain_id = $cdmodel->id;
                    }
                }

                if($model->save()) {
                    $cmpid = $model->id;
                    $keywords = array();
                    $target_urls = array();
                    if (isset($_POST['CampaignTask'])) {
                        $totalcount = 0;
                        $kwexistcount = $_POST['CampaignTask']['kwexistcount'];
                        $kwcount = $_POST['CampaignTask']['kwcount'];
                        $_urls = $_POST['CampaignTask']['targeturl'];
                        $tiers = $_POST['CampaignTask']['tierlevel'];
                        $anchortext = $_POST['CampaignTask']['keyword'];
                        $i = 0;
                        foreach ($_POST['CampaignTask']['kwcount'] as $k => $v) {
                            $v = trim($v);
                            if (empty($v)) $v = $kwcount[$k] = 1;
                            if (!empty($v) && $kwcount[$k] > 0 && !empty($_urls[$k])) {
                                //we can use $v
                                $totalcount += (int)$kwcount[$k];
                                $keywords[$i]['kwcount'] = (int)$kwcount[$k];
                                $keywords[$i]['keyword'] = trim($anchortext[$k]);
                                $keywords[$i]['targeturl'] = trim($_urls[$k]);
                                $keywords[$i]['tierlevel'] = $tiers[$k];

                                if ($i < $kwexistcount && $cti) {
                                    // no need assign value to this one by one, we can use assign $oldtasks to it at one time.
                                    //$keywords[$i] = $oldtasks[$i];
                                    $keywords[$i]['used'] = $oldtasks[$i]['used'];
                                    $keywords[$i]['taskids'] = $oldtasks[$i]['taskids'];
                                    $i++;
                                    continue;
                                } else {
                                    $taskids = array();
                                    for ($j = 0; $j < $kwcount[$k]; $j++) {
                                        /*
                                        //check the data first then update it.
                                        // sometimes, the user may type the same anchortext & targeturl but with different case(i mean upper case & lower case) ;
                                        $cnt = $taskmodel->count("(campaign_id=:cmpid) AND (anchortext LIKE BINARY ':anchortext') AND (targeturl LIKE ':targeturl')",
                                                         array(':cmpid'=>$cmpid, ':anchortext'=>$v, ':targeturl'=>$_urls[$k]));
                                        */

                                        $taskmodel->setIsNewRecord(true);
                                        $taskmodel->id = NULL;
                                        $taskmodel->campaign_id = $model->id;
                                        $taskmodel->duedate = $model->duedate;
                                        $taskmodel->anchortext  = trim($anchortext[$k]);
                                        $taskmodel->targeturl   = trim($_urls[$k]);
                                        $taskmodel->tierlevel   = $tiers[$k];
                                        $taskmodel->tierlevel_built = $tiers[$k];
                                        $taskmodel->save();
                                        //$taskids[$taskmodel->id] = $taskmodel->id;
                                        $taskids[] = $taskmodel->id;
                                    }


                                    $keywords[$i]['duedate'] = $model->duedate;
                                    $keywords[$i]['used'] = 0;
                                    $keywords[$i]['taskids'] = array_values($taskids);
                                    $i++;
                                }
                            }
                        }

                        //update the task due date when the task wasn't send to IO 
                        Task::model()->updateAll(array('duedate' => $model->duedate),
                                                       'iostatus=0 AND campaign_id=:cid AND duedate!=:duedate',
                                                       array(':cid'=>$model->id, ':duedate'=>$model->duedate));

                        $i = 0;
                        foreach ($_urls as $k => $v) {
                            if (!empty($v)) {
                                $target_urls[$i]['targeturl'] = $v;
                                $target_urls[$i]['used'] = 0;
                                $i++;
                            }
                        }

                        if ($cti) {
                            $ctmodel->setIsNewRecord(false);
                            $ctmodel->setScenario('update');
                        } else {
                            $ctmodel->setIsNewRecord(true);
                            $ctmodel->id=NULL;
                        }

                        //$ctmodel->total_count = $totalcount;
                        $totalcount = $taskmodel->countByAttributes(array('campaign_id' => $model->id));
                        $ctmodel->total_count = $totalcount;
                        if ($totalcount > 0) {
                            $ongoingcount = $ctmodel->qa_count + $ctmodel->published_count + $ctmodel->approved_count + $ctmodel->inrepair_count;
                            $ctmodel->percentage_done = round($ctmodel->published_count / $totalcount, 3);
                            $ctmodel->internal_done = round($ongoingcount / $totalcount, 3);
                        }
                        $ctmodel->campaign_id = $model->id;
                        $ctmodel->keyword = serialize($keywords);
                        $ctmodel->targeturl = serialize($target_urls);
                        $ctmodel->save();
                    }
                }

                // Commit the transaction
                $transaction->commit();
                $isredirect = true;
            } catch (Exception $e) {
                // Was there an error? if there some errors, rollback transaction
                //print_r($e);
                $isredirect = false;
                $model->addErrors($e);
                $transaction->rollback();
            }//end transaction
		}


        if ($model && $model->id && isset($_POST['Campaign']) && isset($_POST['Campaign']['upfile'])) {
            $this->addStyleguide(&$model);
        }

        if ($isredirect) $this->redirect(array('view','id'=>$model->id));


        $ctmodel->keyword = unserialize($ctmodel->keyword);
        $ctmodel->targeturl = unserialize($ctmodel->targeturl);

		$this->render('update',array(
			'model'=>$model,
			'ctmodel'=>$ctmodel,
		));
	}

    /**
     * Updates a particular model.
     * AJAX Updately the attributes
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionSetattr($id)
    {
        $model=$this->loadModel($id);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        $rs = array('success' => true, 'msg' => Yii::t('Campaign', 'Updated was Successful.'));
        if(isset($_GET['attrname']) && isset($_GET['attrvalue']))
        {
            $attrname = str_replace("[]", "", $_GET['attrname']);
            $model->$attrname = $_GET['attrvalue'];

            if($model->save()) {
                //do nothing;
            } else {
                $rs['success'] = false;
                $rs['msg'] = Yii::t('Campaign', 'Updated was Failure.');
            }
        }

        echo CJSON::encode($rs);

        Yii::app()->end();
    }

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
        $uid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($uid);
        if (!isset($roles['Admin'])) {
            throw new CHttpException(901,'Invalid request. You have no permission delete campaigns.');
            exit ;
        }

        if (empty($id)) {
            throw new CHttpException(503,'Invalid request. Please indicate one campaign #id at least.');
            exit ;
        }

		if(Yii::app()->request->isPostRequest)
		{
            $cpmodel = CopypressCampaign::model()->findByAttributes(array('campaign_id' => $id));
            if ($cpmodel) {
                throw new CHttpException(503,'Delete campaign failure due to there are already in use in copypress.');
                exit ;
            }

            $transaction = Yii::app()->db->beginTransaction();
            try {
                $model=$this->loadModel($id);

                /*
                $model=$this->loadModel($id);
                $model->status = 0;
                $model->save();
                */

                //Delete lkm_campaign, lkm_campaign_task, lkm_inventory_building_task, lkm_domain_cart, lkm_inventory_link;
                //we can use deleteAll also;
                CampaignTask::model()->deleteAllByAttributes(array('campaign_id' => $id));

                Link::model()->deleteAllByAttributes(array('campaign_id' => $id));

                // delete the related records of lkm_inventory_building_task
                Task::model()->deleteAllByAttributes(array('campaign_id' => $id));

                if ($model->domain_id) {
                    //remove the cart
                    Cart::model()->deleteAllByAttributes(array('client_domain_id' => $model->domain_id));
                }
                IoHistoricReporting::model()->deleteAllByAttributes(array('campaign_id' => $id));

                // we only allow deletion via POST request
                //$this->loadModel($id)->delete();
                $model->delete();

                // Commit the transaction
                $transaction->commit();
                //$isredirect = true;
            } catch (Exception $e) {
                // Was there an error? if there some errors, rollback transaction
                //print_r($e);
                //$isredirect = false;
                //$model->addErrors($e);
                $transaction->rollback();

                throw new CHttpException(503,'Delete campaign failure, Please contact with system admin.');
                exit ;
            }//end transaction

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

        if (!isset($_GET["sort"])) $_GET["sort"] = "id.desc";

		$model=new Campaign('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Campaign']))
			$model->attributes=$_GET['Campaign'];

        ##############################################4/16/2012#####################################
        $roles = Yii::app()->authManager->getRoles(Yii::app()->user->id);
        if(isset($roles['Marketer'])){
            $umodel = User::model()->findByPk(Yii::app()->user->id);
            if ($umodel) {
                $model->client_id = $umodel->client_id;

                if ($umodel->type != 0) {
                    if ($umodel->duty_campaign_ids) {
                        $model->duty_campaign_ids = unserialize($umodel->duty_campaign_ids);
                        if ($model->id && !in_array($model->id, $model->duty_campaign_ids)) {
                            $model->id = 0;
                        }
                    } else {
                        //that means the client owner/admin didn't assign any campaigns to this user.
                        //so this user will see nothing
                        $model->id = 0;
                    }
                }
                /*
                if ($umodel->type != 0 && $umodel->duty_campaign_ids) {
                    $model->duty_campaign_ids = unserialize($umodel->duty_campaign_ids);
                }
                */
            } else {
                $model->client_id = 0;
            }
        }
        ##############################################4/16/2012#####################################

		$this->render('index',array(
			'model'=>$model,
		));
	}

	public function actionHidden()
	{
        if (isset($_GET['pageSize'])) {
            Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
            unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
        }

        if (!isset($_GET["sort"])) $_GET["sort"] = "id.desc";

		$model=new Campaign('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Campaign']))
			$model->attributes=$_GET['Campaign'];

        ##############################################4/16/2012#####################################
        $roles = Yii::app()->authManager->getRoles(Yii::app()->user->id);
        if(isset($roles['Marketer'])){
            $umodel = User::model()->findByPk(Yii::app()->user->id);
            if ($umodel) {
                $model->client_id = $umodel->client_id;

                if ($umodel->type != 0) {
                    if ($umodel->duty_campaign_ids) {
                        $model->duty_campaign_ids = unserialize($umodel->duty_campaign_ids);
                        if ($model->id && !in_array($model->id, $model->duty_campaign_ids)) {
                            $model->id = 0;
                        }
                    } else {
                        //that means the client owner/admin didn't assign any campaigns to this user.
                        //so this user will see nothing
                        $model->id = 0;
                    }
                }
                /*
                if ($umodel->type != 0 && $umodel->duty_campaign_ids) {
                    $model->duty_campaign_ids = unserialize($umodel->duty_campaign_ids);
                }
                */
            } else {
                $model->client_id = 0;
            }
        }
        ##############################################4/16/2012#####################################

		$this->render('hidden',array(
			'model'=>$model,
		));
	}

	/**
	 * Upload file and parse it, then store the data into the database.
	 * If it is successful, the browser will be redirected to the 'index' page.
	 */
	public function actionUpload()
	{
        ini_set("memory_limit", "512M");
        ini_set("post_max_size", "128M");//remove it from here, move it into the .htaccess
        ini_set("max_execution_time", "1200");
        ini_set("max_input_time", "1200");//remove it, move it into the .htaccess
        ini_set("upload_max_filesize", "64M");//remove it from here, move it into the .htaccess

		$model=new Campaign;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
        $rs = array();
        if(isset($_POST['Campaign'])){
            $model->attributes=$_POST['Campaign'];
            $model->upfile=CUploadedFile::getInstance($model,'upfile');
            if (empty($model->upfile)) {
                $rs[] = "Please choose one file first and upload it.";
            }
            $existcmp = Campaign::model()->findByAttributes(array('name'      => $_POST['Campaign']["name"],
                                                                  'client_id' => $_POST['Campaign']["client_id"]));
            if ($existcmp) {
                $rs[] = "The campaign name already be taken, Please use another one.";
            }

            if ($rs) $model->addErrors($rs);
        }

        if(isset($_POST['Campaign']) && empty($rs))
        {
            //print_r($_POST['Campaign']);
            //echo Yii::app()->basePath;

            $domain = trim($_POST['Campaign']['domain']);
            //if (empty($domain)) throw new CHttpException(400,'Invalid request. Please provide correct domain.');

            $transaction = Yii::app()->db->beginTransaction();
            try {
                $cdmodel = new ClientDomain;
                $cdi = $cdmodel->findByAttributes(array('domain' => $domain,'client_id'=>$model->client_id));
                if (!empty($cdi)) {
                    $model->domain_id = $cdi->id;
                } else {
                    $cdmodel->domain = $domain;
                    $cdmodel->client_id = $model->client_id;
                    if ($cdmodel->save()) {
                        $model->domain_id = $cdmodel->id;
                    }
                }

                //####$model->upfile=CUploadedFile::getInstance($model,'upfile');
                $file_ext = strtolower($model->upfile->getExtensionName());
                if (!in_array($file_ext, array("csv","xls","xlsx","ods","slk","xml"))) {
                    $rs[] = 'We are not support '.$file_ext.' for now.';
                }
                $fsrtn = $model->upfile->saveAs(Yii::app()->basePath.'/runtime/campaign/'.$model->upfile);
                $_ecode = $model->upfile->getError();
                if ($_ecode > 0) {
                    $rs[] = Utils::getUploadError($_ecode);
                }

                //###!!!! DO NOT CHANGE the following LOGIC ORDER !!!!
                //下面if的判断顺序不能乱，$model->save()一定要放到最后去！
                if($fsrtn && !$rs && $model->save()) {
                    //$campaign_id = $model->id;
                    /*
                    $_POST['Campaign']['filename'] = $model->upfile->getName();
                    $_POST['Campaign']['file_ext'] = $file_ext;
                    $_POST['Campaign']['campaign_id'] = $model->id;
                    */
                    $p = array();
                    $p['filename'] = $model->upfile->getName();
                    $p['file_ext'] = $file_ext;
                    $p['campaign_id'] = $model->id;
                    $p['duedate'] = $model->duedate;

                    switch ($file_ext) {
                        case 'xls':
                        case 'xlsx':
                        case 'ods':
                        case 'slk':
                        case 'xml':
                        case 'csv':
                            $rs = $this->__addCampaignFromFile($p);
                            break;
                        case 'txt': 
                            $rs[] = "We are not support txt file any more";
                            // do nothing right now 
                            break;
                    }
                }

                // Commit the transaction
                $transaction->commit();
                $isredirect = true;
            } catch (Exception $e) {
                // Was there an error? if there a error, rollback transaction
                //print_r($e);
                //$rtn['success'] = false;
                //$rtn['msg'] = "Create Campaign Failure, Please Try It Again.";
                $isredirect = false;
                $model->addErrors($e);
                $transaction->rollback();
            }//end transaction

            if ($rs === true) {
                $this->redirect(array('index'));
            } else {
                $model->addErrors($rs);
            }
        }

		$this->render('upload',array(
			'model'=>$model,
		));
	}

    /*
    * Parse the file, and insert the data into the table.
    */
    //public function actionTest(){
    private function __addCampaignFromFile($p){
        $errarr = array();//error array

        extract($p);
        $fpath = Yii::app()->basePath.'/runtime/campaign/'.$filename;
        //$fpath = Yii::app()->basePath.'/runtime/campaign/abck.xls';
        if (!file_exists($fpath)) {
            $errarr[] = "File not exists";
            return $errarr;
        }

        //if the file extension is csv, then we need set the auto_detect_line_endings as true;
        ini_set("auto_detect_line_endings", 1);

        //Autoload fix
        spl_autoload_unregister(array('YiiBase','autoload'));
        Yii::import('system.vendors.phpexcel.PHPExcel', true);
        $objPHPExcel = new PHPExcel();
        //IOFactory will call the right reader class base one your file ext.
        $objPHPExcel = PHPExcel_IOFactory::load($fpath);
        spl_autoload_register(array('YiiBase','autoload'));

        Yii::import('application.vendors.SeoUtils');

        $rowIterator = $objPHPExcel->getActiveSheet()->getRowIterator();

        $ctaskheader = array("Count"=>"kwcount","Anchor Text"=>"anchortext","Target URL"=>"targeturl","Tier"=>"tierlevel", "Task Note"=>"tasknote","Other"=>'other');

        $taskmodel = new Task;
        $ctmodel = new CampaignTask;
        $tiers = CampaignTask::$tier;
        $fliptiers = array_flip($tiers);

        $notemodel = new TaskNote;

        $keywords = array();
        $target_urls = array();
        $taskids = array();
        $totalcount = 0;
        $i = 0;
        //if (isset($p["campaign_id"])) {}

        $arr = array();
        $nmp = array();//the excel Column Index mapping to $header array
        foreach($rowIterator as $row){
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
            //if(1 == $row->getRowIndex()) continue;//skip first row
            $rowIndex = $row->getRowIndex();
            if (1 == $rowIndex) {
                foreach ($cellIterator as $cell) {
                    //Header mapping for excel Column Index. for example: Domain=>A, Category=>B
                    //$nmp[$cell->getColumn()] = $cell->getCalculatedValue();
                    $nmp[trim(strtolower($cell->getCalculatedValue()))] = $cell->getColumn();
                }
            } else {
                foreach ($cellIterator as $cell) {
                    $arr[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
                }
                //save data into database.

                //save data into tbl.domain & tbl.inventory,
                $p = array();
                foreach ($ctaskheader as $hk => $hv) {
                    $hk = strtolower($hk);
                    if (!isset($nmp[$hk])) {
                        continue;
                    }

                    $pv = trim($arr[$rowIndex][$nmp[$hk]]);
                    if ($hv == 'tierlevel') {
                        $pv = $fliptiers[$pv];
                    } elseif ($hv == 'kwcount') {
                        if (empty($pv)) $pv = 1;
                    }

                    if (empty($pv)) $pv = "";

                    $p[$hv] = $pv;
                }
                if ($p && !empty($p["targeturl"])) {
                    $kwcount = $p['kwcount'];
                    $anchortext = trim($p["anchortext"]);
                    $targeturl = trim($p["targeturl"]);
                    $tasknote = trim($p["tasknote"]);
                    $other = trim($p["other"]);

                    $totalcount += (int)$kwcount;
                    $keywords[$i]['kwcount'] = (int)$kwcount;
                    $keywords[$i]['keyword'] = $anchortext;
                    $keywords[$i]['targeturl'] = $targeturl;
                    $keywords[$i]['tierlevel'] = $p["tierlevel"];
                    $keywords[$i]['used'] = 0;
                    $keywords[$i]['other'] = $other;
                    $keywords[$i]['tasknote'] = $tasknote;
                    $keywords[$i]['duedate'] = $p["duedate"];

                    $target_urls[$i]['targeturl'] = $v;
                    $target_urls[$i]['used'] = 0;

                    for ($j = 0; $j < $kwcount; $j++) {
                        $taskmodel->setIsNewRecord(true);
                        $taskmodel->id = NULL;
                        $taskmodel->campaign_id = $campaign_id;
                        $taskmodel->anchortext  = $anchortext;
                        $taskmodel->targeturl   = $targeturl;
                        $taskmodel->tierlevel   = $p["tierlevel"];
                        $taskmodel->tierlevel_built = $p["tierlevel"];
                        $taskmodel->duedate = $p["duedate"];
                        $taskmodel->other = $other;
                        //$taskmodel->save();
                        ////$taskids[$taskmodel->id] = $taskmodel->id;
                        //$taskids[] = $taskmodel->id;
                        if ($taskmodel->save()) {
                            $taskids[] = $taskmodel->id;
                            if (!empty($tasknote)) {
                                $notemodel->setIsNewRecord(true);
                                $notemodel->id = NULL;
                                $notemodel->task_id = $taskmodel->id;
                                $notemodel->notes = $tasknote;
                                $notemodel->save();
                            }
                        }
                    }

                    $keywords[$i]['taskids'] = array_values($taskids);
                    $i++;

                }//end of if
            }
        }

        //INSERT A CampaignTask Record Here.
        $ctmodel->setIsNewRecord(true);
        $ctmodel->id=NULL;
        $ctmodel->total_count = $totalcount;
        $ctmodel->remaining_count = $totalcount;
        $ctmodel->campaign_id = $campaign_id;
        $ctmodel->keyword = serialize($keywords);
        $ctmodel->targeturl = serialize($target_urls);
        $ctmodel->save();

        ############################################################33
        if ($errarr) {
            return $errarr;
        } else {
            return true;
        }

        Yii::app()->end();
    }

    function addStyleguide($model){
        if (!$model) {
            return "";
        }
        ini_set("memory_limit", "128M");
        ini_set("post_max_size", "64M");//remove it from here, move it into the .htaccess
        ini_set("max_execution_time", "1200");
        ini_set("max_input_time", "1200");//remove it, move it into the .htaccess
        ini_set("upload_max_filesize", "64M");//remove it from here, move it into the .htaccess
        $model->upfile = $_POST["Campaign"]["upfile"];
        $model->upfile=CUploadedFile::getInstance($model,'upfile');
        if (!$model->upfile) {
            return ;
        }

        $file_ext = strtolower($model->upfile->getExtensionName());
        //$styleguide->getName();
        if (!in_array($file_ext, array("pdf"))) {
            return 'We do not support '.$file_ext.', Support PDF file only for now.';
        }

        define('DS', DIRECTORY_SEPARATOR);
        $fsrtn = $model->upfile->saveAs(dirname(dirname(dirname(__FILE__))).DS."assets".DS."styleguide".DS.$model->id.".pdf");
        $_ecode = $model->upfile->getError();
        if ($_ecode > 0) {
            return Utils::getUploadError($_ecode);
        } else {
            $model->styleguide = $model->id . ".pdf";
            $model->save();
        }
    }


	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Campaign::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='campaign-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
