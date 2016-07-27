<?php

class ReportingController extends RController
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
			//'accessControl', // perform access control for CRUD operations
            'rights', // perform access control for CRUD operations
            //'accessOwn + view,update,delete', // perform customize additional access control for CRUD operations
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionCampaigns()
	{
        if (isset($_GET['pageSize'])) {
            Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
            unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
        }

		$model=new Campaign('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Campaign']))
			$model->attributes=$_GET['Campaign'];
        if (!isset($model->ishidden)) $model->ishidden = 0;

        ##############################################4/16/2012#####################################
        $roles = Yii::app()->authManager->getRoles(Yii::app()->user->id);
        if(isset($roles['Marketer'])){
            $umodel = User::model()->findByPk(Yii::app()->user->id);
            if ($umodel) {
                if ($umodel->client_id) {
                    $model->client_id = $umodel->client_id;
                } else {
                    $model->client_id = 0;
                }

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

		$this->render('campaigns',array(
			'model'=>$model,
		));
	}

    /*
    //## current,accepted,pending,approved,pre qa,post qa
    */
	public function actionCampaignDetail($id)
	{
        if (empty($id)) {
            throw new CHttpException(505, 'You need access this module via the right way, please provide campaign id first.');
        }

        if (isset($_GET['pageSize'])) {
            Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
            unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
        }

        $model=new Task('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Task']))
			$model->attributes=$_GET['Task'];
        $model->campaign_id = $id;

        /*
        $model=new Task('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Campaign']))
			$model->attributes=$_GET['Campaign'];
        $model->campaign_id = $id;

        ##############################################4/16/2012#####################################
        $roles = Yii::app()->authManager->getRoles(Yii::app()->user->id);
        if(isset($roles['Marketer'])){
            $umodel = User::model()->findByPk(Yii::app()->user->id);
            if ($umodel) {
                //#########$model->client_id = $umodel->client_id;

                if ($umodel->type != 0) {
                    if ($umodel->duty_campaign_ids) {
                        $model->duty_campaign_ids = unserialize($umodel->duty_campaign_ids);
                        if ($id && !in_array($id, $model->duty_campaign_ids)) {
                            $model->campaign_id = 0;
                        }
                    } else {
                        //that means the client owner/admin didn't assign any campaigns to this user.
                        //so this user will see nothing
                        $model->campaign_id = 0;
                    }
                }
            } else {
                $model->campaign_id = 0;
            }
        }
        ##############################################4/16/2012#####################################
        */

        $where = "";
        if (isset($_GET['Task']['channel_id']) && $_GET['Task']['channel_id']) {
            $where .= " AND channel_id='".$_GET['Task']['channel_id']."'";
        }
        //current,accepted,pending,approved,pre qa,post qa
        $rawdata = Yii::app()->db->createCommand()->select("channel_id, COUNT( if(iostatus='0',true,null) ) AS init_count,
        COUNT( if(iostatus='31',true,null) ) AS qa_count, COUNT( if(iostatus='5',true,null) ) AS published_count,
        COUNT( if(iostatus='2',true,null) ) AS accepted_count, COUNT( if(iostatus='21',true,null) ) AS pending_count,
        COUNT( if(iostatus='3',true,null) ) AS approved_count, COUNT( if(iostatus='32',true,null) ) AS inrepair_count, 
        COUNT( if(iostatus='1',true,null) ) AS current_count, COUNT( if(iostatus!='4',true,null) ) AS total_count")
            ->from('{{inventory_building_task}}')
            //->join('{{types}} c', "c.refid=channel_id")
            //->where("`campaign_id` = $id AND c.type='channel'")
            ->where("`campaign_id` = $id ".$where)
            ->group('channel_id')
            ->queryAll();

        $summaries=new CArrayDataProvider($rawdata, array(
            'id'=>'campaign',
            'sort'=>array(
                'attributes'=>array(
                     //'channel_id', 'qa_count', 'published_count','approved_count','total_count',
                     'channel_id', 'qa_count', 'inrepair_count', 'accepted_count', 'pending_count', 'init_count',
                     'published_count','approved_count','current_count','channel_name','total_count',
                ),
            ),
        ));

        $others = Yii::app()->db->createCommand()
            ->select("COUNT( if(iostatus='0',true,null) ) AS init_count,
                    COUNT( if(iostatus='1',true,null) ) AS current_count,
                    COUNT( if(iostatus='2',true,null) ) AS accepted_count,
                    COUNT( if(iostatus='21',true,null) ) AS pending_count")
            ->from('{{inventory_building_task}}')
            ->where("`campaign_id` = '".$id."'")
            ->queryRow();

        $cmpmodel = CampaignTask::model()->with('rcampaign')->findByAttributes(array('campaign_id' => $id));
		$this->render('campaign_detail',array(
			'model'=>$model,
			'summaries'=>$summaries,
			'others'=>$others,
			'cmpmodel'=>$cmpmodel,
		));
	}

	public function actionChannels()
	{
        if (isset($_GET['pageSize'])) {
            Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
            unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
        }

		$model=new Task('report');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Task']))
			$model->attributes=$_GET['Task'];

        $where = "1";
        if (isset($_REQUEST["groupby"]) && $_REQUEST["groupby"] == "campaign") {
            if ($model->channel_id) {
                $where = "channel_id=:chlid";
            } elseif ($model->channel_id == 0) {
                $where = "(channel_id IS NULL) OR (channel_id = 0)";
            }
        }

        //COUNT( if(iostatus='5',true,null) ) AS completed_count, 
        $others = Yii::app()->db->createCommand()
            ->select("COUNT( if(iostatus='0',true,null) ) AS init_count,COUNT(*) AS total_count,
                    COUNT( if(iostatus='1',true,null) ) AS current_count,
                    COUNT( if(iostatus='2',true,null) ) AS accepted_count,
                    COUNT( if(iostatus='21',true,null) ) AS pending_count,
                    COUNT( if(iostatus='3',true,null) ) AS approved_count,
                    COUNT( if(iostatus='5',true,null) ) AS published_count,
                    COUNT( if(iostatus='4',true,null) ) AS denied_count,
                    COUNT( if( (iostatus='0' OR iostatus='1' OR iostatus='2') ,true,null)) AS remaining_count,
                    COUNT( if(iostatus='32',true,null) ) AS inrepair_count,
                    COUNT( if(iostatus='31',true,null) ) AS qa_count")
            ->from('{{inventory_building_task}} t')
            ->join('{{campaign}} rcampaign', "(rcampaign.id=t.campaign_id AND rcampaign.ishidden != 1)")
            ->where($where, array(":chlid"=>$model->channel_id))
            ->queryRow();
        //Join campaign for getting unhidden campaign's tasks;

		$this->render('channels',array(
			'model'=>$model,
			'others'=>$others,
		));
    }

	public function actionLatestcheck()
	{
        if (isset($_GET['pageSize'])) {
            Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
            unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
        }

        if (!isset($_GET['Crawler_sort'])) $_GET['Crawler_sort'] = "domain_id";

        $model=new Crawler('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['Crawler'])) {
            $model->attributes=$_GET['Crawler'];
        }

        $this->render('latestcheck',array(
            'model'=>$model,
        ));
    }


	public function actionIohistoric()
	{
        if (isset($_GET['pageSize'])) {
            Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
            unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
        }

        //if (!isset($_GET['IoHistoricReporting_sort'])) $_GET['IoHistoricReporting_sort'] = "task_id";
        if (!isset($_GET['IoHistoricReporting_sort'])) $_GET['IoHistoricReporting_sort'] = "date_completed.desc";

		$model=new IoHistoricReporting('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['IoHistoricReporting']))
			$model->attributes=$_GET['IoHistoricReporting'];

		$this->render('iohistoric',array(
			'model'=>$model,
		));
    }

	public function actionRating()
	{
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
        }
        $model->iostatus = 5;

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

        if (isset($roles['Marketer'])) {
            $dparr = Utils::taskDisplayMode(6);
        } else {
            $dparr = Utils::taskDisplayMode();
        }

        $this->render('/reporting/iorating',array(
            'model'=>$model,
            'roles'=>$roles,
            'dparr'=>$dparr,
        ));
    }
}
