<?php
/*
* This is New version for content IO
* In the Main content IO section we will have 6 pages
*
* Ideation, Idea Approval, Place Order(Same as To Order. but change To to Place), Ordered (Currently Final Approval),
*   Content Approval (Also Currently Final Approval Duplicate), Delivered(Currently Completed).
*/
class ContentStepController extends RController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column1';
    public $content_step = 0;

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
//#			'accessControl', // perform access control for CRUD operations
			'rights', // perform access control for CRUD operations
			//'accessOwn + view,update,delete', // perform customize additional access control for CRUD operations
//#			'accessOverview + index', // perform customize additional access control for CRUD operations
		);
	}

    /*
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform the following actions
				'actions'=>array('step1','step2','step3','step4','step5','step6','step7','updatestep','setattr','getattr','reason','download'),
				//'users'=>array('*'),
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
    */

	/**
	 * @return array action filters
     * We can build one filter file, and put this function into the filter file
	 */
    /*
    public function filterAccessOwn($filterChain) {
        $allow = true;

        $cuid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($cuid);

        if(isset($roles['Publisher'])){
            //Do some stuff first, 
            if ($_GET['id']) {
                $model=$this->loadModel($_GET['id']);
                if ($model->user_id == Yii::app()->user->id) {
                    $filterChain->run();
                } else {
                    $allow = false;
                }
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
    */

    //The Overview feature only open to the internal user for now.
    public function filterAccessOverview($filterChain) {
        //$allow = true;

        $cuid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($cuid);

        if(isset($roles['Publisher']) || isset($roles['Marketer'])){
            $filterChain->controller->accessDenied();
            return false;
        } else {
            $filterChain->run();
        }
    }

	/**
	 * Manage all of the Content Step1 --> Ideation
	 * It will render the "task/newcontentstep" page.
     * Ideation page can be seen by Admins and InternalOutreach.
     *    Admins can see ALL tasks. SO that means it will be initail value 0
     *    InternalOutreach can see all tasks where they are the Channel on that task ID AND IO Status is Pending, Approved.
     *
	 */
	public function actionStep1()
	{
        $this->content_step = 0;
        $this->_contentstep();
	}

	/**
	 * Manage all of the Accepted ContentStep IOs.  --> Idea Approval
	 * It will render the "task/newcontentstep" page.
	 */
	public function actionStep2()
	{
        $this->content_step = 1;
        $this->_contentstep();
	}

	/**
	 * Manage all of the Accepted IOs. --> Place Order
	 * It will render the "task/newcontentstep" page.
	 */
	public function actionStep3()
	{
        $this->content_step = 2;
        $this->_contentstep();
	}

	/**
	 * Manage all of the Accepted IOs. --> Ordered
	 * It will render the "task/contentstep" page.
	 */
	public function actionStep4()
	{
        $this->content_step = 3;
        $this->_contentstep();
	}

	/**
	 * Manage all of the Accepted IOs. --> Content Approval
	 * It will render the "task/newcontentstep" page.
	 */
	public function actionStep5()
	{
        $this->content_step = 4;
        $this->_contentstep();
	}

	/**
	 * Manage all of the Accepted IOs. --> Delivered
	 * It will render the "task/newcontentstep" page.
	 */
	public function actionStep6()
	{
        $this->content_step = 5;
        $this->_contentstep();
	}

	/**
	 * Manage all of the Accepted IOs. --> Completed
	 * It will render the "task/newcontentstep" page.
	 */
	public function actionStep7()
	{
        throw new CHttpException(404,'The requested page does not exist.');
        /*
        $this->content_step = 6;
        $this->_contentstep();
        */
	}

    /**
     * common content step views for 
     */
    private function _contentstep(){
        if (isset($_GET['pageSize'])) {
            Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
            unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
        }

        if (!isset($_GET['Task_sort'])) {
            $_GET['Task_sort'] = "step_date.desc";
        }

        $model=new Task('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['Task'])) {
            $model->attributes=$_GET['Task'];
        }
        $model->content_step = $this->content_step;

        $uid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($uid);
        $umodel = User::model()->findByPk($uid);
        if (isset($roles['Marketer'])) {
            $client_id = -1;
            //$umodel = User::model()->findByPk($uid);
            if ($umodel) {
                if ($umodel->client_id) $client_id = $umodel->client_id;
                if ($umodel->duty_campaign_ids) {
                    $model->duty_campaign_ids = unserialize($umodel->duty_campaign_ids);
                }
            }
            $model->client_id = $client_id;
        //##} else if(isset($roles['Publisher']) || isset($roles['InternalOutreach']) || isset($roles['Outreach'])){
        } else if(isset($roles['Publisher']) || isset($roles['Outreach'])){
            //$umodel = User::model()->findByPk($uid);
            if ($umodel->channel_id) {
                $model->channel_id = $umodel->channel_id;
            } else {
                $model->channel_id = -1;
            }

            //##if ($this->content_step == 0) $model->iostatus = array(3,21);

        } else if(isset($roles['Editor'])){
            $model->content_step_editor = $uid;
        }

        /*
        if (isset($roles['Marketer'])) {
            $dparr = Utils::taskDisplayMode(6);
        } else {
            $dparr = Utils::taskDisplayMode();
        }
        */

        $um_chl_id = ($umodel->channel_id) ? $umodel->channel_id : -1;
        $this->render('/task/newcontentstep',array(
            'model'=>$model,
            'content_step'=>$this->content_step,
            'roles'=>$roles,
            'um_chl_id'=>$um_chl_id,
            //'dparr'=>$dparr,
        ));
    }

	/**
	 * Manages all models.
	 */
	public function actionIndex()
	{
        $uid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($uid);

        if (isset($roles['Admin']) || isset($roles['InternalOutreach'])) {
            //$this->redirect(array('step1'));
            if (isset($_GET['pageSize'])) {
                Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
                unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
            }

            if (!isset($_GET['Task_sort'])) {
                $_GET['Task_sort'] = "step_date.desc";
            }

            $model=new Task('search');
            $model->unsetAttributes();  // clear any default values
            if(isset($_GET['Task'])) {
                $model->attributes=$_GET['Task'];
            }

            $umodel = User::model()->findByPk($uid);
            if (isset($roles['InternalOutreach'])) {
                if ($umodel->channel_id) {
                    $model->channel_id = $umodel->channel_id;
                } else {
                    $model->channel_id = -1;
                }
            }

            $um_chl_id = ($umodel->channel_id) ? $umodel->channel_id : -1;
            $this->render('/task/stepindex',array(
                'model'=>$model,
                'roles'=>$roles,
                'um_chl_id'=>$um_chl_id,
            ));
        } else if (isset($roles['Marketer'])) {
            $this->redirect(array('step4'));
        } else if(isset($roles['Editor'])){
            $this->redirect(array('step3'));
        } else {
            $this->redirect(array('step1'));
        }
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdatestep($task_id)
	{
        $taskmodel = Task::model()->findByPk($task_id);
		if($taskmodel===null)
			throw new CHttpException(404,'The requested page does not exist.');

		//###$model=$this->loadModel($id);
        $mdl = new ContentStep;
        $model = $mdl->findByAttributes(array('task_id' => $task_id));
		//($model);
		$isnewrecord = false;
        if (!$model) {
            $model = $mdl;
			$model->setIsNewRecord(true);
			$model->id = NULL;
            $model->task_id = $task_id;
        }

        if (empty($model->step_title)) $model->step_title = $taskmodel->rewritten_title;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['ContentStep']))
		{
			$model->attributes=$_POST['ContentStep'];
            if ($model->save()) {
				//print_r($model->attributes);
				//print_r($model->getErrors());
			}
		}

        $uid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($uid);

		$this->renderPartial('/task/newcontentstepform',array(
			'model'=>$model,
			'targeturl'=>$taskmodel->targeturl,
			'content_step'=>$taskmodel->content_step,
			'roles'=>$roles,
		));
        Yii::app()->end();
	}

    /**
     * Updates a particular model.
     * AJAX Updately the attributes
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionSetattr($id = 0)
    {
        if ($id) {
            $model=$this->loadModel($id);
        } else {
            if (!empty($_GET['task_id'])) {
                $task_id = $_GET['task_id'];
                $taskmodel = Task::model()->findByPk($task_id);
                if($taskmodel===null)
                    throw new CHttpException(404,'The requested page does not exist.');

                $mdl = new ContentStep;
                $model = $mdl->findByAttributes(array('task_id' => $task_id));
                if (!$model) {
                    $model = $mdl;
                    $model->task_id = $task_id;
                }
            } else {
                throw new CHttpException(404,'The requested page does not exist.');
            }
        }

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
        $rs = array('success' => true, 'msg' => Yii::t('ContentStep', 'Updated was Successful.'));

        if(isset($_GET['attrname']) && isset($_GET['attrvalue']))
        {
            $attrname = str_replace("[]", "", $_GET['attrname']);
            $model->$attrname = $_GET['attrvalue'];

            if($model->save()) {
                //do nothing;
            } else {
                $rs['success'] = false;
                $rs['msg'] = Yii::t('ContentStep', 'Updated was Failure.');
            }
        }

        echo CJSON::encode($rs);

        Yii::app()->end();
    }

    public function actionGetattr($id = 0)
    {
        $attrname = str_replace("[]", "", $_GET['attrname']);
        $rs = array('success' => false, "$attrname" => "",
            'msg' => Yii::t('ContentStep', 'The requested task does not exist.'));

        if ($id) {
            $model=$this->loadModel($id);
            if ($model) {
                $rs[$attrname] = $model->$attrname;
                $rs['success'] = true;
                $rs['msg'] = Yii::t('ContentStep', 'Data was downloaded.');
            }
        } else {
            if (!empty($_GET['task_id'])) {
                $task_id = $_GET['task_id'];
                $taskmodel = Task::model()->findByPk($task_id);
                if($taskmodel===null) {
                    //do nothing;
                } else {
                    $mdl = new ContentStep;
                    $model = $mdl->findByAttributes(array('task_id' => $task_id));
                    if ($model) {
                        //right now we set the step_title=rewritten_title, so we can use rewritten_title directly to fix old data. 
                        $rs[$attrname] = $model->$attrname;
                        if ($attrname == 'step_title') {
                            $rs[$attrname] = $taskmodel->rewritten_title;
                        }
                        $rs['success'] = true;
                        $rs['msg'] = Yii::t('ContentStep', 'Data was downloaded.');
                    } else {
                        if ($attrname == 'step_title') {
                            $rs[$attrname] = $taskmodel->rewritten_title;
                            $rs['success'] = true;
                            $rs['msg'] = Yii::t('ContentStep', 'Data was downloaded.');
                        }
                    }
                }
            } else {
                //do nothing;
            }
        }

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
        echo CJSON::encode($rs);

        Yii::app()->end();
    }

    public function actionRevision($id = 0)
    {
        if (!$id) {
            $id = $_POST['revisiontaskid'];
        }

        $model = Task::model()->findByPk($id);
        $rtn['msg'] = "Revision Ideation with reason success";
        $rtn['success'] = true;
        if ($model && $_POST['revisionreason']) {
            $oldstep = $model->content_step;
            $model->content_step = 4;
            $model->step_date    = date("Y-m-d H:i:s");
            $model->save();

            $snmodel = new StepNote;
            $snmodel->setIsNewRecord(true);
            $snmodel->id = NULL;
            $snmodel->task_id = $id;
            $snmodel->notes=$_POST['revisionreason'];
            $snmodel->type=1;
            if ($snmodel->save()) {
                //do nothing for now;
            } else {
                $rtn['msg'] = "Revision Ideation with reason failure, please try it again";
                $rtn['success'] = false;
            }

            $shmodel = new Stephistory;
            $shmodel->setIsNewRecord(true);
            $shmodel->id       = NULL;
            $shmodel->oldvalue = $oldstep;
            $shmodel->newvalue = $model->content_step;
            $shmodel->task_id  = $id;
            $shmodel->reason   = $p['revisionreason'];
            $shmodel->created  = $model->step_date;//the tbl.content_step_history.created will be reset in beforeValidate
            $shmodel->save();
        }

        echo CJSON::encode($rtn);
        Yii::app()->end();
    }

    //Client Request email
    public function actionReason($id = 0)
    {
        set_time_limit(0);
        ini_set("memory_limit", "128M");

        $p = $_REQUEST;
        if (empty($id)) $id = $p['denytaskid'];
        $denyaction = $p["denyaction"];

        $rtn = array();
        $umodel = User::model()->findByPk(Yii::app()->user->id);

        if (empty($p['denyemail'])) {
            $p['denyemail'] = $umodel->email;
        }
        $email = $p['denyemail'];

        if ($id) {
            //$model = Task::model()->findByPk($id);

            $model = Task::model()->with("rcampaign")->findByPk($id);
            $campaignname = $model->rcampaign->name;

            if ($model->channel_id > 0) {
                $toumdl = User::model()->findByAttributes(array('channel_id'=>$model->channel_id));
                if ($toumdl && !empty($toumdl->email)) {
                    $responseemail = $toumdl->email;
                }
            }
        }

        $username = $umodel->username;

        $subject = "Deny Contet Step #{$id}(Task ID) from " . $username;
        if ($model) {
            $content = "This deny action of Task ID '#{$id}' with Anchor Text of '".$model->anchortext.
                    "' and TargetURL of '".$model->targeturl."' from Campaign '{$campaignname}' has been set by " .
                   $username . " ($email)\n\nDeny Reason As Following:\n";
        } else {
            $content = "This deny action had been set by " . $username . " ($email)\n\nDeny Reason As Following:\n";
        }
        $_notes = "Email From:".$email."\r\nDeny With Reason: ".$p['denyreason'];
        $content .= $p['denyreason'];

        $np = array();
        $np['tos'] = array("csclientissues@steelcast.com","twyher@copypress.com");
        if (isset($responseemail)) {
            //$np['tos'] = array("csclientissues@steelcast.com","twyher@copypress.com","$responseemail");
            array_push($np['tos'], $responseemail);
        }
        if ($model->content_step_editor>0) {
            $editorumdl = User::model()->findByPk($model->content_step_editor);
            if ($editorumdl && !empty($editorumdl->email)) {
                array_push($np['tos'], $editorumdl->email);
            }
        }

        $np['mfrom'] = $email;
        $np['displayname'] = $username;
        $np['content'] = $content;
        $np['subject'] = $subject;
        $np['format'] = "text/plain";

        //print_r($np);
        $c = Utils::notice($np);
        if ($c && $model) {
            //store it into database;
            /*
            $denyvalue = 3;
            if ($model->content_step == 6) {
                $denyvalue = 5;
            }
            if ($model->content_step > 1) {
                $denyvalue = $model->content_step - 1;
            } else {
                $denyvalue = 3;
            }
            */
            $denyvalue = 0;
            //##if ($model->content_step >= 5) $denyvalue = $model->content_step - 1;

            $rtn['success'] = true;
            $rtn['msg'] = "Deny Reason sent Successful.";

            $oldstep = $model->content_step;
            $model->content_step = $denyvalue;
            $model->step_date    = date("Y-m-d H:i:s");
            $model->save();

            $shmodel = new Stephistory;
            $shmodel->setIsNewRecord(true);
            $shmodel->id       = NULL;
            $shmodel->oldvalue = $oldstep;
            $shmodel->newvalue = $model->content_step;
            $shmodel->task_id  = $id;
            $shmodel->reason   = $p['denyreason'];
            $shmodel->created  = $model->step_date;//the tbl.content_step_history.created will be reset in beforeValidate
            $shmodel->save();
        } else {
            $rtn['msg'] = "Deny with reason failure, please try it again";
            $rtn['success'] = false;
        }

        echo CJSON::encode($rtn);
        Yii::app()->end();
    }

    /**
     * Download link building tasks/content step part.
     */
    public function actionDownload()
    {
        set_time_limit(3600);
        ini_set("memory_limit", "512M");
        //$this->layout = '';

        $model=new Task('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['Task'])) {
            if (isset($_GET['Task']['id']) && strpos($_GET['Task']['id'], ",") !== false) {
                $_GET['Task']['id'] = explode(",", $_GET['Task']['id']);
            }
            $model->attributes=$_GET['Task'];
        }

        $uid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($uid);
        $umodel = User::model()->findByPk($uid);
        if (isset($roles['InternalOutreach'])) {
            if ($umodel->channel_id) {
                $model->channel_id = $umodel->channel_id;
            } else {
                $model->channel_id = -1;
            }
        }

        $exportType = "Excel5";
        if (strtolower($_GET['Task']['export']) == 'csv') {
            $exportType = "CSV";
        }

        $types = Types::model()->bytype(array("channel",'category'))->findAll();
        $gtps = CHtml::listData($types, 'refid', 'typename', 'type');
        $channels = $gtps['channel'] ? $gtps['channel'] : array();
        $channelstr = Utils::array2String($channels);
        $categories = $gtps['category'] ? $gtps['category'] : array();
        $categorystr = Utils::array2String($categories);
        $tiers = CampaignTask::$tier;
        $tierstr = Utils::array2String($tiers);

        $this->widget('application.extensions.lkgrid.EExcelView', array(
            'id'=>'task-grid',
            'pageSize'=>$model->contentio()->search()->getTotalItemCount(),
            'filename'=>date("Y-m-d")."_content_step",
            //'customizedata'=>$customizedata,
            'exportType' => $exportType,
            'dataProvider'=>$model->contentio()->search(),
            'columns'=>array(
                array(
                    'name' => 'id',
                    'header' => 'Task ID',
                ),
                array(
                    'name' => 'rcampaign.client_id',
                    'value' => '$data->rcampaign->rclient->name ." (". $data->rcampaign->rclient->company .")"',
                    'header' => 'Client',
                ),
                array(
                    'header' => 'Campaign',
                    'name' => 'rcampaign.name',
                    'type' => 'raw',
                ),
                /*
                array(
                    'name' => 'campaign_id',
                    'type' => 'raw',
                    'value' => 'CHtml::encode($data->rcampaign->name)." - ".($data->rcampaign->rcampaigntask->internal_done)* 100 ."%"',
                ),
                'rstep.step_title',
                */
                array(
                    'header' => 'Channel',
                    'name' => 'channel_id',
                    'type' => 'raw',
                    'value' => 'Utils::getValue(' . $channelstr . ', $data->channel_id, true)',
                ),
                array(
                    'name' => 'tierlevel',
                    'type' => 'raw',
                    'value' => 'Utils::getValue(' . $tierstr . ', $data->tierlevel, true)',
                    'visible' => isVisible('tierlevel', $dparr),
                ),
                /*
                array(
                    'header' => 'Word Count',
                    'name' => 'tierlevel',
                    'type' => 'raw',
                    'value' => 'Utils::getTierWordCount($data->tierlevel, true)',
                ),
                */
                array(
                    'header' => 'Title',
                    'name' => 'rstep.step_title',
                    'type' => 'raw',
                    'value' => '$data->id . " - " . $data->rstep->step_title',
                ),
                'rstep.direction',
                'rstep.resource_link_1',
                'rstep.resource_link_2',
                'rstep.resource_link_3',
                array(
                    'name' => 'desired_domain',
                    'type' => 'raw',
                    'visible' => isVisible('desired_domain_id', $dparr),
                ),
                'anchortext',
                'targeturl',
                array(
                    'header' => 'Website Example',
                    'name' => 'rstep.step_domain',
                ),
                'rstep.client_comment',
                'rstep.extra_writer_note',

                /*
                array(
                    'header' => 'Client Comment',
                    'name' => 'rstepnote2.notes',
                    'type' => 'raw',
                ),
                array(
                    'header' => 'Website Category',
                    'name' => 'content_category_id',
                    'type' => 'raw',
                    'value' => 'Utils::getValue(' . $categorystr . ', $data->content_category_id, true)',
                ),
                array(
                    'header' => 'Extra Writer Notes',
                    'name' => 'rstepnote3.notes',
                    'type' => 'raw',
                ),
                */
            ),
        ));

        //no need use app end, cause we ended this one in the EExcelView already.
        //Yii::app()->end();
    }

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=ContentStep::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='user-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
