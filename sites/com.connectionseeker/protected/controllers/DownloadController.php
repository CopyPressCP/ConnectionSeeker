<?php
/**
* About the download & export feature, we have 2 ways,
* 1.Put the download & export action into the specific/corresponding controller. in other words if we wanna download tasks
* then we put the download action into the TaskController, i.e.: Create one method "public function actionDownload()"
* so in this case you can access the download feature via http://www.connectionseeker.com/task/download
*
* 2.Create one DownloadController, which will handle all of the the download actions in this controller. so if you wanna 
* download tasks, then you just need create one publich method in this controller, named like : public function actionTask()
* then you can access this download feature via http://www.connectionseeker.com/download/task
* @authour: leo@infinitenine.com 6/7/2012
*/
class DownloadController extends RController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using one-column layout. See 'protected/views/layouts/column1.php'.
     * acctually we no need use layout for the download actions
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
			//'rights', // perform access control for CRUD operations
			//'accessOwn + task,inventory', // perform customize additional access control for CRUD operations
		);
	}

    /**
    * We built this method already in the TaskController.php
    */
	public function actionTask()
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

        $filename = date("Y-m-d")."_link_task";
        if (isset($_GET['Task']['campaign_id']) && $_GET['Task']['campaign_id']) {
            $cmpmodel = Campaign::model()->findByPk($_GET['Task']['campaign_id']);
            $filename = $cmpmodel->name . "-". date("Y-m-d");
        }

        $types = Types::model()->bytype(array("linktask","channel"))->findAll();
        $gtps = CHtml::listData($types, 'refid', 'typename', 'type');
        //print_r($gtps);
        $linktask = $gtps['linktask'] ? $gtps['linktask'] : array();
        $tasktypestr = Utils::array2String($linktask);

        $channels = $gtps['channel'] ? $gtps['channel'] : array();
        $channelstr = Utils::array2String($channels);

        $tiers = CampaignTask::$tier;
        $tierstr = Utils::array2String($tiers);

        $taskstatus = Task::$status;
        $taskstatusstr =  Utils::array2String($taskstatus);

        $carts = Cart::model()->findAllByAttributes(array('client_domain_id'=>$cmpmodel->domain_id));
        $cartdomains = CHtml::listData($carts, 'domain_id', 'domain');
        $desiredstr =  Utils::array2String($cartdomains);

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

        //###$roles = Yii::app()->authManager->getRoles(Yii::app()->user->id);
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
            'filename'=>$filename,
            //'customizedata'=>$customizedata,
            'exportType' => $exportType,
            'dataProvider'=>$model->search(),
            'columns'=>array(
                'id',
                array(
                    'name' => 'content_article_id',
                    'visible' => isVisible('content_article_id', $dparr),
                ),
                array(
                    'name' => 'tasktype',
                    'type' => 'raw',
                    'value' => 'Utils::getValue(' . $tasktypestr . ', $data->tasktype, true)',
                    'visible' => isVisible('tasktype', $dparr),
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

                //Here you need pay attention to ($data->desired_domain_id ? "D:".$data->desired_domain_id : "C:".$data->channel_id)
                array(
                    'name' => 'desired_domain_id',
                    'type' => 'raw',
                    //'value' => 'Utils::getValue(' . $desiredstr . ', $data->desired_domain_id ? "D:".$data->desired_domain_id : "C:".$data->channel_id, true)',
                    'value' => '$data->desired_domain_id ? Utils::getValue(' . $desiredstr . ', $data->desired_domain_id, true) : Utils::getValue(' . $channelstr . ', $data->channel_id, true) ',
                    'visible' => isVisible('desired_domain_id', $dparr),
                ),
                array(
                    'name' => 'channel_id',
                    'type' => 'raw',
                    'value' => 'Utils::getValue(' . $channelstr . ', $data->channel_id, true)',
                    'visible' => isVisible('channel_id', $dparr),
                ),
                array(
                    'name' => 'sourceurl',
                    'type' => 'raw',
                    //###'value' => '($ismarketer && $data->iostatus != 5) ? "" : CHtml::encode($data->sourceurl)',
                    'value' => '($ismarketer && $data->iostatus != 5) ? "" : str_replace("&amp;","&",domain2URL($data->sourceurl,false))',
                    'visible' => isVisible('sourceurl', $dparr),
                ),
                array(
                    'name' => 'taskstatus',
                    'type' => 'raw',
                    'value' => 'Utils::getValue(' . $taskstatusstr . ', $data->taskstatus, true)',
                    'visible' => isVisible('taskstatus', $dparr),
                ),
                
                array(
                    'name' => 'target_stype',
                    'type' => 'raw',
                    'value' => 'CHtml::encode($data->target_stype)',
                    'visible' => isVisible('target_stype', $dparr),
                ),
                array(
                    'name' => 'rsummary.googlepr',
                    'type' => 'raw',
                    'value' => '$data->desired_domain_id ? CHtml::encode($data->rsummary->googlepr): ""',
                    'visible' => isVisible('googlepr', $dparr),
                ),
                array(
                    'name' => 'rsummary.mozrank',
                    'type' => 'raw',
                    'value' => '$data->desired_domain_id ? CHtml::encode($data->rsummary->mozrank) : ""',
                    'visible' => isVisible('mozrank', $dparr),
                ),
                array(
                    'name' => 'rsummary.alexarank',
                    'type' => 'raw',
                    'value' => '$data->desired_domain_id ? CHtml::encode($data->rsummary->alexarank): ""',
                    'visible' => isVisible('alexarank', $dparr),
                ),

                array(
                    'name' => 'rewritten_title',
                    'type' => 'raw',
                    'value' => 'CHtml::encode($data->rewritten_title)',
                    'visible' => isVisible('rewritten_title', $dparr),
                ),
                array(
                    'name' => 'blog_title',
                    'type' => 'raw',
                    'value' => 'CHtml::encode($data->blog_title)',
                    'visible' => isVisible('blog_title', $dparr),
                ),
                array(
                    'name' => 'blog_url',
                    'type' => 'raw',
                    'value' => 'CHtml::encode($data->blog_url)',
                    'visible' => isVisible('blog_url', $dparr),
                ),
                array(
                    'name' => 'qa_comments',
                    'type' => 'raw',
                    'value' => 'CHtml::encode($data->qa_comments)',
                    'visible' => isVisible('qa_comments', $dparr),
                ),
                array(
                    'name' => 'livedate',
                    'type' => 'raw',
                    'value' => 'CHtml::encode($data->livedate)',
                    'visible' => isVisible('livedate', $dparr),
                ),
                array(
                    'name' => 'tierlevel_built',
                    'type' => 'raw',
                    'value' => 'Utils::getValue(' . $tierstr . ', $data->tierlevel_built, true)',
                    'visible' => isVisible('tierlevel_built', $dparr),
                ),
                array(
                    'name' => 'spent',
                    'type' => 'raw',
                    'value' => '"$".CHtml::encode($data->spent)',
                    'visible' => isVisible('spent', $dparr) && isset($roles['Admin']),
                ),
                array(
                    'name' => 'notes',
                    'type' => 'raw',
                    'value' => 'CHtml::encode($data->notes)',
                    'visible' => isVisible('notes', $dparr),
                ),
                array(
                    'name' => 'other',
                    'type' => 'raw',
                    'value' => 'CHtml::encode($data->other)',
                    'visible' => isVisible('other', $dparr),
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
                    'value' => '$data->duedate ? date("M/d/Y",strtotime($data->duedate)) : ""',
                    'visible' => isVisible('duedate', $dparr),
                ),

                array(
                    'name' => 'modified',
                    'type' => 'raw',
                    'value' => '$data->modified ? $data->modified : ""',
                    'visible' => isVisible('modified', $dparr),
                ),
            ),
        ));

        //no need use app end, cause we ended this one in the EExcelView already.
        //Yii::app()->end();
    }

	public function actionInventory()
	{
        //do nothing for now;
        set_time_limit(3600);
        ini_set("memory_limit", "512M");
        //print_r($_SERVER);

        //$types = Types::model()->actived()->findAll("type='site' OR type='category' OR type='channel' OR type='linktask'");
        $types = Types::model()->actived()->findAll("type='site' OR type='channel'");
        $gtps = CHtml::listData($types, 'refid', 'typename', 'type');
        /*
        $_linktasks = $gtps['linktask'] ? $gtps['linktask'] : array();
        $_categories = $gtps['category'] ? $gtps['category'] : array();
        */
        $_stypes = $gtps['site'] ? $gtps['site'] : array();
        $_channels = $gtps['channel'] ? $gtps['channel'] : array();
        $chnlstr = Utils::array2String(array("" => '[Channel]') + $_channels);
        $stypestr = Utils::array2String($_stypes);

        $currentaction = $_GET['Inventory']['currentaction'];
        if ($currentaction=='denied') $_GET['Inventory']['isdenied'] = 1;
        $model=new Inventory('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['Inventory']))
            $model->attributes=$_GET['Inventory'];

        $exportType = "Excel5";
        if (strtolower($_GET['Inventory']['export']) == 'csv') {
            $exportType = "CSV";
        }

        //if the user's role is publisher, then he can download his/her own domains only.
        $uid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($uid);
        if(isset($roles['Publisher'])){
            $model->user_id = $uid;
        }

        $this->widget('application.extensions.lkgrid.EExcelView', array(
            'id'=>'inventory-grid',
            'pageSize'=>$model->search()->getTotalItemCount(),
            'filename'=>date("Y-m-d")."_inventory",
            //'customizedata'=>$customizedata,
            'exportType' => $exportType,
            'dataProvider'=>$model->search(),
            'columns'=>array(
                'id',
                'domain',
                array(
                    'name' => 'rdomain.stype',
                    'type' => 'raw',
                    'value' => 'Utils::getValue(' . $stypestr . ', $data->rdomain->stype, true)',
                ),
                'rdomain.googlepr',
                'rdomain.alexarank',
                array(
                    'header' => 'Moz Rank',
                    'name' => 'mozrank',
                    'type' => 'raw',
                    'value' => 'round($data->rdomain->rsummary->mozrank)',
                ),
                array(
                    'header' => 'Authority',
                    'name' => 'mozauthority',
                    'type' => 'raw',
                    'value' => 'round($data->rdomain->rsummary->mozauthority)',
                ),
                'rdomain.linkingdomains',
                'rdomain.inboundlinks',

                array(
                    'name' => 'rdomain.age',
                    'type' => 'raw',
                    'value' => '(($data->rdomain->onlinesince-658454400)>0) ? date("Y-m-d", $data->rdomain->onlinesince) : "-1"',
                ),
                'category_str',
                'accept_tasktype_str',
                /*
                array(
                    'name' => 'channel_id',
                    'type' => 'raw',
                    'value' => 'CHtml::encode(Utils::getValue(' . $chnlstr . ', $data->channel_id, true))',
                ),
                */
                array(
                    'name' => 'channel_str',
                    'type' => 'raw',
                    'value' => '$data->channel_str',
                    'visible' => !isset($roles['Marketer']) && $currentaction == "published",
                ),
                array(
                    'name' => 'acquired_channel_id',
                    'type' => 'raw',
                    'value' => '$data->acquired_channel_id ? Utils::getValue('.$chnlstr.',$data->acquired_channel_id,true) : ""',
                    'visible' => !isset($roles['Marketer']) && $currentaction != "published",
                ),
                array(
                    'name' => 'acquireddate',
                    'type' => 'raw',
                    'visible' => !isset($roles['Marketer']),
                ),
            ),
        ));

        //Yii::app()->end();
	}


	public function actionDeniedDomainWithIO()
	{
        error_reporting(E_ALL);
        //do nothing for now;
        set_time_limit(3600);
        ini_set("memory_limit", "512M");

        $types = Types::model()->actived()->findAll("type='site' OR type='channel'");
        $gtps = CHtml::listData($types, 'refid', 'typename', 'type');

        $_stypes = $gtps['site'] ? $gtps['site'] : array();
        $_channels = $gtps['channel'] ? $gtps['channel'] : array();
        $chnlstr = Utils::array2String(array("" => '[Channel]') + $_channels);
        $stypestr = Utils::array2String($_stypes);

        $currentaction = $_GET['Inventory']['currentaction'];
        //if ($currentaction=='denied') $_GET['Inventory']['isdenied'] = 1;
        $_GET['Inventory']['isdenied'] = 1;
        $model=new Inventory('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['Inventory']))
            $model->attributes=$_GET['Inventory'];

        $exportType = "Excel5";
        if (strtolower($_GET['Inventory']['export']) == 'csv') {
            $exportType = "CSV";
        }

        //if the user's role is publisher, then he can download his/her own domains only.
        $uid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($uid);
        if(isset($roles['Publisher'])){
            $model->user_id = $uid;
        }

        $this->widget('application.extensions.lkgrid.EExcelView', array(
            'id'=>'inventory-grid',
            //'pageSize'=>$model->search()->getTotalItemCount(),
            //'pageSize'=>$model->denied()->getDeniedIO()->search()->getTotalItemCount(),
            'pageSize'=>20000,
            'filename'=>date("Y-m-d")."_denied_inventory",
            //'customizedata'=>$customizedata,
            'exportType' => $exportType,
            'dataProvider'=>$model->getDeniedIO()->denied()->search(),
            'columns'=>array(
                'id',
                'domain',
                'rdomain.googlepr',
                'rdomain.alexarank',
                'rdomain.rsummary.mozrank',
                'rdomain.rsummary.mozauthority',
                'category_str',
                'riotrail.rcreatedby.username',
                array(
                    'name' => 'channel_str',
                    'type' => 'raw',
                    'value' => '$data->channel_str',
                    'visible' => !isset($roles['Marketer']) && $currentaction == "published",
                ),
                'riotrail.rio.rcampaign.rclient.company',
                'riotrail.rio.rcampaign.name',
                'riotrail.rio.anchortext',
                'riotrail.rio.targeturl',
                'riotrail.created',
            ),
        ));

        //Yii::app()->end();
	}


    public function actionDomain(){
        set_time_limit(3600);
        ini_set("memory_limit", "1024M");
        //##error_reporting(E_ALL);

        $model=new Domain('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['Domain']))
            $model->attributes=$_GET['Domain'];

        $exportType = "Excel5";
        if (strtolower($_GET['Domain']['export']) == 'csv') {
            $exportType = "CSV";
        }

        //####1/7/2014
        $types = Types::model()->actived()->bytype(array("site","category"))->findAll();
        $gtps = CHtml::listData($types, 'refid', 'typename', 'type');
        $stypes = $gtps['site'] ? $gtps['site'] : array();
        $categories = $gtps['category'] ? $gtps['category'] : array();
        $stypestr = Utils::array2String($stypes);
        $categorystr = Utils::array2String($categories);
        //####1/7/2014

        $touchedstatus = Domain::$status;
        $statusstr = Utils::array2String($touchedstatus);

        $this->widget('application.extensions.lkgrid.EExcelView', array(
            'id'=>'domain-grid',
            'pageSize'=>$model->with('ronenote')->undeleted()->search()->getTotalItemCount(),
            'filename'=>date("Y-m-d")."_domain",
            //'customizedata'=>$customizedata,
            'exportType' => $exportType,
            'dataProvider'=>$model->with('ronenote')->undeleted()->search(),
            'columns'=>array(
                'id',
                'domain',
                'primary_email',
                'owner',
                'primary_email2',
                'owner2',
                array(
                    'name' => 'touched_status',
                    'type' => 'raw',
                    'value' => 'Utils::getValue(' . $statusstr . ', $data->touched_status, true)',
                ),
                'touched',
                'rsummary.mozauthority',

                //###1/7/2014
                array(
                    'name' => 'rsummary.mozrank',//we can use this one also
                    'type' => 'raw',
                    'value' => 'round($data->rsummary->mozrank)',
                ),
                array(
                    'name' => 'stype',
                    'type' => 'raw',
                    'value' => 'Utils::getValue(' . $stypestr . ', $data->stype, true)',
                ),
                'rsummary.semrushkeywords',
                'category_str',
                'host_country',
                'ronenote.notes',
                //###1/7/2014
            ),
        ));
    }

    /**
     * Download Emails.
     */
    public function actionEmail()
    {
        set_time_limit(3600);
        ini_set("memory_limit", "512M");

		$model=new Email('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Email']))
			$model->attributes=$_GET['Email'];

        $exportType = "Excel5";
        if (strtolower($_GET['Task']['export']) == 'csv') {
            $exportType = "CSV";
        }

        $uid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($uid);
        if(!isset($roles['Admin'])){
            $model->id = -1;//return nothing, if the download user not the Admin role
        }

        $this->widget('application.extensions.lkgrid.EExcelView', array(
            'id'=>'email-grid',
            'pageSize'=>$model->search()->getTotalItemCount(),
            'filename'=>date("Y-m-d")."_emails",
            'exportType' => $exportType,
            'dataProvider'=>$model->search(),
            'columns'=>array(
                'id',
                'rdomain.domain',
                'subject',
                'to',
                'send_time',
                //'revent.event',
                //'rtemplate.name',
                //'rmailer.user_alias',
                'rcreatedby.username',
                /*
                array(
                    'name' => 'revent.event',
                    'type' => 'raw',
                    'value' => 'Email::getEvents($data->revent)',
                ),
                */
            ),
        ));

        //no need use app end, cause we ended this one in the EExcelView already.
        //Yii::app()->end();
    }

    /**
     * Download Trails.
     */
    public function actionTrail()
    {
        set_time_limit(3600);
        ini_set("memory_limit", "512M");

		$model=new Trail('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Trail']))
			$model->attributes=$_GET['Trail'];

        $exportType = "Excel5";
        if (strtolower($_GET['Task']['export']) == 'csv') {
            $exportType = "CSV";
        }

        $uid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($uid);
        if(!isset($roles['Admin'])){
            $model->id = -1;//return nothing, if the download user not the Admin role
        }

        $this->widget('application.extensions.lkgrid.EExcelView', array(
            'id'=>'email-grid',
            'pageSize'=>$model->search()->getTotalItemCount(),
            'filename'=>date("Y-m-d")."_trails",
            'exportType' => $exportType,
            'dataProvider'=>$model->search(),
            'columns'=>array(
                'id',
                array(
                    'name' => 'old_value',
                    'type' => 'raw',
                    'value' => 'Utils::array2String(unserialize($data->old_value), ",\r\n");',
                ),
                array(
                    'name' => 'new_value',
                    'type' => 'raw',
                    'value' => 'Utils::array2String(unserialize($data->new_value), ",\r\n");',
                ),
                array(
                    'name' => 'description',
                    'type' => 'raw',
                ),
                'operation',
                'model',
                'action',
                'created',
                array(
                    'name' => 'user_id',
                    'type' => 'raw',
                    'value' => '$data->rcreatedby->username',
                ),
            ),
        ));

        //no need use app end, cause we ended this one in the EExcelView already.
        //Yii::app()->end();
    }

    /**
     * Download IO historic.
     */
    public function actionIohistoric()
    {
        set_time_limit(3600);
        ini_set("memory_limit", "512M");

		$model=new IoHistoricReporting('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['IoHistoricReporting']))
			$model->attributes=$_GET['IoHistoricReporting'];

        $exportType = "Excel5";
        if (strtolower($_GET['Task']['export']) == 'csv') {
            $exportType = "CSV";
        }

        $uid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($uid);
        if(!isset($roles['Admin'])){
            $model->id = -1;//return nothing, if the download user not the Admin role
        }

        $types = Types::model()->bytype(array("channel"))->findAll();
        $gtps = CHtml::listData($types, 'refid', 'typename', 'type');
        $channels = $gtps['channel'] ? $gtps['channel'] : array();
        natcasesort($channels);
        $channelstr = Utils::array2String($channels);

        $tiers = CampaignTask::$tier;
        $tierstr = Utils::array2String($tiers);

        $this->widget('application.extensions.lkgrid.EExcelView', array(
            'id'=>'email-grid',
            'pageSize'=>$model->search()->getTotalItemCount(),
            'filename'=>date("Y-m-d")."_io_historic",
            'exportType' => $exportType,
            'dataProvider'=>$model->search(),
            'columns'=>array(
                'task_id',
                'rcampaign.name',
                array(
                    'name'   => 'rcampaign.rclient.name',
                    'type'   => 'raw',
                    'header' => 'Client',
                    'value'  => '$data->rcampaign->rclient->name',
                ),
                array(
                    'name' => 'channel_id',
                    'value' => 'Utils::getValue(' . $channelstr . ', $data->channel_id, true)',
                ),
                array(
                    'name' => 'tierlevel',
                    'value' => 'Utils::getValue(' . $tierstr . ', $data->tierlevel, true)',
                ),
                //'rcampaign.rclient.name',
                'date_initial',
                'date_current',
                'date_accepted',
                'date_pending',
                'date_approved',
                'date_preqa',
                'date_inrepair',
                'date_completed',

                array(
                    'name' => 'time2current',
                    'value' => '($data->time2current>0) ? round($data->time2current/86400,2) : 0',
                ),
                array(
                    'name' => 'time2accepted',
                    'value' => '($data->time2accepted>0) ? round($data->time2accepted/86400,2) : 0',
                ),
                array(
                    'name' => 'time2pending',
                    'value' => '($data->time2pending>0) ? round($data->time2pending/86400,2) : 0',
                ),
                array(
                    'name' => 'time2approved',
                    'value' => '($data->time2approved>0) ? round($data->time2approved/86400,2) : 0',
                ),
                array(
                    'name' => 'time2preqa',
                    'value' => '($data->time2preqa>0) ? round($data->time2preqa/86400,2) : 0',
                ),
                array(
                    'name' => 'time2inrepair',
                    'value' => '($data->time2inrepair>0) ? round($data->time2inrepair/86400,2) : 0',
                ),
                array(
                    'name' => 'time2completed',
                    'value' => '($data->time2completed>0) ? round($data->time2completed/86400,2) : 0',
                ),
            ),
        ));

        //no need use app end, cause we ended this one in the EExcelView already.
        //Yii::app()->end();
    }

    public function actionContent($id = 0, $format='html')
    {

        $tmdl = Task::model()->findByPk($id);
        if ($tmdl && $tmdl->content_article_id > 0) {
            $ctm = Content::model()->findByPk($tmdl->content_article_id);

            //////////////1/28/2015/////////////////////
            $fuzzytitle = $tmdl->id . " - " . $tmdl->rewritten_title;
            $response = Utils::sendCmd2SSSAPI('fuzzytitle',array('ids'=>$fuzzytitle));
            if ($response->isSuccessful()) {
                $responsebody = $response->getBody();
                //echo $responsebody;
                $rbodys = simplexml_load_string(utf8_encode($responsebody));
                $iarr = array();
                //echo (string)$rbodys->title;
                if (isset($rbodys->articleid) && $rbodys->articleid) {
                    $articleid = $rbodys->articleid;
                    $articleid = (int)$articleid;
                    $status    = (int)$rbodys->status;//### int(4.5) = 4;

                    if ($status >= 4) {
                        $ctm->setIsNewRecord(false);
                        $ctm->setScenario('update');

                        $ctm->title  = (string)$rbodys->title;
                        $ctm->length = (int)$rbodys->length;
                        $ctm->text   = (string)$rbodys->textBody;
                        $ctm->html   = (string)$rbodys->htmlBody;
                        $ctm->save();
                    }
                }

                if (isset($status) && $status >= 4) {
                    $tmdl->setIsNewRecord(false);
                    $tmdl->setScenario('update');
                    $tmdl->taskstatus = $status;
                    $tmdl->checkouted = time();
                    $tmdl->save();
                }

            }
            ////////////////1/28/2015///////////////////

            if ($ctm) {
                ob_start();
                header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 
                header("Cache-Control: no-cache, must-revalidate"); 
                header("Pragma: no-cache"); 

                //header("Content-Type: text/html");
                header("Cache-Control: no-store, no-cache");
                //header('Content-Disposition: attachment; filename= '.$article_info['keyword'].$suffix);
                header("Pragma: no-cache");

                //##$suffix = ($format == 'html') ? '.html' : '.txt';
                $suffix = in_array(strtolower($format), array('html','txt','doc')) ? '.'.$format : '.txt';

                $filename = preg_replace( '#\s+#', '_', trim($ctm->title) );
                $filename = html_entity_decode($filename);
                //windows valid file name,
                $reg_str = array('/\//', '/\\\/', '/\*/', '/\?/', '/\:/', '/\"/', '/\</', '/\>/', '/\|/', '/\,/');
                $filename = preg_replace( $reg_str, '_', $filename ) . $suffix;

                header('Content-Disposition: attachment; filename='. $filename  );
                if ($format == 'html') {
                    echo $ctm->html;
                } elseif($format == 'doc') {
                    header("Content-type: application/vnd.ms-word");
                    echo "<html><body>";
                    echo html_entity_decode($ctm->html);
                    echo "</body></html>";
                } else {
                    echo "Article title: " . $ctm->title . "\r\n";
                    echo "Article content:\r\n" . $ctm->text . "\r\n";
                    //echo $ctm->text;
                }

                ob_end_flush();
            }
        }

        //Yii::app()->end();
        exit();
    }
}