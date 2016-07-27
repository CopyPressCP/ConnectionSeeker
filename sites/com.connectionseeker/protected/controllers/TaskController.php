<?php

class TaskController extends RController
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
        //We can comment out the Array.accessControl & the method accessRules() when we turn rights on.
		return array(
			//'accessControl', // perform access control for CRUD operations
			'rights', // perform access control for CRUD operations
			//'accessOwn + view,update,delete', // perform customize additional access control for CRUD operations
		);
	}


    /*
    * trigger error, when the system catch an error or exception,
    */
    public function actionError()
    {
        if($error=Yii::app()->errorHandler->error)
            $this->render('error', $error);
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

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Task;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Task']))
		{
			$model->attributes=$_POST['Task'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}


    /*
    * This method was discarded, we never use this method anymore
    */
	public function actionAdd()
	{
		$model=new Task;

        $styleguide = Utils::preference("styleguide");
        if ($styleguide === false) {
            throw new CHttpException(401,'Preferences File Is Invalid, Please contact system admin.');
            /*
            $rs['msg'] = "Preferences File Is Invalid, Please contact system admin.";
            $rs['success'] = false;
            echo CJSON::encode($rs);
            Yii::app()->end();
            */
        }

	    // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
        //$created = time();
        //$month = date('Ym', $created);

        $_kws = array();
        $_urls = array();
        if(isset($_POST['Task']))
        {
            if (isset($_POST['Task']['duedate'])) {
                $_POST['Task']['duedate'] = strtotime(str_replace("/", "-", $_POST['Task']['duedate']));
                $_POST['Task']['duedate'] = date("Y-m-d H:i:s", $_POST['Task']['duedate']);
            }

            $model->attributes=$_POST['Task'];

            if ($model->campaign_id) {
                $cmptask = CampaignTask::model()->with("rcampaign")->findByAttributes(array('campaign_id' => $model->campaign_id));
                if ($cmptask) {
                    $keywords = unserialize($cmptask->keyword);
                    $targeturls = unserialize($cmptask->targeturl);//capability code, acctually, we can remove it now.
                    if ($keywords) {
                        foreach($keywords as $k=> $v) {
                            //$_kws[$v['keyword']] = $v['kwcount']." - ".$v['keyword'];
                            $_kws[$k] = $v['kwcount']." - ".$v['keyword'];
                            if (isset($v['targeturl'])) {
                                $_urls[$k] = $v['targeturl'];
                            } else {
                                $_urls[$k] = $targeturls[$k]['targeturl'];
                            }
                        }
                    }
                }
            }

            if ($model->inventory_ids) 
                $ivtids = explode(",", $model->inventory_ids);
            else 
                throw new CHttpException(404, 'Please choose inventory domain first.');

            $ivtmodel = Inventory::model()->with("rdomain")->findAllByPK($ivtids);

            if (isset($_POST['Task']['assignee']) && $cmptask) {
                if ($model->tasktype == 1) {
                    $attrs = array("anchortext","targeturl","title","optional_keywords","mapping_id","notes");
                } else {
                    $attrs = array("anchortext","targeturl");
                }

                $transaction = Yii::app()->db->beginTransaction();
                //start transation
                try {
                    $p = array();
                    foreach ($ivtids as $i => $v) {
                        $p["inventory_id"] = $_POST["inventory_id"][$i];
                        $p["domain_id"] = $ivtmodel[$i]->domain_id;
                        $p["domain"] = $ivtmodel[$i]->domain;
                        ////$pk = $i + 1;
                        $pk = $v;
                        $_count = count($_POST["anchortext".$pk]);
                        for($_i = 0; $_i < $_count; $_i++) {
                            $model->setIsNewRecord(true);
                            $model->id=NULL;

                            foreach($attrs as $av){
                                if ($av == "optional_keywords") {
                                    $a = $_POST[$av.$pk];
                                    $optionalkws = array('optlkw1' => $a["optionalkw1"][$_i],
                                                         'optlkw2' => $a["optionalkw2"][$_i],
                                                         'optlkw3' => $a["optionalkw3"][$_i],
                                                         'optlkw4' => $a["optionalkw4"][$_i]);
                                    $p['optional_keywords'] = serialize($optionalkws);
                                } elseif ($av == "targeturl") {
                                    if (is_numeric($_POST[$av.$pk][$_i])) {
                                        //this one for Sponsored Posts & Guest Blogging~
                                        $p[$av] = $_urls[$_POST[$av.$pk][$_i]];
                                    } else {
                                        $p[$av] = $_POST[$av.$pk][$_i];
                                    }
                                    //$p[$av] = $_urls[$_POST[$av.$pk][$_i]];
                                } elseif ($av == "anchortext") {
                                    $p[$av] = $keywords[$_POST[$av.$pk][$_i]]['keyword'];
                                } else {
                                    $p[$av] = $_POST[$av.$pk][$_i];
                                }
                            }

                            $model->attributes = $p;
                            //print_r($model->attributes);
                            if($model->save()){
                                //do nothing for now;
                            } else {
                                //print_r($model->attributes);
                                //die();
                            }
                        }
                    }

                    // Commit the transaction
                    $transaction->commit();

                    $this->redirect(array('task/index','client_id'=>$model->client_id, 'campaign_id'=>$model->campaign_id,
                               'content_category_id'=>$model->content_category_id, 'tasktype'=>$model->tasktype));
                } catch (Exception $e) {
                    // Was there an error?
                    // Error, rollback transaction
                    //print_r($e);
                    $model->addErrors($e);
                    $transaction->rollback();
                }//end transaction
            }
        }

        $styleguide = CHtml::decode($styleguide);
        $this->render('add',array(
            'model'=>$model,
            '_kws'=>$_kws,
            '_urls'=>$_urls,
            'styleguide'=>$styleguide,
        ));
    }

    /**
     * Send link building task to copypress.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionSend()
    {
        $ids = $_REQUEST['ids'];
        if (!is_array($ids)) {
            $ids = array($ids);
        }

        if (empty($ids)) {
            $rs['msg'] = "Please provide at least one link building task";
            $rs['success'] = false;
            echo CJSON::encode($rs);
            Yii::app()->end();
        }

        if (isset($_REQUEST['styleguide'])) {
            $styleguide = $_REQUEST['styleguide'];
        } else {
            $styleguide = Utils::preference("styleguide");
        }

        if ($styleguide === false) {
            $rs['msg'] = "Preferences File Is Invalid, Please contact system admin.";
            $rs['success'] = false;
            echo CJSON::encode($rs);
            Yii::app()->end();
        }

        if (!empty($_REQUEST['duedate'])) {
            $duedate = strtotime(str_replace("/", "-", $_REQUEST['duedate']));
            $duedate = date("Y-m-d H:i:s", $duedate);
        }

        $model = new Task;
        $cpmodel = new CopypressCampaign;
        $created = time();
        $month = date('Ym', $created);

        $i = 0;
        foreach($ids as $id) {
            $m = $model->findByPk($id);
            $rs['task'][$i]['id'] = $id;
            $rs['task'][$i]['feedback'] = "No related campaign there.";
            if ($m && $m->tasktype == 1 && empty($m->content_article_id)) {
                if ($duedate) $m->duedate = $duedate;
                if (!$m->campaign_id) {
                    $rs['task'][$i]['feedback'] = "No related campaign there.";
                    $rs['task'][$i]['content_article_id'] = 0;
                    $i++;
                    continue;
                }

                $cp = $cpmodel->find("month=:month AND campaign_id=:campaign_id",
                              array(':month' => $month, ':campaign_id' => $m->campaign_id,));
                if ($cp) {
                    $contentcampaignid = $cp->content_campaign_id;
                } else {
                    //create a copypress campaign first.
                    $cp = $cpmodel;
                    $cp->client_id = $m->rcampaign->client_id;
                    $cp->campaign_id = $m->campaign_id;
                    $cp->content_category_id = $m->content_category_id;
                    $cp->content_campaign_name = date('FY', $created) . "_" . $m->rcampaign->name;
                    //$cp->notes = $m->style_guide;
                    $cp->notes = $styleguide;
                    $cp->month = $month;

                    $ccmp = array('campaignname' => $cp->content_campaign_name,
                                  'contentcategory_id' => $cp->content_category_id,
                                  'datestart' => date('Y-m-d', $created),
                                  'campaignrequirement' => $cp->notes,
                                  'dateend' => date('Y-m-d', strtotime($m->duedate)));
                    //print_r($ccmp);
                    //cause the Yii use the lazy load strategy, so we need put the method into a class
                    $response = Utils::sendCmd2SSSAPI("createcampaign", $ccmp);
                    //var_dump($response);
                    if ($response->isSuccessful()) {
                        $responsebody = $response->getBody();
                        //echo $responsebody;
                        $rbodys = simplexml_load_string(utf8_encode($responsebody));
                        //echo $rbodys->campaignstatus->memo;
                        $contentcampaignid = $rbodys->campaignstatus->campaignid;
                        $contentcampaignid = (int)$contentcampaignid;
                        $cp->content_campaign_id = $contentcampaignid;
                        if (!$cp->save()) {
                            //throw new CHttpException(401,'Content Campaign create failure.');
                            //print_r($cp->getErrors());
                            $rs['task'][$i]['feedback'] = "Create content campaign failure.";
                            if ($rbodys->campaignstatus->memo) $rs['task'][$i]['feedback'] .= $rbodys->campaignstatus->memo;
                            $rs['task'][$i]['content_article_id'] = 0;
                            $i++;
                            continue;
                        }
                    }
                }
                $m->content_campaign_id = $contentcampaignid;

                ################# Reset Optional Keywords ##################
                //reset the optional_keywords; acctually we can remove this field!!!
                $optional_keywords = unserialize($m->optional_keywords);
                $optional_keywords['optlkw1'] = $m->rewritten_title;
                $optional_keywords['optlkw2'] = $m->anchortext;
                $optional_keywords['optlkw3'] = $m->targeturl;
                $optional_keywords['optlkw4'] = $m->blog_url;
                $m->optional_keywords = serialize($optional_keywords);
                ################# Reset Optional Keywords ##################

                $p = $m->attributes;
                //$p['content_campaign_id'] = $contentcampaignid;

                //step 2: create article under this campaign
                $response = Utils::sendCmd2SSSAPI("createarticle", $p);
                if ($response->isSuccessful()) {
                    $responsebody = $response->getBody();
                    $rbodys = simplexml_load_string(utf8_encode($responsebody));
                    $article_id = $rbodys->articlestatus->articleid;
                    $m->content_article_id = (int)$article_id;
                }

                if($m->save()){
                    $rs['task'][$i]['feedback'] = "Task #{$id} sent to copypress successfully.";
                    $rs['task'][$i]['content_article_id'] = $m->content_article_id;
                } else {
                    //print_r($m->attributes);
                    $rs['task'][$i]['feedback'] = "Task #{$id} sent to copypress failure.";
                    $rs['task'][$i]['content_article_id'] = 0;
                }

            }//else we should contine it.

            $i++;
        }

        $rs['msg'] = "Send tasks to copypress were done!";
        $rs['success'] = true;
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

        if(isset($_POST['Task']))
        {
            $olddesireddomain = strtolower($model->desired_domain);
            $model->attributes=$_POST['Task'];
            $desireddomain = strtolower($_POST['Task']["desired_domain"]);
            $model->desired_domain = $desireddomain;
            if ($olddesireddomain != $desireddomain) {
                $url = $desireddomain;
                if (($pos = stripos($url, 'http://')) === false 
                    && ($pos = stripos($url, 'https://')) === false) {
                    $url = "http://".$url;
                }

                //determine if the data is a valid URL or just some text string.
                if (filter_var($url, FILTER_VALIDATE_URL)) {
                    //Yii::import('application.vendors.*');
                    //##$desireddomain = SeoUtils::getSubDomain($url);

                    $domodel = Domain::model()->findByAttributes(array("domain" => $desireddomain));
                    if ($domodel) {
                        $model->desired_domain_id = $domodel->id;
                    }
                }
            }

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
            $model=$this->loadModel($id);
            $article_id = $model->content_article_id;
            if ($article_id) {
                //call api and update the status.
                $p = array('ids' => $article_id);

                $response = Utils::sendCmd2SSSAPI("cancelarticle", $p);
                if ($response->isSuccessful()) {
                    $responsebody = $response->getBody();
                    $rbodys = simplexml_load_string(utf8_encode($responsebody));

                    foreach ($rbodys->articlestatus as $_r) {
                        //print_r($_r);
                        $articleid = $_r->articleid;
                        $articleid = (int)$articleid;
                        $status = $_r->status;

                        if (strtolower($status) == 'canceled') {
                            $model->delete();
                        } else {
                            //could n't delete this task, due to the articels status is writing.
                            echo "Delete failure, ".$_r->memo;
                        }
                    }
                }
            } else {
                //$cmpid = $model->campaign_id;
                $model->delete();
            }
            IoHistoricReporting::model()->deleteAllByAttributes(array('task_id' => $id));

            // we only allow deletion via POST request
            //$this->loadModel($id)->delete();

            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if(!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
        }
        else
            throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
    }

    /**
     * Download link building tasks.
     */
    public function actionDownload()
    {
        set_time_limit(3600);
        ini_set("memory_limit", "512M");
        //$this->layout = '';

        $model=new Task('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['Task']))
            $model->attributes=$_GET['Task'];

        $exportType = "Excel5";
        if (strtolower($_GET['Task']['export']) == 'csv') {
            $exportType = "CSV";
        }

        $this->widget('application.extensions.lkgrid.EExcelView', array(
            'id'=>'task-grid',
            'pageSize'=>$model->search()->getTotalItemCount(),
            'filename'=>date("Y-m-d")."_link_task",
            //'customizedata'=>$customizedata,
            'exportType' => $exportType,
            'dataProvider'=>$model->search(),
            'columns'=>array(
                'id',
                'content_article_id',
                'domain',
                'anchortext',
                'targeturl',
                'assignee',
                array(
                    'name' => 'duedate',
                    'type' => 'raw',
                    'value' => '$data->duedate ? date("Y-m-d", strtotime($data->duedate)) : ""',
                ),

            ),
        ));

        //no need use app end, cause we ended this one in the EExcelView already.
        //Yii::app()->end();

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

        $model=new Task('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['Task']))
            $model->attributes=$_GET['Task'];

        $this->render('index',array(
            'model'=>$model,
        ));
    }

    /**
     * Manages all models.
     */
    public function actionProcessing($campaign_id)
    {
        if (empty($campaign_id)) {
            throw new CHttpException(505, 'You need access this module via the right way, please provide campaign first.');
        }

        if (isset($_GET['pageSize'])) {
            Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
            unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
        }

        $model=new Task('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['Task'])) {
            $model->attributes=$_GET['Task'];

            if (isset($_GET['Task']['desired_domain_id'])) {
                $desired_domain_id = $_GET['Task']['desired_domain_id'];
                if (stripos($desired_domain_id, "C:") !== false) {
                    //$_GET['Task']['channel_id'] = str_replace("C:", "", $desired_domain_id);
                    //unset($_GET['Task']['desired_domain_id']);
                    $model->channel_id = str_replace("C:", "", $desired_domain_id);
                    $model->desired_domain_id = null;
                } elseif(stripos($desired_domain_id, "D:") !== false) {
                    //$_GET['Task']['desired_domain_id'] = str_replace("D:", "", $desired_domain_id);
                    $model->desired_domain_id = str_replace("D:", "", $desired_domain_id);
                }
            }

        }
        $model->campaign_id = $campaign_id;
        $cmpmodel = Campaign::model()->findByPk($campaign_id);

        //dispath to different view.
        $cuid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($cuid);
        if(isset($roles['Marketer'])){
            $umodel = User::model()->findByPk($cuid);
            if ($umodel) {
                if ($cmpmodel->client_id != $umodel->client_id) {
                    throw new CHttpException(403, 'Pemission Denied, Please contact with admin.');
                }
                $model->client_id = $umodel->client_id;

                if ($umodel->type != 0) {
                    if ($umodel->duty_campaign_ids) {
                        $model->duty_campaign_ids = unserialize($umodel->duty_campaign_ids);
                        if (!in_array($campaign_id, $model->duty_campaign_ids)) {
                            throw new CHttpException(403, 'Pemission Denied, Please contact with admin.');
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
            $renderview = "clientprocessing";
        } else {
            $renderview = "processing";
        }

        $this->render($renderview,array(
            'model'=>$model,
            'roles'=>$roles,
            'cmpmodel'=>$cmpmodel,
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
        $oldiostatus = $model->iostatus;

        //$_curruser = Yii::app()->getUser();
        $_curruser = Yii::app()->user;

        $uid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($uid);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        $rs = array('success' => true, 'msg' => Yii::t('Task', 'Updated was Successful.'));
        if(isset($_GET['attrname']) && isset($_GET['attrvalue']))
        {
            Yii::import('application.vendors.*');

            $attrname = str_replace("[]", "", $_GET['attrname']);
            $attrvalue = trim($_GET['attrvalue']);
            if (stripos($attrname, "rebuild") !== false) {
                $model->rebuild = $attrvalue;
                $attrname = "iostatus";
                $attrvalue = 5;
            }

            if (stripos($attrname, "desired_domain_id") !== false) {
                //C:$inter,means it is channel, D:$inter means domain;
                if (stripos($attrvalue, "C:") !== false) {
                    $attrname = "channel_id";
                    $attrvalue = str_replace("C:", "", $attrvalue);
                    $rs['channel_id'] = $attrvalue;
                    if ($attrvalue) {
                        //if ($model->sourceurl && empty($model->desired_domain_id)) {
                        if ($model->sourceurl) {
                            if (filter_var($model->sourceurl, FILTER_VALIDATE_URL)) {
                                $__domain = $rs["desired_domain"] = SeoUtils::getSubDomain($model->sourceurl);
                            }
                        } else {
                            $model->desired_domain = null;
                            $model->desired_domain_id = 0;
                        }
                    } else {
                        $model->desired_domain = null;
                        $model->desired_domain_id = 0;
                    }
                } elseif(stripos($attrvalue, "D:") !== false) {
                    $attrvalue = str_replace("D:", "", $attrvalue);
                    $ivtmodel = Inventory::model()->findByAttributes(array("domain_id" => $attrvalue));
                    $model->desired_domain = $ivtmodel->domain;
                    if ($ivtmodel->channel_id && strlen($ivtmodel->channel_id) > 2) {
                        /*
                        $chls = explode("|", $ivtmodel->channel_id);
                        $model->channel_id = $chls[1];
                        */
                        //$chlstr = substr($ivtmodel->channel_id, 1, (strlen($ivtmodel->channel_id) -2));
                        $chlstr = substr($ivtmodel->channel_id, 1, -1);
                        $chls = explode("|", $chlstr);
                        $model->channel_id = $chls[0];//set the 1st one as default
                        $rs['channel_id'] = $model->channel_id;
                        //$rs['chlstr'] = str_replace("|", ",", $chlstr);
                        $rs['chlstr'] = $chls;
                    }
                    /*
                    //$cmpmodel = new Campaign;
                    $client_domain_id = $model->rcampaign->domain_id;
                    $cartmodel=Cart::model()->findByAttributes(array("domain_id" => $attrvalue,
                                                                     "client_domain_id" => $client_domain_id))->delete();
                    */
                    $client_domain_id = $model->rcampaign->domain_id;
                    $cartmodel=Cart::model()->findByAttributes(array("domain_id" => $attrvalue,
                                                                     "client_domain_id" => $client_domain_id));
                    if (empty($model->sourceurl)) {
                        $cartmodel->status = 1;
                    } else {
                        $cartmodel->status = 2;
                    }
                    $cartmodel->save();
                } else {
                    if (empty($model->desired_domain_id) && !empty($model->channel_id)) {
                        $model->channel_id = 0;
                    }
                    $attrvalue = "";//$_GET['attrvalue'];
                }
                $model->$attrname = $attrvalue;
            } else if (stripos($attrname, "desired_domain") !== false) {
                /*
                When a user is in IO Accepted and hits save, it will check this blacklist first. 
                If its Blacklisted as Blacklist , it will not let them proceed. 
                If it is blacklisted as a Warning it will alert them and ask them if they are sure they want to continue.
                */
                if ($model->iostatus == 2 && strlen($attrvalue)>3 && !isset($_GET["forcechange"])) {
                    $attrvalue = SeoUtils::getSubDomain($attrvalue);

                    /*
                    When creating a campaign, add a new checkbox called:  Allow duplicate URL.  When checked, then we will just alert the user on the IO-ACCEPTED tab that they have entered a duplicate url.  When it's un-checked, then we will alert the user they have entered a duplicate url, and you will clear out the DESIRED PLACEMENT field.  Have this defaulted to checked.
                    On the IO-ACCEPTED tab, when you look for a duplicate URL, we just care about duplicates in the campaign regardless of where they are at in the IO.  
                    For the message that pops up, are you able to say something like "This URL has already been used for the following Task IDS: XXXX, XXXX etc"?
                    */
                    if (!empty($model->campaign_id)) {
                        $existtask = Task::model()->findByAttributes(array("desired_domain"=>$attrvalue,
                                                                           "campaign_id"=>$model->campaign_id), "(id != {$id})");
                        if ($existtask) {
                            $cmpinfo = Campaign::model()->findByPk($model->campaign_id);
                            $rs["makeitblue"] = true;
                            if ($cmpinfo) {
                                $rs['msg'] = Yii::t('Task', "This Desired Placement has already been used for the following Task ID:".$existtask->id);
                                if ($cmpinfo->allow_duplicate_url == 1) {
                                    //do nothing for now;
                                    $rs['allowduplicate'] = 1;
                                } else {
                                    $rs['allowduplicate'] = 0;
                                    $rs['success'] = false;
                                    echo CJSON::encode($rs);
                                    Yii::app()->end();
                                }
                            }
                        }
                    }

                    $ioblmdl = Ioblacklist::model()->findByAttributes(array("domain"=>$attrvalue));

                    //For domain input it should keep subdomain in mind.
                    $rootattrvalue = SeoUtils::getDomain($attrvalue);
                    if ($attrvalue != $rootattrvalue && !$ioblmdl) {
                        $ioblmdl = Ioblacklist::model()->findByAttributes(array("domain"=>$attrvalue));
                    }
                    if ($ioblmdl) {
                        $blclients = explode("|", $ioblmdl->clients);
                        if ($ioblmdl->isallclient || in_array($model->rcampaign->client_id, $blclients)) {
                            if ($ioblmdl->isblacklist) {
                                $rs['msg'] = Yii::t('Task', "This domain $attrvalue has been blacklisted for the following reason:\r\n".$ioblmdl->notes);
                            } else {
                                $rs['msg'] = Yii::t('Task', "A Warning has been added to this domain $attrvalue for the following reason: \r\n".$ioblmdl->notes.", \r\nAre you sure?");
                            }

                            $rs['isblacklist'] = $ioblmdl->isblacklist;
                            $rs['success'] = false;
                            echo CJSON::encode($rs);
                            Yii::app()->end();
                        } else {
                            //do nothing;
                        }
                    }
                }

                //cause we already check stripos($attrname, "desired_domain_id"), so here we can use desired_domain directly
                //store this domain into tbl.domain & tbl.inventory
                $__domain = $rs["desired_domain"] = $model->$attrname = $attrvalue;
                //Check the desired domain exist in accept,pending,approved; then make it blue.
                if (strlen($attrvalue)>=3) {
                    $bluetasks = Task::model()->findByAttributes(array('desired_domain'=>$attrvalue),
                                    "(iostatus IN (2,3,21)) AND (id != {$id})");
                    if ($bluetasks) {
                        $rs["makeitblue"] = true;
                    }
                }
            } else if (stripos($attrname, "sourceurl") !== false) {
                $_tmp_sourceurl = $model->sourceurl;
                $model->$attrname = $attrvalue;
                $url = strtolower($attrvalue);
                if (($pos = stripos($url, 'http://')) === false && ($pos = stripos($url, 'https://')) === false) {
                    $url = "http://".$url;
                }

                //determine if the data is a valid URL or just some text string.
                if (filter_var($url, FILTER_VALIDATE_URL)) {
                    /*
                    Leo - Can we make a change to the Approved IO tab.  We would like you to implement similar functionality as the Accepted tab when looking for duplicate links in the Desired Placement field.
                    For the Approved IO screen, can you change the Posted URL field to red after they have entered in the URL if that url already exists?  That would queue the user to go research where else we have used the URL before.  
                    */
                    $_noschemeurl = str_replace(array("http://", "https://"), array("",""), strtolower($attrvalue));
                    if (substr($_noschemeurl, -1) == "/") $_noschemeurl = substr($_noschemeurl, 0, -1);
                    /*
                    $makeitreds = Task::model()->findByAttributes(array('sourceurl'=>$_noschemeurl),
                                    "(id != {$id})");
                    */
                    $makeitreds = Task::model()->find("(id != $id) AND ((sourceurl LIKE :sourceurl) OR (sourceurl LIKE :sourceurl2))", 
                        array(':sourceurl'=>"%".$_noschemeurl,':sourceurl2'=>"%".$_noschemeurl."/"));
                    if ($makeitreds) {
                        $rs["makeitred"] = true;
                    }

                    //Yii::import('application.vendors.*');
                    $rs["desired_domain"] = SeoUtils::getSubDomain($url);

                    $_checkspent = $model->spent * 100;
                    if (!empty($attrvalue) && !in_array($model->iostatus, array(31,5,32)) 
                        && !empty($model->livedate) && $_checkspent > 0) {
                        $model->iostatus = 31;
                        //###$model->iodate = date('Y-m-d H:i:s');
                        if ($_tmp_sourceurl != $attrvalue) $model->iodate = date('Y-m-d H:i:s');
                    }

                    if (!$model->desired_domain) {
                        if ($model->channel_id) {
                            $__domain = strtolower($rs["desired_domain"]);
                        }
                        /*
                        $__url = Yii::app()->createUrl("/task/setattr",array("attrname"=>"desired_domain[]", 
                                                                             "attrvalue"=>$rs["desired_domain"], "id"=>$id));
                        //set the desired_domain;
                        file_get_contents("$__url");
                        */
                    } else {
                        $__root_domain = SeoUtils::getDomain($url);
                        $__root_desired = SeoUtils::getDomain($model->desired_domain);
                        //$__root_domain = strtolower($__root_domain);
                        //$__root_desired = strtolower($__root_desired);
                        $__model_root_desired = strtolower($model->desired_domain);

                        if (isset($_GET["forcechange"]) && $_GET["forcechange"]) {
                            if ($model->channel_id) {
                                $__domain = strtolower($rs["desired_domain"]);
                            }
                        } else {

                            if (($__root_desired == $__model_root_desired && $__root_desired == $__root_domain)
                             || ($__root_desired != $__model_root_desired && $__model_root_desired == $rs["desired_domain"])) {
                                if ($model->channel_id) {
                                    $__domain = strtolower($rs["desired_domain"]);
                                }
                            } else {
                                if (!isset($roles["Admin"])) {
                                    $rs['success'] = false;
                                    if ($_curruser->checkAccess('task.*')!==true 
                                     && $_curruser->checkAccess('task.processing')!==true) {
                                        //$rs['msg'] = Yii::t('Task','This will not be saved. To use this domain please rewind this task.');
                                    } else {
                                        //$rs['msg'] = Yii::t('Task','Domain already used. Ask Admin for override.');
                                        $rs['forcechange'] = 1;
                                    }
                                    $rs['msg'] = Yii::t('Task','Domain already used. Ask Admin for override.');
                                    echo CJSON::encode($rs);
                                    Yii::app()->end();
                                }
                            }

                        }
                    }
                } else {}//coz it is just some text string, so do nothing for now.
            } else if (stripos($attrname, "livedate") !== false) {
                if (empty($attrvalue)) $attrvalue = null;
                $model->$attrname = $attrvalue;
                $_checkspent = $model->spent * 100;
                if (!empty($attrvalue) && $model->iostatus==3 && !empty($model->sourceurl) && $_checkspent > 0) {
                    $model->iostatus = 31;
                    $model->iodate = date('Y-m-d H:i:s');
                }
            } else if (stripos($attrname, "duedate") !== false) {
                if (empty($attrvalue)) {
                    $attrvalue = 0;
                } else {
                    //$attrvalue = strtotime($attrvalue); //cause we changed the duedate format from int to datetime in table
                }
                $model->$attrname = $attrvalue;
            } else if (stripos($attrname, "channel_id") !== false) {
                $model->$attrname = $attrvalue;
                if ($model->sourceurl) {
                    if (filter_var($model->sourceurl, FILTER_VALIDATE_URL)) {
                        $__domain = $rs["desired_domain"] = SeoUtils::getSubDomain($model->sourceurl);
                    }
                }
            } else if(stripos($attrname, "iostatus") !== false) {
                // if the IO was denied in pending view, then remove the desired_domain.
                if ($attrvalue == 1 && $model->iostatus == 21) {
                    $model->desired_domain = null;
                    $model->desired_domain_id = 0;
                }
                if ((($oldiostatus==2 && $attrvalue==21) || $attrvalue==3 || $attrvalue==31 || $attrvalue==5) 
                    && $model->desired_domain_id > 0) {
                    //if iostatus is approved, then set the desire domain to Site Acquired.
                    $domodel = Domain::model()->findByPk($model->desired_domain_id);
                    if ($domodel && $domodel->touched_status != 20) {
                        if ($attrvalue==5) {
                            $domodel->touched_status = 20;
                        } else {
                            $domodel->touched_status = 6;
                        }
                        if (!$domodel->touched_by) {
                            $domodel->touched = date("Y-m-d H:i:s");
                            $domodel->touched_by = Yii::app()->user->id;
                        }
                        $domodel->status = 1;
                        $domodel->save();
                    }
                }
                $model->$attrname = $attrvalue;
                if (!isset($roles["Admin"]) && $oldiostatus == 2 && in_array($attrvalue, array(3,31,5,21)) && $model->desired_domain_id > 0) {
                    $targetdomain = SeoUtils::getDomain($model->targeturl);
                    $existtask = Task::model()->findByAttributes(array('desired_domain'=>$model->desired_domain),
                                     "targeturl LIKE '%{$targetdomain}%' AND (iostatus IN (3,5,31,21)) AND (id != {$id})");
                                     //"targeturl LIKE '%{$targetdomain}%' AND id != {$id}");
                                                          //'targeturl'=>$model->targeturl), "id != {$id}");
                    if ($existtask) {
                        $rs['success'] = false;
                        if ($_curruser->checkAccess('task.*')!==true 
                         && $_curruser->checkAccess('task.processing')!==true) {
                            //$rs['msg'] = "'".$model->desired_domain."' has already been used. Please find a different Desired Placement.";
                        } else {
                            //$rs['msg'] = Yii::t('Task','Domain already used. Ask Admin for override.');
                            $rs['forcechange'] = 1;
                        }

                        $rs['msg'] = Yii::t('Task','Domain already used. Ask Admin for override.');
                        echo CJSON::encode($rs);
                        Yii::app()->end();
                    }
                }
            } else if(stripos($attrname, "spent") !== false) {
                if (empty($attrvalue)) {
                    $attrvalue = 0;
                } else {
                    $attrvalue = str_replace("$", "", $attrvalue);
                    if (!is_numeric($attrvalue)) {
                        $rs['success'] = false;
                        $rs['msg'] = Yii::t('Task', 'Invalid Format.');
                        echo CJSON::encode($rs);
                        Yii::app()->end();
                    }

                    $_checkspent = $attrvalue * 100;
                    if (!empty($model->sourceurl) && !in_array($model->iostatus, array(31,5,32)) 
                        && !empty($model->livedate) && $_checkspent > 0) {
                        $model->iostatus = 31;
                        $model->iodate = date('Y-m-d H:i:s');
                    }
                }
                $model->$attrname = $attrvalue;
            } else {
                $model->$attrname = $attrvalue;
            }


            if (stripos($attrname, "sourceurl") !== false || $attrname == "desired_domain" || $attrname == "channel_id") {
                //echo "-----$__domain------";

                //Check the user to see if he/she can access the task/processing page.
                /*
                if ($_curruser->checkAccess('task.*')!==true && $_curruser->checkAccess('task.processing')!==true) {
                    if ($attrname != "channel_id" && !empty($rs["desired_domain"])) {
                        $targetdomain = SeoUtils::getDomain($model->targeturl);
                        $existtask = Task::model()->findByAttributes(array('desired_domain'=>$rs["desired_domain"]),
                                                                     "targeturl LIKE '%{$targetdomain}%' AND id != {$id}");
                                                              //'targeturl'=>$model->targeturl), "id != {$id}");
                        if ($existtask) {
                            $rs['success'] = false;
                            $rs['msg'] = "'".$rs["desired_domain"]."' has already been used. Please find a different Desired Placement.";
                            echo CJSON::encode($rs);
                            Yii::app()->end();
                        }
                    }
                }
                */

                if (isset($_GET["forcechange"]) && $_GET["forcechange"]) {
                    //do nothing;
                } else {
                    if (!isset($roles["Admin"]) && $attrname != "channel_id" && !empty($rs["desired_domain"])) {
                        $targetdomain = SeoUtils::getDomain($model->targeturl);
                        $existtask = Task::model()->findByAttributes(array('desired_domain'=>$rs["desired_domain"]),
                                        "targeturl LIKE '%{$targetdomain}%' AND (iostatus IN (3,5,31,21)) AND (id != {$id})");
                        if ($existtask) {
                            $rs['success'] = false;
                            if ($_curruser->checkAccess('task.*')!==true 
                             && $_curruser->checkAccess('task.processing')!==true) {
                                //##$rs['msg'] = "'".$rs["desired_domain"]."' has already been used. Please find a different Desired Placement.";
                            } else {
                                //##$rs['msg'] = Yii::t('Task','Domain already used, do you want to override?');
                                //##$rs['msg'] = Yii::t('Task','Domain already used. Ask Admin for override.');
                                $rs['forcechange'] = 1;
                            }
                            $rs['msg'] = Yii::t('Task','Domain already used. Ask Admin for override.');
                            echo CJSON::encode($rs);
                            Yii::app()->end();
                        }
                    }
                }

                if (isset($__domain) && !empty($__domain)) {
                    $ivtmodel = new Inventory;
                    $ivt = $ivtmodel->findByAttributes(array('domain' => $__domain));

                    if ($attrname == "channel_id") {
                        $_channel_id = $model->channel_id = $attrvalue;
                    } else {
                        $_channel_id = $model->channel_id;
                    }

                    if(isset($roles['Publisher'])){
                        $umodel = User::model()->findByPk($uid);
                        if ($umodel->channel_id) $_channel_id = $umodel->channel_id;
                    }
                    if ($ivt) {
                        $model->desired_domain_id = $ivt->domain_id;
                        $model->desired_domain = $__domain;
                        if ($_channel_id) {
                            if ($ivt->channel_id) {
                                $chlstr = substr($ivt->channel_id, 1, -1);
                                $_chls = explode("|", $chlstr);
                                if (!in_array($_channel_id, $_chls)) {
                                    array_push($_chls, $_channel_id);
                                }
                            } else {
                                $_chls = array($_channel_id);
                            }

                            if (empty($ivt->acquired_channel_id)) $ivt->acquired_channel_id = $_channel_id;
                            if (empty($ivt->ispublished) && $model->iostatus == 5) {
                                $ivt->ispublished = 1;
                                $ivt->last_published = date('Y-m-d H:i:s');
                            }

                            $ivt->setIsNewRecord(false);
                            $ivt->setScenario('update');
                            $ivt->channel_id = $_chls;
                            if (empty($ivt->acquireddate) && $model->iostatus>2) $ivt->acquireddate = date('Y-m-d H:i:s');
                            $ivt->triggerDomainSave = true;
                            $ivt->save();
                        }
                    } else {
                        $ivt = $ivtmodel;
                        $ivt->setIsNewRecord(true);
                        $ivt->id=NULL;
                        $ivt->domain=$__domain;
                        if ($_channel_id) {
                            $ivt->channel_id = array($_channel_id);
                            $ivt->acquired_channel_id = $_channel_id;
                        }
                        if (empty($ivt->ispublished) && $model->iostatus == 5) {
                            $ivt->ispublished = 1;
                            $ivt->last_published = date('Y-m-d H:i:s');
                        }
                        if ($model->iostatus>2) $ivt->acquireddate = date('Y-m-d H:i:s');
                        $ivt->triggerDomainSave = true;
                        if ($ivt->save()) {
                            $model->desired_domain_id = $ivt->domain_id;
                            $model->desired_domain = $__domain;
                        }
                    }

                    $_cmpmd = Campaign::model()->findByPk($model->campaign_id);

                    $cartmodel = new Cart;
                    $ctmd = $cartmodel->findByAttributes(array('domain_id' => $ivt->domain_id,
                                                               'client_id' => $_cmpmd->client_id,
                                                               'client_domain_id' => $_cmpmd->domain_id));
                    $_cartstatus = empty($model->sourceurl) ? 1 : 2;
                    if ($ctmd) {
                        if ($ctmd->status == 2) {
                            $ctmd->setIsNewRecord(false);
                            $ctmd->setScenario('update');
                            $ctmd->status = $_cartstatus;
                            $ctmd->save();
                        }
                    } else {
                        $ctmd = $cartmodel;
                        $ctmd->setIsNewRecord(true);
                        $ctmd->id=NULL;
                        $ctmd->status=$_cartstatus;
                        $ctmd->domain_id = $ivt->domain_id;
                        $ctmd->domain = $ivt->domain;
                        $ctmd->client_id = $_cmpmd->client_id;
                        $ctmd->client_domain_id = $_cmpmd->domain_id;
                        $ctmd->client_domain = $_cmpmd->domain;
                        $ctmd->save();
                    }
                }
            }

            //We can put this part into the Task model afterSave();
            if ($oldiostatus != $model->iostatus) {
                $iomodel = new Iohistory;
                $iomodel->setIsNewRecord(true);
                $iomodel->id       = NULL;
                $iomodel->oldiostatus = $oldiostatus;
                $iomodel->iostatus = $model->iostatus;
                $iomodel->task_id  = $id;
                if ($model->iostatus == 1 || empty($model->iodate)) {
                    $iomodel->created = $model->created;//the tbl.io_history.created will be reset in beforeValidate
                } else {
                    $iomodel->created = $model->iodate;//the tbl.io_history.created will be reset in beforeValidate
                }
                $iomodel->save();
            }

            if (stripos($attrname, "iostatus") !== false || 
                (stripos($attrname, "sourceurl") !== false && filter_var($url, FILTER_VALIDATE_URL) && !empty($attrvalue))) {
                if (isset($_tmp_sourceurl)) {
                    if ($_tmp_sourceurl != $attrvalue) $model->iodate = date('Y-m-d H:i:s');
                } else {
                    $model->iodate = date('Y-m-d H:i:s');
                }
            }

            ################################# End: plugin for IO history ########################

            if($model->save()) {
                //do nothing;
                if (stripos($attrname, "iostatus") !== false) {
                    if ($_GET['attrvalue'] == 4) {
                        $np = array();
                        //$np['tos'] = array("csdeny@steelcast.com");
                        $np['tos'] = array("csclientissues@steelcast.com");
                        $np['subject'] = "The IO #{$id} Was Denied By " . Yii::app()->user->name;
                        $np['content'] = "Anchor Text: " . $model->anchortext . "\nTarget URL: " . $model->targeturl;
                        $np['format'] = "text/plain";
                        Utils::notice($np);
                    }
                }
            } else {
                $rs['success'] = false;
                $rs['msg'] = Yii::t('Task', 'Updated was Failure.');
            }
            $rs["status"] = $model->iostatus;
        }

        echo CJSON::encode($rs);
        Yii::app()->end();
    }

    /**
     * Send link building task to IO. Or bulk cancel IO
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionSend2io()
    {
        $ids = $_REQUEST['ids'];
        if (!is_array($ids)) {
            $ids = array($ids);
        }

        $oprname = "send";
        if ($_REQUEST['oprname'] == "cancel") $oprname = "cancel";
        if ($_REQUEST['oprname'] == "send2step") $oprname = "send2step";

        if (empty($ids)) {
            $rs['msg'] = "Please provide at least one link building task";
            $rs['success'] = false;
            echo CJSON::encode($rs);
            Yii::app()->end();
        }

        $model = new Task;

        $i = 0;
        foreach($ids as $id) {
            $m = $model->findByPk($id);
            $rs['task'][$i]['id'] = $id;
            if ($oprname == "cancel") {
                $rs['task'][$i]['feedback'] = "you couldn't cancel io task #{$id}.";
                if ($m && in_array($m->iostatus, array(0,1,2,4))) {
                    $m->iostatus = 0;
                    $m->channel_id = 0;
                    $m->desired_domain_id = 0;
                    $m->desired_domain = null;
                    if($m->save()){
                        $rs['task'][$i]['feedback'] = "Cancel IO #{$id} successfully.";
                    } else {
                        //print_r($m->attributes);
                        $rs['task'][$i]['feedback'] = "Cancel IO #{$id} failure.";
                    }
                }//else we should contine it.
            } else if ($oprname == "send2step") {
                $rs['task'][$i]['feedback'] = "you couldn't send task #{$id} to content step again.";
                //###if ($m && in_array($m->content_step, array(0,1,2,4))) {
                if ($m) {
                    //This part need remove, due to we re-build the ContentStep PART already
                    $m->content_step = 1;
                    $m->step_date    = date("Y-m-d H:i:s");
                    //##$m->content_step_editor = $_REQUEST['bulk_channel_id'];
                    if($m->save()){
                        $rs['task'][$i]['feedback'] = "Send Task #{$id} to Content Step successfully.";
                    } else {
                        $rs['task'][$i]['feedback'] = "Send Task #{$id} to Content Step failure.";
                    }
                }//else we should contine it.
            } else {
                $rs['task'][$i]['feedback'] = "you couldn't send task #{$id} to io again.";
                if ($m && ($m->iostatus == 0 || $m->iostatus == 4)) {
                    if (empty($m->channel_id) && empty($m->desired_domain_id) && !empty($_REQUEST['bulk_channel_id'])) {
                        $m->channel_id = $_REQUEST['bulk_channel_id'];
                    }
                    if ($_REQUEST['duedate']) $m->duedate = $_REQUEST['duedate'];

                    $m->iostatus = 1;
                    if($m->save()){
                        $rs['task'][$i]['feedback'] = "Task #{$id} sent to Io successfully.";
                    } else {
                        //print_r($m->attributes);
                        $rs['task'][$i]['feedback'] = "Task #{$id} sent to Io failure.";
                    }
                }//else we should contine it.
            }

            $i++;
        }

        $rs['msg'] = "Send tasks to IO were done!";
        if ($oprname == "cancel") $rs['msg'] = "Cancel IO were done!";
        if ($oprname == "send2step") $rs['msg'] = "Send task to Content Step were done!";

        $rs['success'] = true;
        echo CJSON::encode($rs);
        Yii::app()->end();
    }

    /**
     * Send link building task to copypress.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionBatchattr()
    {
        $ids = $_REQUEST['ids'];
        if (!is_array($ids)) {
            $ids = array($ids);
        }

        if (empty($ids)) {
            $rs['msg'] = "Please provide at least one link building task";
            $rs['success'] = false;
            echo CJSON::encode($rs);
            Yii::app()->end();
        }

        $model = new Task;
        $attrname = $_REQUEST['attrname'];
        $attrvalue = $_REQUEST['attrvalue'];

        $i = 0;
        foreach($ids as $id) {
            $m = $model->findByPk($id);
            $rs['task'][$i]['id'] = $id;
            $rs['task'][$i]['feedback'] = "you couldn't set the $attrname of task #{$id}.";
            if ($m) {
                if ($attrname == "iostatus") {
                    $oldiostatus = $m->iostatus;
                    $m->iodate = date('Y-m-d H:i:s');
                }
                $m->$attrname = $attrvalue;
                if($m->save()){
                    $rs['task'][$i]['feedback'] = "Set the $attrname of task #{$id} successfully.";
                    if ($attrname == "iostatus") {
                        $iomodel = new Iohistory;
                        $iomodel->setIsNewRecord(true);
                        $iomodel->id       = NULL;
                        $iomodel->oldiostatus = $oldiostatus;
                        $iomodel->iostatus = $attrvalue;
                        $iomodel->task_id  = $id;
                        if ($model->iostatus == 1 || empty($model->iodate)) {
                            $iomodel->created = $m->created;
                        } else {
                            $iomodel->created = $m->iodate;
                        }
                        $iomodel->save();
                    }
                } else {
                    //print_r($m->attributes);
                    $rs['task'][$i]['feedback'] = "set the $attrname of task #{$id} failure.";
                }
            }//else we should contine it.

            $i++;
        }

        $attrlabel = $model->getAttributeLabel($attrname);

        $rs['msg'] = "$attrlabel Updated.";
        $rs['success'] = true;
        echo CJSON::encode($rs);
        Yii::app()->end();
    }

    //Client Request email
    public function actionRequest($id = 0)
    {
        set_time_limit(0);
        ini_set("memory_limit", "128M");

        $p = $_REQUEST;
        if (empty($id)) $id = $p['crtaskid'];

        $rtn = array();
        $umodel = User::model()->findByPk(Yii::app()->user->id);

        if (empty($p['cremail'])) {
            $p['cremail'] = $umodel->email;
        }
        $email = $p['cremail'];

        if ($id) {
            $taskmodel = Task::model()->with("rcampaign")->findByPk($id);
            $campaignname = $taskmodel->rcampaign->name;
        }

        $username = $umodel->username;
        $subject = "Client Feedback from " . $username;
        $content = "{$username} has an issue with task #{$id} from campaign '{$campaignname}' of concern/feedback. \nPlease contact {$email} to further discuss this issue. \n\nThe request as following:\n".$p['crclientrequest'];

        $np = array();
        $np['tos'] = array("csclientissues@steelcast.com");
        $np['mfrom'] = $email;
        $np['displayname'] = $username;
        $np['content'] = $content;
        $np['subject'] = $subject;
        $np['format'] = "text/plain";

        $c = Utils::notice($np);
        if ($c) {
            //store it into database;
            $rtn['msg'] = "Request was sent successfully.";
            $rtn['success'] = true;
        } else {
            $rtn['msg'] = "Request sent failure, please try it again";
            $rtn['success'] = false;
        }

        echo CJSON::encode($rtn);
        Yii::app()->end();
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id)
    {
        $model=Task::model()->findByPk($id);
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
        if(isset($_POST['ajax']) && $_POST['ajax']==='task-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}
