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

        if (!empty($_REQUEST['duedate'])) $duedate = strtotime(str_replace("/", "-", $_REQUEST['duedate']));

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
                                  'dateend' => date('Y-m-d', $m->duedate));
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
            $model->attributes=$_POST['Task'];
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
                    'value' => '$data->duedate ? date("Y-m-d", $data->duedate) : ""',
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

        //$_curruser = Yii::app()->getUser();
        $_curruser = Yii::app()->user;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        $rs = array('success' => true, 'msg' => Yii::t('Task', 'Updated was Successful.'));
        if(isset($_GET['attrname']) && isset($_GET['attrvalue']))
        {
            Yii::import('application.vendors.*');

            $attrname = str_replace("[]", "", $_GET['attrname']);
            $attrvalue = $_GET['attrvalue'];
            if (empty($model->progressstatus) && in_array($attrname, array("rewritten_title","blog_title","blog_url"))) {
                if (!empty($attrvalue)) {
                    if (!empty($model->desired_domain_id) || !empty($model->channel_id)) {
                        $model->progressstatus = 2;
                    } else {
                        $model->progressstatus = 1;
                    }
                }
            }
            if (stripos($attrname, "desired_domain_id") !== false) {
                //C:$inter,means it is channel, D:$inter means domain;
                if (stripos($attrvalue, "C:") !== false) {
                    if ($model->progressstatus <= 2) {
                        $model->progressstatus = 2;
                    }
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
                    if ($model->progressstatus <= 2) {
                        $model->progressstatus = 2;
                    }
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
                        if ($model->progressstatus <= 2 && $model->iostatus == 0) {
                            $model->progressstatus = 1;
                        }
                        $model->channel_id = 0;
                    }
                    $attrvalue = "";//$_GET['attrvalue'];
                }
                $model->$attrname = $attrvalue;
            } else if (stripos($attrname, "desired_domain") !== false) {
                //cause we already check stripos($attrname, "desired_domain_id"), so here we can use desired_domain directly
                //store this domain into tbl.domain & tbl.inventory
                $__domain = $rs["desired_domain"] = $model->$attrname = $attrvalue;

                /*
                if (!empty($attrvalue)) {
                    $ivtmodel = new Inventory;
                    //$ivt = $ivtmodel->find('domain=:domain',array(':domain'=>$attrvalue));
                    $ivt = $ivtmodel->findByAttributes(array('domain' => $attrvalue));
                    $uid = Yii::app()->user->id;
                    $roles = Yii::app()->authManager->getRoles($uid);
                    $_channel_id = $model->channel_id;
                    if(isset($roles['Publisher'])){
                        $umodel = User::model()->findByPk($uid);
                        if ($umodel->channel_id) $_channel_id = $umodel->channel_id;
                    }
                    if ($ivt) {
                        $model->desired_domain_id = $ivt->domain_id;
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
                            $ivt->setIsNewRecord(false);
                            $ivt->setScenario('update');
                            $ivt->channel_id = $_chls;
                            $ivt->save();
                        }
                    } else {
                        $ivt = $ivtmodel;
                        $ivt->setIsNewRecord(true);
                        $ivt->id=NULL;
                        $ivt->domain=$attrvalue;
                        if ($_channel_id) $ivt->channel_id = array($_channel_id);
                        if ($ivt->save()) {
                            $model->desired_domain_id = $ivt->domain_id;
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
                */
            } else if (stripos($attrname, "sourceurl") !== false) {
                $_tmp_sourceurl = $model->sourceurl;
                $model->$attrname = $attrvalue;
                $url = strtolower($attrvalue);
                if (($pos = stripos($url, 'http://')) === false && ($pos = stripos($url, 'https://')) === false) {
                    $url = "http://".$url;
                }

                //determine if the data is a valid URL or just some text string.
                if (filter_var($url, FILTER_VALIDATE_URL)) {
                    //Yii::import('application.vendors.*');
                    $rs["desired_domain"] = SeoUtils::getSubDomain($url);

                    if (!empty($attrvalue)) {
                        $model->iostatus = 5;
                        //###$model->iodate = date('Y-m-d H:i:s');
                        if ($_tmp_sourceurl != $attrvalue) $model->iodate = date('Y-m-d H:i:s');
                        if (empty($model->progressstatus) || $model->progressstatus <=3) {
                            $model->progressstatus = 3;
                        }
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
                                $rs['success'] = false;
                                if ($_curruser->checkAccess('task.*')!==true 
                                 && $_curruser->checkAccess('task.processing')!==true) {
                                    $rs['msg'] = Yii::t('Task','This will not be saved. To use this domain please rewind this task.');
                                } else {
                                    $rs['msg'] = Yii::t('Task','Domain already used, do you want to override?');
                                    $rs['forcechange'] = 1;
                                }
                                echo CJSON::encode($rs);
                                Yii::app()->end();
                            }

                        }

                        /*
                        //if (strtolower($model->desired_domain) != $rs["desired_domain"]) {
                        if ($__root_desired != $__root_domain) {
                            //$model->channel_id = 0;
                            //$model->desired_domain = null;
                            //$model->desired_domain_id = 0;

                            $rs['success'] = false;
                            $rs['msg'] = Yii::t('Task', 'This will not be saved. To use this domain please rewind this task.');

                            echo CJSON::encode($rs);
                            Yii::app()->end();
                        } else {
                            if ($model->channel_id) {
                                $__domain = $rs["desired_domain"];
                            }
                        }
                        */
                        //$rs["desired_domain"] = $model->desired_domain;
                    }
                } else {}//coz it is just some text string, so do nothing for now.
            } else if (stripos($attrname, "livedate") !== false) {
                if (empty($attrvalue)) $attrvalue = null;
                $model->$attrname = $attrvalue;
            } else if (stripos($attrname, "duedate") !== false) {
                if (empty($attrvalue)) {
                    $attrvalue = 0;
                } else {
                    $attrvalue = strtotime($attrvalue);
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
                    if ($attrname != "channel_id" && !empty($rs["desired_domain"])) {
                        $targetdomain = SeoUtils::getDomain($model->targeturl);
                        $existtask = Task::model()->findByAttributes(array('desired_domain'=>$rs["desired_domain"]),
                                                                     "targeturl LIKE '%{$targetdomain}%' AND id != {$id}");
                                                              //'targeturl'=>$model->targeturl), "id != {$id}");
                        if ($existtask) {
                            $rs['success'] = false;
                            if ($_curruser->checkAccess('task.*')!==true 
                             && $_curruser->checkAccess('task.processing')!==true) {
                                $rs['msg'] = "'".$rs["desired_domain"]."' has already been used. Please find a different Desired Placement.";
                            } else {
                                $rs['msg'] = Yii::t('Task','Domain already used, do you want to override?');
                                $rs['forcechange'] = 1;
                            }
                            echo CJSON::encode($rs);
                            Yii::app()->end();
                        }
                    }
                }

                if (isset($__domain) && !empty($__domain)) {
                    $ivtmodel = new Inventory;
                    $ivt = $ivtmodel->findByAttributes(array('domain' => $__domain));
                    $uid = Yii::app()->user->id;
                    $roles = Yii::app()->authManager->getRoles($uid);

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
                            $ivt->setIsNewRecord(false);
                            $ivt->setScenario('update');
                            $ivt->channel_id = $_chls;
                            $ivt->save();
                        }
                    } else {
                        $ivt = $ivtmodel;
                        $ivt->setIsNewRecord(true);
                        $ivt->id=NULL;
                        $ivt->domain=$__domain;
                        if ($_channel_id) $ivt->channel_id = array($_channel_id);
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

            ################################# Start: plugin for IO history ########################
            if (stripos($attrname, "iostatus") !== false || 
                (stripos($attrname, "sourceurl") !== false && filter_var($url, FILTER_VALIDATE_URL) && !empty($attrvalue))) {
                $iomodel = new Iohistory;
                $iomodel->setIsNewRecord(true);
                $iomodel->id       = NULL;
                if (stripos($attrname, "iostatus") !== false) {
                    $iomodel->iostatus = $attrvalue;
                } else {
                    $iomodel->iostatus = 5;
                }
                $iomodel->task_id  = $id;
                if ($attrvalue == 1 || empty($model->iodate)) {
                    $iomodel->created = $model->created;//the tbl.io_history.created will be reset in beforeValidate
                } else {
                    $iomodel->created = $model->iodate;//the tbl.io_history.created will be reset in beforeValidate
                }
                $iomodel->save();

                if (stripos($attrname, "iostatus") !== false) {
                    $model->iodate = date('Y-m-d H:i:s');
                } else {
                    if ($_tmp_sourceurl != $attrvalue) $model->iodate = date('Y-m-d H:i:s');
                }
            }
            ################################# End: plugin for IO history ########################

            if($model->save()) {
                //do nothing;
                if (stripos($attrname, "iostatus") !== false) {
                    if ($_GET['attrvalue'] == 4) {
                        $np = array();
                        $np['tos'] = array("csdeny@steelcast.com");
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
            $rs["status"] = $model->progressstatus;
        }

        echo CJSON::encode($rs);
        Yii::app()->end();
    }

    /**
     * Send link building task to copypress.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionSend2io()
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

        $i = 0;
        foreach($ids as $id) {
            $m = $model->findByPk($id);
            $rs['task'][$i]['id'] = $id;
            $rs['task'][$i]['feedback'] = "you couldn't send task #{$id} to io again.";
            if ($m && ($m->iostatus == 0 || $m->iostatus == 4)) {
                $m->iostatus = 1;
                if($m->save()){
                    $rs['task'][$i]['feedback'] = "Task #{$id} sent to Io successfully.";
                } else {
                    //print_r($m->attributes);
                    $rs['task'][$i]['feedback'] = "Task #{$id} sent to Io failure.";
                }
            }//else we should contine it.

            $i++;
        }

        $rs['msg'] = "Send tasks to IO were done!";
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
                $m->$attrname = $attrvalue;
                if($m->save()){
                    $rs['task'][$i]['feedback'] = "Set the $attrname of task #{$id} successfully.";
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
            $rtn['msg'] = "Request sent failer, please try it again";
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
