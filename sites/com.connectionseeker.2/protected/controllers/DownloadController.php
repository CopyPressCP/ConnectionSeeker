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
        $pgstatus = Task::$pgstatus;
        $pgstatusstr =  Utils::array2String($pgstatus);
        $taskstatus = Task::$status;
        $taskstatusstr =  Utils::array2String($taskstatus);

        $carts = Cart::model()->findAllByAttributes(array('client_domain_id'=>$cmpmodel->domain_id));
        $cartdomains = CHtml::listData($carts, 'domain_id', 'domain');
        $desiredstr =  Utils::array2String($cartdomains);

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
                    'name' => 'progressstatus',
                    'type' => 'raw',
                    'value' => 'Utils::getValue(' . $pgstatusstr . ', $data->progressstatus, true)',
                    'visible' => isVisible('progressstatus', $dparr),
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
                    'value' => '($ismarketer && $data->progressstatus != 4) ? "" : CHtml::encode($data->sourceurl)',
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
                    'name' => 'notes',
                    'type' => 'raw',
                    'value' => 'CHtml::encode($data->notes)',
                    'visible' => isVisible('notes', $dparr),
                ),

                array(
                    'name' => 'duedate',
                    'type' => 'raw',
                    'value' => '$data->duedate ? date("M/d/Y",$data->duedate) : ""',
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
                'rdomain.linkingdomains',
                'rdomain.inboundlinks',

                array(
                    'name' => 'rdomain.age',
                    'type' => 'raw',
                    'value' => 'CHtml::encode((($data->rdomain->onlinesince - 658454400) > 0) ? date("Y-m-d", $data->rdomain->onlinesince) : "-1")',
                ),
                'category_str',
                'accept_tasktype_str',
                array(
                    'name' => 'channel_id',
                    'type' => 'raw',
                    'value' => 'CHtml::encode(Utils::getValue(' . $chnlstr . ', $data->channel_id, true))',
                ),

            ),
        ));

        //Yii::app()->end();
	}

    public function actionDomain(){
        set_time_limit(3600);
        ini_set("memory_limit", "512M");

        $model=new Domain('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['Domain']))
            $model->attributes=$_GET['Domain'];

        $exportType = "Excel5";
        if (strtolower($_GET['Domain']['export']) == 'csv') {
            $exportType = "CSV";
        }

        $this->widget('application.extensions.lkgrid.EExcelView', array(
            'id'=>'domain-grid',
            'pageSize'=>$model->search()->getTotalItemCount(),
            'filename'=>date("Y-m-d")."_domain",
            //'customizedata'=>$customizedata,
            'exportType' => $exportType,
            'dataProvider'=>$model->search(),
            'columns'=>array(
                'domain',
            ),
        ));
    }
}