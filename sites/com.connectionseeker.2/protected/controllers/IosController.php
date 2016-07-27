<?php

class IosController extends RController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column1';
    public $iostatus = 0;

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			//'accessControl', // perform access control for CRUD operations
			'rights', // perform access control for CRUD operations
			//'accessOwn + view,update,delete', // perform customize additional access control for CRUD operations
			'accessOverview + index', // perform customize additional access control for CRUD operations
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
        $this->_iov();
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
            //if (!empty($_GET['Task']["duedate"])) $_GET['Task']["duedate"] = strtotime($_GET['Task']["duedate"]);
            $model->attributes=$_GET['Task'];
            if ($model->duedate) $model->duedate = strtotime($model->duedate);
        }
        $model->iostatus = $this->iostatus;

        $uid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($uid);
        if (isset($roles['Marketer'])) {
            $client_id = -1;
            $umodel = User::model()->findByPk($uid);
            if ($umodel) {
                $client_id = $umodel->client_id;
                if ($umodel->duty_campaign_ids) {
                    $model->duty_campaign_ids = unserialize($umodel->duty_campaign_ids);
                }
            }
            //echo $client_id;
            $model->client_id = $client_id;
        } else if(isset($roles['Publisher']) || isset($roles['InternalOutreach']) || isset($roles['Outreach'])){
            $umodel = User::model()->findByPk($uid);
            if ($umodel->channel_id) {
                $model->channel_id = $umodel->channel_id;
            } else {
                $model->channel_id = -1;
            }
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
        if (!empty($_GET['Task']['iostatus'])) {
            $iostr = $_iostatuses[$_GET['Task']['iostatus']]."_";
        }

        $types = Types::model()->bytype(array("linktask","channel"))->findAll();
        $gtps = CHtml::listData($types, 'refid', 'typename', 'type');
        $channels = $gtps['channel'];
        $channelstr = Utils::array2String($channels);

        $tiers = CampaignTask::$tier;
        $tierstr = Utils::array2String($tiers);

        $roles = Yii::app()->authManager->getRoles(Yii::app()->user->id);
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
                    'value' => 'CHtml::encode($data->sourceurl)',
                    'visible' => isVisible('sourceurl', $dparr),
                ),
                array(
                    'name' => 'channel_id',
                    'type' => 'raw',
                    'value' => 'Utils::getValue(' . $channelstr . ', $data->channel_id, true)',
                    'visible' => isVisible('channel_id', $dparr),
                ),
                array(
                    'name' => 'duedate',
                    'type' => 'raw',
                    'value' => '$data->duedate ? date("Y-m-d", $data->duedate) : ""',
                    'visible' => isVisible('duedate', $dparr),
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

        /*
        $types = Types::model()->bytype(array("linktask","channel"))->findAll();
        $gtps = CHtml::listData($types, 'refid', 'typename', 'type');
        //print_r($gtps);
        $linktask = $gtps['linktask'];
        $tasktypestr = Utils::array2String($linktask);

        $channels = $gtps['channel'];
        $channelstr = Utils::array2String($channels);

        $tiers = CampaignTask::$tier;
        $tierstr = Utils::array2String($tiers);

        $pgstatus = Task::$pgstatus;
        $pgstatusstr =  Utils::array2String($pgstatus);

        $isvisible = false;
        if ($iostatus == 1) {
            $isvisible = true;
        }

        $cuid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($cuid);
        $isadmin = false;
        if(isset($roles['Admin'])){
            $isadmin = true;
        }

        $dparr = Utils::taskDisplayMode();

        $this->widget('application.extensions.lkgrid.EExcelView', array(
            'id'=>'task-grid',
            'pageSize'=>$model->search()->getTotalItemCount(),
            'filename'=>date("Y-m-d")."_link_task_io",
            //'customizedata'=>$customizedata,
            'exportType' => $exportType,
            'dataProvider'=>$model->search(),
            'columns'=>array(
                'id',
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
                    'type' => 'raw',
                    'value' => 'CHtml::encode($data->desired_domain)',
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
                    'value' => 'CHtml::encode($data->sourceurl)',
                    'visible' => isVisible('sourceurl', $dparr) && ($iostatus != 1 && $iostatus != 2),
                ),
                array(
                    'name' => 'channel_id',
                    'type' => 'raw',
                    'value' => 'Utils::getValue(' . $channelstr . ', $data->channel_id, true)',
                    'visible' => isVisible('channel_id', $dparr),
                ),

                array(
                    'name' => 'duedate',
                    'type' => 'raw',
                    'value' => '$data->duedate ? date("Y-m-d", $data->duedate) : ""',
                ),

            ),
        ));

        //no need use app end, cause we ended this one in the EExcelView already.
        //Yii::app()->end();
        */
    }

    //Client Request email
    public function actionReason($id = 0)
    {
        set_time_limit(0);
        ini_set("memory_limit", "128M");

        $p = $_REQUEST;
        if (empty($id)) $id = $p['denytaskid'];

        $rtn = array();
        $umodel = User::model()->findByPk(Yii::app()->user->id);

        if (empty($p['denyemail'])) {
            $p['denyemail'] = $umodel->email;
        }
        $email = $p['denyemail'];

        if ($id) {
            $model = Task::model()->findByPk($id);
            /*
            $model = Task::model()->with("rcampaign")->findByPk($id);
            $campaignname = $model->rcampaign->name;
            */
        }

        $username = $umodel->username;
        $subject = "Deny IO #{$id} from " . $username;
        $content = "This deny action had been set by " . $username . " ($email)\n\nDeny Reason As Following:\n";
        $content .= $p['denyreason'];

        $np = array();
        $np['tos'] = array("csdeny@steelcast.com");
        $np['mfrom'] = $email;
        $np['displayname'] = $username;
        $np['content'] = $content;
        $np['subject'] = $subject;
        $np['format'] = "text/plain";

        $c = Utils::notice($np);
        if ($c && $model) {
            //store it into database;
            $rtn['msg'] = "Successful, Please go to the denied to check it out.";
            $rtn['success'] = true;
            $model->iostatus = 4;
            $model->save();
        } else {
            $rtn['msg'] = "Deny with reason failer, please try it again";
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
        if (isset($_GET['pageSize'])) {
            Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
            unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
        }

        $model=new Task('search');
        $model->unsetAttributes();  // clear any default values
		if(isset($_GET['Task'])) {
			$model->attributes=$_GET['Task'];
            if ($model->duedate) $model->duedate = strtotime($model->duedate);
        }

        $uid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($uid);
        $dparr = Utils::taskDisplayMode();

		$this->render('/task/iosindex',array(
			'model'=>$model,
			'roles'=>$roles,
			'dparr'=>$dparr,
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='user-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
