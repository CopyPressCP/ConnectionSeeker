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
                    if ($keywords) {
                        foreach($keywords as $k=> $v) {
                            //$_kws[$v['keyword']] = $v['kwcount']." - ".$v['keyword'];
                            $_kws[$k] = $v['kwcount']." - ".$v['keyword'];
                        }
                    }
                    $targeturls = unserialize($cmptask->targeturl);
                    if ($targeturls) {
                        foreach($targeturls as $k=> $v) {
                            //$_urls[$v['targeturl']] = $v['targeturl'];
                            $_urls[$k] = $v['targeturl'];
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
                                    $p[$av] = $_urls[$_POST[$av.$pk][$_i]];
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

        $model = new Task;
        $cpmodel = New CopypressCampaign;
        $created = time();
        $month = date('Ym', $created);

        $styleguide = Utils::preference("styleguide");
        if ($styleguide === false) {
            $rs['msg'] = "Preferences File Is Invalid, Please contact system admin.";
            $rs['success'] = false;
            echo CJSON::encode($rs);
            Yii::app()->end();
        }

        $i = 0;
        foreach($ids as $id) {
            $m = $model->findByPk($id);
            $rs['task'][$i]['id'] = $id;
            $rs['task'][$i]['feedback'] = "No related campaign there.";
            if ($m && $m->tasktype == 1 && empty($m->content_article_id)) {
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
                    $cp->client_id = $m->client_id;
                    $cp->campaign_id = $m->campaign_id;
                    $cp->content_category_id = $m->content_category_id;
                    $cp->content_campaign_name = date('FY', $created) . "_" . $cmptask->rcampaign->name;
                    //$cp->notes = $m->style_guide;
                    $cp->notes = $styleguide;
                    $cp->month = $month;

                    $ccmp = array('campaignname' => $cp->content_campaign_name,
                                  'contentcategory_id' => $cp->content_category_id,
                                  'datestart' => date('Y-m-d', $created),
                                  'campaignrequirement' => $cp->notes,
                                  'dateend' => date('Y-m-d', $m->duedate));
                    //cause the Yii use the lazy load strategy, so we need put the method into a class
                    $response = Utils::sendCmd2SSSAPI("createcampaign", $ccmp);
                    //var_dump($response);
                    if ($response->isSuccessful()) {
                        $responsebody = $response->getBody();
                        //echo $responsebody;
                        $rbodys = simplexml_load_string(utf8_encode($responsebody));
                        $contentcampaignid = $rbodys->campaignstatus->campaignid;
                        $contentcampaignid = (int)$contentcampaignid;
                        $cp->content_campaign_id = $contentcampaignid;
                        if (!$cp->save()) {
                            //throw new CHttpException(401,'Content Campaign create failure.');
                            $rs['task'][$i]['feedback'] = "Create content campaign failure.";
                            $rs['task'][$i]['content_article_id'] = 0;
                            $i++;
                            continue;
                        }
                    }
                }
                $m->content_campaign_id = $contentcampaignid;

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
            'pageSize'=>$model->search()->getItemCount(),
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
