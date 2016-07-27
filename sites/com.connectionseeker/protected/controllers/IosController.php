<?php

class IosController extends RController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column1';
    public $iostatus = 0;
    public $rebuild = -1;//##7/10/2014 added for completed rebuild

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			//'accessControl', // perform access control for CRUD operations
			'rights', // perform access control for CRUD operations
			//'accessOwn + view,update,delete', // perform customize additional access control for CRUD operations
			'accessOverview + index,hidden', // perform customize additional access control for CRUD operations
		);
	}

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
            if ($this->action->id == 'hidden') {
                if ($roles['Admin'] || $roles['InternalOutreach']) {
                    //do nothing
                } else {
                    $filterChain->controller->accessDenied();
                    return false;
                }
            }

            $filterChain->run();
        }
    }

	/**
	 * Manage all of the Current IOs.
	 * It will render the "task/ios" page.
	 */
	public function actionCurrent()
	{
        $this->iostatus = 1;
        $this->_iov();
	}

	/**
	 * Manage all of the Accepted IOs.
	 * It will render the "task/ios" page.
	 */
	public function actionAccepted()
	{
        $this->iostatus = 2;
        $this->_iov();
	}

	/**
	 * Manage all of the Pending IOs.
	 * It will render the "task/ios" page.
	 */
	public function actionPending()
	{
        $this->iostatus = 21;
        $this->_iov();
	}

	/**
	 * Manage all of the Completed IOs.
	 * It will render the "task/ios" page.
	 */
	public function actionApproved()
	{
        $this->iostatus = 3;
        $this->_iov();
	}


	/**
	 * Manages all denied entire rows
	 * It will render the "task/ios" view page.
	 */
	public function actionDenied()
	{
        $this->iostatus = 4;
        $this->_iov();
	}

	/**
	 * Manage all of the Completed IOs.
	 * It will render the "task/ios" page.
	 */
	public function actionCompleted()
	{
        $this->iostatus = 5;

        //added 7/10/2014 for seperate io complete rebuild
        $this->rebuild = 0;
        $this->_iov();
	}

	public function actionPreqa()
	{
        $this->iostatus = 31;
        $this->_iov();
	}

	public function actionInrepair()
	{
        $this->iostatus = 32;
        $this->_iov();
	}

	public function actionRebuilt()
	{
        $this->iostatus = 5;
        $this->rebuild = 1;
        $this->_iov();
	}

	public function actionHidden()
	{
        //We use different action name in the view ($this->action->id) to control something/layout.
        $this->actionIndex();
	}

    /**
     * common io views for 
     */
    private function _iov(){
        if (isset($_GET['pageSize'])) {
            Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
            unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
        }

        if (!isset($_GET['Task_sort'])) {
            $_GET['Task_sort'] = "iodate.desc";
        }

        $model=new Task('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['Task'])) {
            $model->attributes=$_GET['Task'];
            //if ($model->duedate) $model->duedate = strtotime($model->duedate);
        }
        $model->iostatus = $this->iostatus;

        $uid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($uid);
        if (isset($roles['Marketer'])) {
            $client_id = -1;
            $umodel = User::model()->findByPk($uid);
            if ($umodel) {
                if ($umodel->client_id) $client_id = $umodel->client_id;
                if ($umodel->duty_campaign_ids) {
                    $model->duty_campaign_ids = unserialize($umodel->duty_campaign_ids);
                }
            }

            $model->client_id = $client_id;
        } else if(isset($roles['Publisher']) || isset($roles['InternalOutreach']) || isset($roles['Outreach'])){
            $umodel = User::model()->findByPk($uid);
            if ($umodel->channel_id) {
                $model->channel_id = $umodel->channel_id;
            } else {
                $model->channel_id = -1;
            }
        }
        if ($this->rebuild >= 0) {
            $model->rebuild = $this->rebuild;
        }

        if (isset($roles['Marketer'])) {
            $dparr = Utils::taskDisplayMode(6);
        } else {
            $dparr = Utils::taskDisplayMode();
        }

        /*
        $model->campaign_id = $campaign_id;
        $cmpmodel = Campaign::model()->findByPk($campaign_id);

        //dispath to different view.
        $cuid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($cuid);
        if(isset($roles['Marketer'])){
            $renderview = "clientprocessing";
        } else {
            $renderview = "processing";
        }
        */

        $this->render('/task/ios',array(
            'model'=>$model,
            'iostatus'=>$this->iostatus,
            'roles'=>$roles,
            'dparr'=>$dparr,
        ));
    }

    /**
     * Download link building tasks/IOs.
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

        $exportType = "CSV";
        if (strtolower($_GET['Task']['export']) == 'csv') {
            $exportType = "CSV";
        }

        $_iostatuses = Task::$iostatuses;
        $iostr = "";
        if (!empty($_GET['Task']['iostatus']) && is_numeric($_GET['Task']['iostatus'])) {
            $iostr = $_iostatuses[$_GET['Task']['iostatus']]."_";
        }

        $types = Types::model()->bytype(array("linktask","channel"))->findAll();
        $gtps = CHtml::listData($types, 'refid', 'typename', 'type');
        $channels = $gtps['channel'];
        $channelstr = Utils::array2String($channels);

        $iostatuses = Task::$iostatuses;
        $iostatuses = $iostatuses + array("500"=>"IO Completed","501"=>"Completed - Rebuild");
        $iostatusesstr =  Utils::array2String($iostatuses);
        unset($iostatuses["5"]);

        $tiers = CampaignTask::$tier;
        $tierstr = Utils::array2String($tiers);

        $uid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($uid);
        if (isset($roles['Marketer'])) {
            $client_id = -1;
            $umodel = User::model()->findByPk($uid);
            if ($umodel) {
                if ($umodel->client_id) $client_id = $umodel->client_id;
                if ($umodel->duty_campaign_ids) {
                    $model->duty_campaign_ids = unserialize($umodel->duty_campaign_ids);
                }
            }
            $model->client_id = $client_id;
        } else if(isset($roles['Publisher']) || isset($roles['InternalOutreach']) || isset($roles['Outreach'])){
            $umodel = User::model()->findByPk($uid);
            if ($umodel->channel_id) {
                $model->channel_id = $umodel->channel_id;
            } else {
                $model->channel_id = -1;
            }
        }

        $ismarketer = false;
        if (isset($roles['Marketer'])) {
            $ismarketer = true;
            $dparr = Utils::taskDisplayMode(6);
        } else {
            $dparr = Utils::taskDisplayMode();
        }

        $this->widget('application.extensions.lkgrid.EExcelView', array(
            'id'=>'task-grid',
            'pageSize'=>$model->search()->getTotalItemCount(),
            'filename'=>"IO_".$iostr.date("Y-m-d"),
            //'customizedata'=>$customizedata,
            'exportType' => $exportType,
            'dataProvider'=>$model->search(),
            'columns'=>array(
                'id',
                array(
                    'header' => 'Client',
                    'name' => 'client_id',
                    'type' => 'raw',
                    'value' => 'CHtml::encode($data->rcampaign->rclient->company)',
                ),
                array(
                    'header' => 'Campaign',
                    'name' => 'campaign_name',
                    'type' => 'raw',
                    'value' => 'CHtml::encode($data->rcampaign->name)',
                ),
                array(
                    'name' => 'iostatus',
                    'type' => 'raw',
                    'value' => '$data->iostatus ? fixRebuildIOStatus(' . $iostatusesstr . ', $data->iostatus, $data->rebuild, true) : ""',
                ),
                array(
                    'name' => 'anchortext',
                    'visible' => isVisible('anchortext', $dparr),
                ),
                array(
                    'name' => 'targeturl',
                    'visible' => isVisible('targeturl', $dparr),
                ),
                array(
                    'name' => 'tierlevel',
                    'type' => 'raw',
                    'value' => 'Utils::getValue(' . $tierstr . ', $data->tierlevel, true)',
                    'visible' => isVisible('tierlevel', $dparr),
                ),
                array(
                    'name' => 'desired_domain',
                    'visible' => isVisible('desired_domain_id', $dparr),
                ),
                array(
                    'name' => 'rewritten_title',
                    'type' => 'raw',
                    'value' => 'CHtml::encode($data->rewritten_title)',
                    'visible' => isVisible('rewritten_title', $dparr),
                ),
                array(
                    'name' => 'sourceurl',
                    'type' => 'raw',
                    'visible' => isVisible('sourceurl', $dparr),
                ),
                array(
                    'name' => 'channel_id',
                    'type' => 'raw',
                    'value' => 'Utils::getValue(' . $channelstr . ', $data->channel_id, true)',
                    'visible' => isVisible('channel_id', $dparr),
                ),
                array(
                    'name' => 'tierlevel_built',
                    'type' => 'raw',
                    'value' => 'Utils::getValue(' . $tierstr . ', $data->tierlevel_built, true)',
                    'visible' => isVisible('tierlevel_built', $dparr),
                ),
                array(
                    'name' => 'livedate',
                    'type' => 'raw',
                    'value' => 'CHtml::encode($data->livedate)',
                    'visible' => isVisible('livedate', $dparr),
                ),
                array(
                    'name' => 'iodate',
                    'type' => 'raw',
                    'value' => 'CHtml::encode($data->iodate)',
                    'visible' => isVisible('iodate', $dparr),
                ),
                array(
                    'name' => 'duedate',
                    'type' => 'raw',
                    'value' => '$data->duedate ? date("Y-m-d", strtotime($data->duedate)) : ""',
                    'visible' => isVisible('duedate', $dparr),
                ),
                array(
                    'name' => 'spent',
                    'type' => 'raw',
                    'value' => ($iostatus == 3 || $iostatus == 5) ? '"$".CHtml::textField("spent[]", $data->spent, array("size"=>"6"))' : '$data->spent',
                    'visible' => isVisible('spent', $dparr) && (isset($roles['Admin']) || isset($roles['InternalOutreach'])),
                ),
                array(
                    'name' => 'rebuild',
                    'type' => 'raw',
                    'value' => '$data->rebuild ? "True" : "False"',
                ),
                array(
                    'name' => 'rsummary.mozauthority',
                    'header' => 'DA',
                    'type' => 'raw',
                    'visible' => 'in_array($data->iostatus, array(3,5,31,32))',
                ),
                /*
                'rewritten_title',
                'domain',
                'anchortext',
                'targeturl',
                'desired_domain',
                'sourceurl',
                */
            ),
        ));

        //no need use app end, cause we ended this one in the EExcelView already.
        //Yii::app()->end();
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
        if (strtolower($denyaction) == "repair") {
            $subject = $username . " move IO #{$id} in repair.";
            $content = $subject . "\r\n";
            $_notes = "Email From:".$email."\r\nRepair Reason: ".$p['denyreason'];
        } else if (strtolower($denyaction) == "rewindinapprove") {
            $subject = "#".$model->id." Rewind";
            $content = "#".$model->id." was just rewound with a reason of:\r\n";
        } else {
            $subject = "Deny IO #{$id} from " . $username . " for " . $model->desired_domain;
            if ($model) {
                $content = "This deny action of '#{$id}' with Anchor Text of '".$model->anchortext.
                        "' and TargetURL of '".$model->targeturl."' from Campaign '{$campaignname}' has been set by " .
                       $username . " ($email)\n\nDeny Reason As Following:\n";
            } else {
                $content = "This deny action had been set by " . $username . " ($email)\n\nDeny Reason As Following:\n";
            }
            $_notes = "Email From:".$email."\r\nDeny With Reason: ".$p['denyreason'];
        }
        $content .= $p['denyreason'];

        $np = array();
        //$np['tos'] = array("csdeny@steelcast.com");
        if (strtolower($denyaction) == "rewindinapprove") {
            //if we rewind this IO task in Apporve page, then we need send this email to myself.
            $np['tos'] = (array) $umodel->email;
        } else {
            if (isset($responseemail)) {
                //##$np['tos'] = array("csclientissues@steelcast.com","twyher@copypress.com","$responseemail");
                $np['tos'] = array("csclientissues@steelcast.com","$responseemail");
            } else {
                $np['tos'] = array("csclientissues@steelcast.com");
            }
        }

        $np['mfrom'] = $email;
        $np['displayname'] = $username;
        $np['content'] = $content;
        $np['subject'] = $subject;
        $np['format'] = "text/plain";

        $c = Utils::notice($np);
        if ($c && $model) {
            //store it into database;
            $rtn['success'] = true;
            $oldiostatus = $model->iostatus;

            if (strtolower($denyaction) == "repair") {
                $model->iostatus = 32;
                $rtn['msg'] = "Successful, Please check it out.";
            } else if (strtolower($denyaction) == "rewindinapprove") {
                $model->iostatus = 2;
                $rtn['msg'] = "Successful, Please check it out.";
            } else {
                if ($model->iostatus == 1) {
                    $model->iostatus = 4;
                    $rtn['msg'] = "Successful, Please go to the denied to check it out.";
                } else {
                    $model->iostatus = 1;
                    $model->desired_domain = null;
                    $model->desired_domain_id = 0;
                    $rtn['msg'] = "Deny Reason sent Successful.";
                }
            }

            $model->save();

            if (strtolower($denyaction) != "rewindinapprove") {
                $notemodel=new TaskNote;
                $notemodel->setIsNewRecord(true);
                $notemodel->id = NULL;
                $notemodel->task_id = $id;
                $notemodel->notes = $_notes;
                $notemodel->hidefromclient = 1;
                $notemodel->save();
            }

            $iomodel = new Iohistory;
            $iomodel->setIsNewRecord(true);
            $iomodel->id       = NULL;
            $iomodel->oldiostatus = $oldiostatus;
            $iomodel->iostatus = $model->iostatus;
            $iomodel->task_id  = $id;
            $iomodel->reason   = $p['denyreason'];
            if ($model->iostatus == 1 || empty($model->iodate)) {
                $iomodel->created = $model->created;//the tbl.io_history.created will be reset in beforeValidate
            } else {
                $iomodel->created = $model->iodate;//the tbl.io_history.created will be reset in beforeValidate
            }
            $iomodel->save();
        } else {
            $rtn['msg'] = "Deny with reason failure, please try it again";
            $rtn['success'] = false;
        }

        echo CJSON::encode($rtn);
        Yii::app()->end();
    }

	/**
	 * Manages all models.
	 */
	public function actionIndex()
	{
        //##$this->_iov();

        if (isset($_GET['pageSize'])) {
            Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
            unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
        }

        $model=new Task('search');
        $model->unsetAttributes();  // clear any default values
		if(isset($_GET['Task'])) {
			$model->attributes=$_GET['Task'];
            //#if ($model->duedate) $model->duedate = strtotime($model->duedate);
        }

        $uid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($uid);
        $dparr = Utils::taskDisplayMode();

        if(isset($roles['InternalOutreach']) && $this->action->id == "hidden"){
            $umodel = User::model()->findByPk($uid);
            if ($umodel->channel_id) {
                $model->channel_id = $umodel->channel_id;
            } else {
                $model->channel_id = -1;
            }
        }

		$this->render('/task/iosindex',array(
			'model'=>$model,
			'roles'=>$roles,
			'dparr'=>$dparr,
		));
	}

    //Get Desired Domains IO task List
    public function actionHistoric($domain_id)
    {
        $model = new Task;
        $model->unsetAttributes();  // clear any default values

        $reasons = array();
        if ($_GET["currentaction"] != 'denied') {
            $model->desired_domain_id = $_GET["domain_id"]; 
            if (!isset($_GET['Task_sort'])) {
                $_GET['Task_sort'] = "iodate.desc";
            }
            Yii::app()->user->setState('pageSize', 20);
        } else {
            $_nvs = array();
            $_nvs[0] = 's:8:"iostatus";i:1;';
            $_nvs[1] = 's:8:"iostatus";s:1:"1";';
            $_nvs[2] = 's:8:"iostatus";i:4;';
            $_nvs[3] = 's:8:"iostatus";s:1:"4";';
            $condition = "(t.new_value LIKE '%".implode("%') OR (t.new_value LIKE '%", $_nvs)."%')";

            $desired_domain = trim($_GET["desired_domain"]);
            $s = 's:14:"desired_domain";s:'.strlen($desired_domain).':"'.$desired_domain.'";';
            $s = "(t.old_value LIKE '%{$s}%')";
            $ios = Yii::app()->db->createCommand()->select('t.user_id,t.model_id,t.created, u.username')
                ->from('{{operation_trail}} t')
                ->join('{{user}} u', 'u.id=t.user_id')
                ->where("($condition) AND t.model = 'Task' AND $s")
                ->order('t.id ASC')
                ->queryAll();

            $model->id = -1;//set default search values;
            if ($ios) {
                $ids = array();
                $ihmodel = new Iohistory;
                foreach($ios as $iv) {
                    $ids[$iv["model_id"]] = $iv["model_id"];
                    $reasons[$iv["model_id"]]["createdby"] = $iv["username"];
                    $ihmdl = $ihmodel->findByAttributes(array('task_id'=>$iv["model_id"],'created'=>$iv["created"],
                                                     'created_by'=>$iv["user_id"], 'iostatus'=>array(1,4) ));
                    if (!empty($ihmdl)) {
                        $reasons[$iv["model_id"]]["reason"] = addslashes($ihmdl->reason);
                    }
                }
                $model->id = array_values($ids);
            }
        }
        if ($_GET["currentaction"] == 'bloggerprogram') {
            $renderview = "/task/ioshistoric4bp";
        } else {
            $renderview = "/task/ioshistoric";
        }

        //####$this->renderPartial('/task/ioshistoric',array(
        $this->renderPartial($renderview,array(
            'model'=>$model,
            'reasons'=>$reasons,
            'currentaction'=>$_GET["currentaction"],
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='user-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
