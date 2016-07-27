<?php

class EmailController extends RController
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
			'rights - open, mailerreport', // perform access control for CRUD operations
            'onlyAdminAccess + index,delete',
			//'accessOwn + view,update,delete', // perform customize additional access control for CRUD operations
		);
	}

	/**
	 * @return array action filters
     * We can build one filter file, and put this function into the filter file
	 */
    public function filterOnlyAdminAccess($filterChain) {
        $cuid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($cuid);

        if(isset($roles['Admin'])){
            $filterChain->run();
        } else {
            $filterChain->controller->accessDenied();
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

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Email;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Email']))
		{
			$model->attributes=$_POST['Email'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
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

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Email']))
		{
			$model->attributes=$_POST['Email'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * get email group from a particular model.
	 * @param integer $id the ID of parent id
	 */
	public function actionGroup($id)
	{
        $rs = array();
        $rs = Email::model()->findAllByAttributes(array('parent_id'=>$id));
        echo CJSON::encode($rs);
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
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

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

		$model=new Email('search');
		$model->unsetAttributes();  // clear any default values
        if (!isset($_GET['Email'])) {
            $_GET['Email']["created_by"] = Yii::app()->user->id;
        }
		if(isset($_GET['Email']))
			$model->attributes=$_GET['Email'];

		$this->render('index',array(
			'model'=>$model,
		));
	}

	/**
	 * Export Review History Of Email Data
	 * @param $date_start date, $date_end date
	 */
	public function actionReviewexport()
	{
		if(isset($_POST['Export'])) {
            extract($_POST['Export']);
            if (empty($date_start)) $date_start = date("Y-m-d", time() - 86400);//one day ago
            if (empty($date_end)) $date_end = date("Y-m-d");//today.

            $startstamp = strtotime($date_start);
            $endstamp = strtotime($date_end);
            $nofday = round( ($endstamp - $startstamp) / 86400 );

            define('DS', DIRECTORY_SEPARATOR);
            $exportfile = dirname(dirname(dirname(__FILE__))) . DS . "assets" . DS . "reviewhistorydata.csv";
            if (file_exists($exportfile)) unlink($exportfile);//remove the old files

            $fp = fopen($exportfile, 'a+');
            $els = array("Date","Sites Acquired","Pending","Pending TO Approved","Pending TO Accepted",
                "Approved TO Pre QA","Approved TO Accepted","Content Sent","Email Sent","Email Open","Email Received");
            fputcsv($fp, $els);

            for($i = 0; $i<$nofday; $i++) {
                $currentdate = date("Y-m-d H:i:s", $startstamp + ($i*86400));
                $dateaftercurrent = date("Y-m-d H:i:s", $startstamp + ($i*86400) + 86400);
                $els = array();
                $els["currentdate"] = $currentdate;

                //Total Sites Acquired
                $q = "SELECT COUNT( * ) AS total FROM lkm_inventory
                    WHERE acquireddate >= '{$currentdate}' AND  acquireddate < '{$dateaftercurrent}'";
                $totalacquired = Yii::app()->db->createCommand($q)->queryRow();
                $totalac = 0;
                if ($totalacquired) $totalac = $totalacquired['total'];
                $els["totalac"] = $totalac;

                $els["pending"] = 0; //"Total Sites moved into pending"
                $els["p2approved"] = 0; //"Total Sites moved FROM pending TO Approved"
                $els["p2accepted"] = 0; //"Total Sites moved FROM pending TO Accepted"
                $els["a2preqa"] = 0; //"Total Sites moved FROM Approved TO Pre QA"
                $els["a2accept"] = 0; //"Total Sites moved FROM Approved TO Accepted"
                $q = "SELECT COUNT( * ) AS iocount, oldiostatus, iostatus FROM lkm_io_history
                    WHERE created >= '{$currentdate}' AND created < '{$dateaftercurrent}'
                    GROUP BY oldiostatus, iostatus";
                $taskscount = Yii::app()->db->createCommand($q)->queryAll();
                if ($taskscount) {
                    foreach ($taskscount as $tc) {
                        if ($tc["iostatus"] == 21) {
                            $els["pending"] = $tc["iocount"];
                        } else if($tc["oldiostatus"] == 21 && $tc["iostatus"] == 3) {
                            $els["p2approved"] = $tc["iocount"];
                        } else if($tc["oldiostatus"] == 21 && $tc["iostatus"] == 2) {
                            $els["p2accepted"] = $tc["iocount"];
                        } else if($tc["oldiostatus"] == 3 && $tc["iostatus"] == 31) {
                            $els["a2preqa"] = $tc["iocount"];
                        } else if($tc["oldiostatus"] == 3 && $tc["iostatus"] == 2) {
                            $els["a2accept"] = $tc["iocount"];
                        }
                    }
                }

                //Total pieces of content sent
                $q = "SELECT COUNT( DISTINCT model_id ) AS total  FROM `lkm_operation_trail` 
                    WHERE created >= '{$currentdate}' AND created < '{$dateaftercurrent}'
                    AND `new_value` LIKE '%s:8:".'"sentdate";s:10:"2%'."' AND model='Task'";
                $contentsent = Yii::app()->db->createCommand($q)->queryRow();
                $totalcs = 0;
                if ($contentsent) $totalcs = $contentsent['total'];
                $els["totalcs"] = $totalcs;


                //##24 hours of Emails Sent, Open, and Received
                $q = "SELECT COUNT( * ) AS total  FROM `lkm_outreach_email` 
                    WHERE send_time >= '{$currentdate}' AND send_time < '{$dateaftercurrent}'";
                $emailsent = Yii::app()->db->createCommand($q)->queryRow();
                $totales = 0;
                if ($emailsent) $totales = $emailsent['total'];
                $els["totales"] = $totales;

                //24 hours of Emails Open
                $q = "SELECT COUNT( * ) AS total  FROM `lkm_outreach_email` 
                    WHERE open_time >= '{$currentdate}' AND open_time < '{$dateaftercurrent}'";
                $emailopen = Yii::app()->db->createCommand($q)->queryRow();
                $totaleo = 0;
                if ($emailopen) $totaleo = $emailopen['total'];
                $els["totaleo"] = $totaleo;

                //24 hours of Emails Received
                $q = "SELECT COUNT( * ) AS total  FROM `lkm_outreach_email` 
                    WHERE first_reply_time >= '{$currentdate}' AND first_reply_time < '{$dateaftercurrent}'";
                $emailreply = Yii::app()->db->createCommand($q)->queryRow();
                $totaler = 0;
                if ($emailreply) $totaler = $emailreply['total'];
                $els["totaler"] = $totaler;


                fputcsv($fp, $els);
            }
            fclose($fp);

            header("Location:assets/reviewhistorydata.csv");
            Yii::app()->end();
		}

		$this->render('reviewexport',array(
			//'model'=>$model,//nothing need to pass through
		));
	}
	/**
	 * Email Report, it will shows up the open rate & click rate for each mailer.
	 */
	public function actionMreport()
	{
        //$mmodel = new Mailer;
        if (isset($_GET['pageSize'])) {
            Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
            unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
        }

        $refids = array();
        if (isset($_GET['user_id'])) {
            $_umailers = Yii::app()->db->createCommand()->select('mailer_id')->from('{{outreach_email}}')
                ->where("(created_by = :cby) AND (send_time>='2013-01-01 00:00:00')", array(':cby'=>trim($_GET['user_id'])))
                ->group('mailer_id')->queryAll();
            if ($_umailers) {
                $i = 0;
                foreach ($_umailers as $um) {
                    $_GET['Mailer']['id'][$i] = $um['mailer_id'];
                    $i++;
                }
            } else {
                $_GET['Mailer']['id'] = 0;
            }
            $refids = $_GET['Mailer']['id'];
        }

		$model=new Mailer('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Mailer']))
			$model->attributes=$_GET['Mailer'];

		$this->render('report',array(
			'model'=>$model,
			'refids'=>$refids,
		));
	}

	/**
	 * Email Report, it will shows up the open rate & click rate for each template.
	 */
	public function actionTreport()
	{
        //$mmodel = new Template;
        if (isset($_GET['pageSize'])) {
            Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
            unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
        }

        $refids = array();
        if (isset($_GET['user_id'])) {
            $_umailers = Yii::app()->db->createCommand()->select('template_id')->from('{{outreach_email}}')
                ->where("(created_by = :cby) AND (send_time>='2013-01-01 00:00:00')", array(':cby'=>trim($_GET['user_id'])))
                ->group('template_id')->queryAll();
            if ($_umailers) {
                $i = 0;
                foreach ($_umailers as $um) {
                    $_GET['Template']['id'][$i] = $um['template_id'];
                    $i++;
                }
            } else {
                $_GET['Template']['id'] = 0;
            }
            $refids = $_GET['Template']['id'];
        }

		$model=new Template('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Template']))
			$model->attributes=$_GET['Template'];

		$this->render('report',array(
			'model'=>$model,
			'refids'=>$refids,
		));
	}

	/**
	 * Email Report, it will shows up the open rate & click rate for each user.
	 */
	public function actionReport()
	{
        // page size drop down changed
        if (isset($_GET['pageSize'])) {
            Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
            unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
        }

		$model=new User('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['User']))
			$model->attributes=$_GET['User'];

		$this->render('newreport',array(
			'model'=>$model,
		));
	}

	//####public function actionMailerreport()
	public function actionReportdetails()
	{
        $ids = $_REQUEST['ids'];
        if (!is_array($ids)) {
            $ids = array($ids);
        }
        //print_r($ids);

        $now = time();
        //create last 7 days date time;
        for ($i = 1; $i <=7; $i++) {
            $diff = 7 - $i;
            $last7[$i] = date("Y-m-d", strtotime("-{$diff} days"));;
        }

        $rs['success'] = true;
        if (empty($ids)) {
            $rs['msg'] = "Please provide at least one item there.";
            $rs['success'] = false;
        } else {
            //#####$inwhere = " AND `from` IN(".implode(",", array_values($ids)).")";

            //#########1/11/2013 start ################//
            $cssortby = "user";
            if (isset($_REQUEST['sortby'])) $cssortby = strtolower($_REQUEST['sortby']);
            if ($cssortby == 'template') {
                $replyfield = $mainfield = "template_id";
            } elseif ($cssortby == 'mailer') {
                $replyfield = $mainfield = "mailer_id";
            } else {
                $replyfield = $mainfield = "created_by";
                //## $replyfield = "reply_created_by";
            }
            $inwhere = " AND `$mainfield` IN(".implode(",", array_values($ids)).") AND send_time >= '2013-01-01 00:00:00' ";
            //##$replyinwhere = " AND `$replyfield` IN(".implode(",", array_values($ids)).") AND send_time >= '2013-01-01 00:00:00' ";

            if (in_array($cssortby, array('template', 'mailer')) && isset($_REQUEST['user_id'])) {
                $_int_uid = (int)$_REQUEST['user_id'];
                $inwhere = $inwhere." AND (created_by = $_int_uid) ";
                //##$replyinwhere = $replyinwhere." AND (created_by = $_int_uid) ";
            }
            //#########1/11/2013 end ################//

            $stamp24hours = $now - 86400;
            $stamp7days = $now - (86400 * 7);
            $stamp30days = $now - (86400 * 30);
            $stamplifetime = 0;
            $report = array();

            //time zone
            $_tzs = array("24hours","7days","30days","lifetime");
            foreach ($_tzs as $vt) {
                $_curr = "stamp".$vt;
                $_last = "last".$vt;
                $currentstamp = date("Y-m-d H:i:s", $$_curr);

                $lastopen = Yii::app()->db->createCommand()
                     ->select("`$mainfield`, count(*) AS count")->from('{{outreach_email}}')
                     ->where("(open_time>'$currentstamp') ".$inwhere)
                     ->group("$mainfield")->queryAll();
                if ($lastopen) {
                    foreach ($lastopen as $v) {
                        $refid = $v[$mainfield];
                        $report[$refid]['open'][$_last] = $v['count'];
                    }
                }

                #####!!!!!!!!!!!!!!!!!!!!!!###############
                //##$currentstamp = date("Y-m-d H:i:s", $currentstamp);

                $lastsent = Yii::app()->db->createCommand()
                     ->select("`$mainfield`, count(*) AS count")->from('{{outreach_email}}')
                     ->where("(send_time>'$currentstamp')".$inwhere)
                     ->group("$mainfield")->queryAll();
                if ($lastsent) {
                    foreach ($lastsent as $v) {
                        $refid = $v[$mainfield];
                        $report[$refid]['sent'][$_last] = $v['count'];
                    }
                }

                $lastreply = Yii::app()->db->createCommand()
                     ->select("`$mainfield`, count(*) AS count")->from('{{outreach_email}}')
                     ->where("(first_reply_time>'$currentstamp')".$inwhere)
                     ->group("$mainfield")->queryAll();
                if ($lastreply) {
                    foreach ($lastreply as $v) {
                        $refid = $v[$mainfield];
                        $report[$refid]['reply'][$_last] = $v['count'];
                    }
                }

                $sql = "SELECT `user_id`, COUNT(`user_id`) AS `count` FROM (
	                       SELECT `id`, `new_value`, `model_id`, `user_id` FROM (
                               SELECT * FROM `lkm_operation_trail` 
                               WHERE user_id IN (".implode(",", array_values($ids)).") AND created>'$currentstamp' 
                               AND `new_value` LIKE '%s:14:".'"touched_status"'.";%' ORDER BY id DESC
	                       ) AS m WHERE model='Domain' GROUP BY model_id
                        ) AS mm WHERE `new_value` LIKE '%s:14:".'"touched_status";s:2:"11";'."%' OR `new_value` LIKE '%s:14:".'"touched_status";i:11;'."%' GROUP BY user_id";

                $contactform = Yii::app()->db->createCommand($sql)->queryAll();
                if ($contactform) {
                    foreach ($contactform as $v) {
                        $refid = $v['user_id'];
                        $report[$refid]['ctform'][$_last] = $v['count'];
                    }
                }
            }
            $rs["report"] = $report;
        }

        echo CJSON::encode($rs);
        Yii::app()->end();
	}


	/**
	 * Track mails event from sendgrid.
     * this funtion permission need open to all of the users.
	 */
    public function actionTrack()
    {
        //the default header should be 200 OK.But when we didn't store it correctly, we should set it into other status code.
        //header('HTTP/1.1 200 OK'); http://www.jonasjohn.de/snippets/php/headers.htm
        $model = new Email;
        $emodel = new EmailEvent;
        $dmodel = new Domain;

        $isvalid = false;
        $created = time();

        //start trasaction;
        //email=emailrecipient@domain.com&amp;event=open&amp;userid=1123&amp;template=welcome&amp;category=user_signup

		$model->unsetAttributes();  // clear any default values
		if(isset($_POST) && $_POST) {
            extract($_POST);
            $transaction = Yii::app()->db->beginTransaction();
            try {
                $emodel->attributes=$_POST;
                $emodel->rawdata = CJSON::encode($_POST);
                $emodel->created = $created;
                if ($emodel->save()) {
                    //早期邮件中，没有queue_id，因此这里需要做出调整
                    $_status = $this->_getTouchedStatus($event);
                    $_ele = $event."ed";
                    if (in_array(strtolower($event), array("open","click"))) {
                        if (isset($queue_id) && $queue_id > 0) {
                            $qm = $model->findByPk($queue_id);
                        } else {
                            /*
                            $qinfo = array();
                            $qinfo[$_ele] = $created;
                            Yii::app()->db->createCommand()
                                ->update('{{email_queue}}', $qinfo, 'email=:email', array(':email'=>$email));
                                */
                            $qm = $model->find('`to`=:to', array(':to'=>$email));
                        }

                        if ($qm) {
                            $qm->$_ele = $created;
                            $qm->save();

                            $do = $dmodel->findByPk($qm->domain_id);
                            if ($do && ($_status > 0)) {
                                if (!in_array($do->touched_status, array(6,20))) {
                                    $do->touched_status = $_status;
                                }
                                $do->touched = $created;
                                $do->touched_by = Yii::app()->user->id;
                                $do->save();
                            }
                        }
                    }


                }


                // Commit the transaction
                $transaction->commit();
                $isvalid = true;
            } catch (Exception $e) {
                // Was there an error? Error, rollback transaction
                //print_r($e);

                $isvalid = false;
                $transaction->rollback();
            }//end transaction
        }

        if ($isvalid) {
            echo "ok";
        } else {
            header('HTTP/1.1 406 INVALID_DATA');//do not send 200 OK to sendgrid
            echo "invalid data";
        }

        Yii::app()->end();
    }

    private function _getTouchedStatus($event) {
        $event = strtolower($event);

        switch ($event) {
            case 'bounce':
            case 'dropped':
            case 'deferred':
                $_status = 3;
                break;
            case 'open':
                $_status = 4;
                break;
            case 'click':
                $_status = 5;
                break;
            case 'spamreport':
                $_status = 7;
                break;
            case 'unsubscribe':
                $_status = 8;
                break;
            default:
                $_status = -1;
        }

        return $_status;
    }

	public function actionOpen($id)
	{
        $model = $this->loadModel($id);
        $now = time();
        if ($model) {
            //echo $model->domain_id;
            if (!$model->opened) {
                $model->opened = $now;
                $model->save();

                $emodel = new EmailEvent;
                $emodel->setIsNewRecord(true);
                $emodel->id=NULL;
                $emodel->domain_id=$model->domain_id;
                $emodel->template_id=$model->template_id;
                $emodel->queue_id=$model->id;
                $emodel->email=$model->email_from;
                $emodel->event="open";
                $emodel->created=$now;
                $emodel->save();

                $asmodel = AutomationSent::model()->findByAttributes(array('queue_id'=>$id));
                if ($asmodel) {
                    $asmodel->opened_time = date("Y-m-d H:i:s", $now);
                    $asmodel->save();
                }

                $ormodel = OutreachEmail::model()->findByAttributes(array('queue_id'=>$id));
                if ($ormodel) {
                    $ormodel->open_time = date("Y-m-d H:i:s", $now);
                    $ormodel->save();
                }
            }
        }

        //print_r($_GET);
        Yii::app()->end();
        exit;
    }

	public function actionIsreplied()
	{
        $ids = $_REQUEST['ids'];
        $rs = array();
        $rs['success'] = true;
        if (empty($ids)) {
            $rs['msg'] = "Please provide at least one item there.";
            $rs['success'] = false;
        } else {
            if (!is_array($ids)) {
                $ids = array($ids);
            }

            $lastreply = Yii::app()->db->createCommand()
                 ->select("id, parent_id, count(DISTINCT `parent_id`) AS count")->from('{{email_queue}}')
                 ->where("(is_reply = 1) AND (domain_id>0) AND (parent_id>0) AND (ccreply_ordering<=1) AND (reply_created_by>0) AND (created_by IS NULL) AND `parent_id` IN(".implode(",", array_values($ids)).")")
                 ->group("parent_id")->queryAll();
            //print_r($lastreply);
            if ($lastreply) {
                foreach ($lastreply as $v) {
                    $refid = $v["parent_id"];
                    $rs['report'][$refid] = $v['count'];
                }
            }
        }

        //print_r($_GET);
        echo CJSON::encode($rs);
        Yii::app()->end();
        exit;
    }

	/**
	 * Send mails.
     * this funtion is not done yet, we need improve it in the feature when we get a chance.
	 */
	public function actionSend()
	{
        set_time_limit(0);
        ini_set("memory_limit", "128M");

        $rtn = array();
        $acttype = strtolower(Yii::app()->request->getParam("actiontype"));
        //$acttype = strtolower($_POST['actiontype']);
        if (in_array($acttype, array("send","sendall"))) {
            //Autoload fix & set the X-SMTPAPI
            spl_autoload_unregister(array('YiiBase','autoload'));
            Yii::import('ext.yii-mail.XSmtpHeader', true);
            $xhdr = new XSmtpHeader();
            spl_autoload_register(array('YiiBase','autoload'));

            $xhdr->setCategory("contact1st");//initial, contact 1st time.

            $message = new YiiMailMessage;
            $headers = $message->getHeaders();
        }

        $_sendtime = date('Y-m-d H:i:s');

        if ($acttype == 'sendall') {
            //sending out the all of the queued email
            $emails = Yii::app()->db->createCommand()->select()->from('{{email_queue}}')
                ->where('((send_time IS NULL) AND (status = 0))')
                //->limit(10)
                ->queryAll();
            if ($emails) {

                foreach($emails as $e) {
                    $fo = Mailer::model()->findByPk($e['from']);
                    $xhdr->setUniqueArgs(array('domain_id'=>$e['domain_id'], 'template_id'=>$e['template_id'], 'queue_id'=>$e['id'], 'mailer_id' => $e['from']));
                    $headers->addTextHeader('X-SMTPAPI', $xhdr->asJSON());

                    //8/28/2014
                    if ($e['domain_id'] > 0) {
                        $dm = Domain::model()->findByPk($e['domain_id']);
                        if ($dm) {
                            $e['content'] = str_ireplace('%DOMAIN%', $dm->domain, $e['content']);
                        }
                    }

                    // where mailer_id = $e['from'] and domain_id = $e['domain_id'];
                    $parentmdl = Email::model()->findByAttributes(array('from'=>$e['from'],'domain_id'=>$e['domain_id']),
                                                                        'id!=:eid', array(':eid'=>$e['id']));
                    $parent_id = 0;
                    if ($parentmdl) {
                        $parent_id = $parentmdl->id;
                    }

                    $pixel = "<img src='http://www.connectionseeker.com/index.php?r=email/open&id=".$e['id']."' width='1' height='1' />";

                    if (!empty($e['cc'])) {
                        $_cces = explode(",", trim($e['cc']));
                        if ($_cces) {
                            foreach ($_cces as $_cc) {
                                if (filter_var($_cc, FILTER_VALIDATE_EMAIL) && $message) $message->addCc($_cc);
                            }
                        }
                    }

                    $message->setSubject($e['subject'])
                        ->setTo($e['to'])
                        ->setFrom($fo->email_from, $fo->display_name)
                        ->setReplyTo($fo->reply_to, $fo->display_name)
                        ->setBody($e['content'].$pixel, 'text/html');

                    $m = Yii::app()->mail;
                    $m->transportOptions = array(
                                            'host' => $fo->smtp_host,
                                            'username' => $fo->username,
                                            'password' => $fo->password,
                                            'port' => $fo->smtp_port,);
                    $c = $m->send($message);
                    if ($c) {
                        $em = Email::model()->findByPk($e['id']);
                        $em->status = 1;
                        $em->send_time = $_sendtime;
                        $em->parent_id = $parent_id;
                        $em->save();
                        //##$dm = Domain::model()->findByPk($e['domain_id']);
                        if ($dm) {
                            if ($dm->touched_status == 11) {
                                $dm->touched_status = 19;
                            } else {
                                if (!in_array($dm->touched_status, array(6,20,11,19,18))) {
                                    $dm->touched_status = 2;
                                }
                            }
                            $dm->touched = $_sendtime;
                            $dm->touched_by = Yii::app()->user->id;
                            $dm->save();
                        }
                    } else {
                        $rtn['message'] .= 	$e['to'].",";
                    }
                }
            }

            if (isset($rtn['message'])) {
                $rtn['message'] = "Queued Emails Were Sent, But ".$rtn['message']." weren't send out successfully,";;
            } else {
                $rtn['message'] = "Queued Emails Were Sent Successfully.";
            }
            echo CJSON::encode($rtn);
            exit;
        }

        if (!isset($_POST['mailfrom'])) {
            $rtn['success'] = false;
            $rtn['message'] = "Failure, Please provide mail from.";
            echo CJSON::encode($rtn);
            exit;
        }

        $model=new Email;
        $attrs = array();

        //print_r($_POST);
        //switch ($acttype) {
        //}
        if ($acttype == 'queue') {
            $attrs['status'] = 0;
            $rtn['message'] = "Email was saved.";
        } else {
            $attrs['status'] = 1;
            $attrs['send_time'] = date("Y-m-d H:i:s");
            $rtn['message'] = "Email was sent Successfully.";
        }
        $attrs['domain_id'] = $_POST['mail_domain_id'];
        $attrs['template_id'] = $_POST['template_id'];
        $attrs['from'] = $_POST['mailfrom'];
        $attrs['to'] = trim($_POST['mailto']);
        $attrs['subject'] = $_POST['subject'];
        $attrs['content'] = $_POST['message'];
        if (trim($_POST['cc'])) {
            $_cces = explode(",", trim($_POST['cc']));
            if ($_cces) {
                $cces = array();
                foreach ($_cces as $_cc) {
                    if (filter_var($_cc, FILTER_VALIDATE_EMAIL)) {
                        $cces[$_cc] = $_cc;
                        if ($message) $message->addCc($_cc);
                    }
                }
                if ($cces) {
                    $attrs['cc'] = implode(",", array_values($cces));
                }
            }
        }

        // where mailer_id = $e['from'] and domain_id = $e['domain_id'];
        $parentmdl = Email::model()->findByAttributes(array('from'=>$attrs['from'],'domain_id'=>$attrs['domain_id']));
        $parent_id = 0;
        if ($parentmdl) {
            $parent_id = $parentmdl->id;
        }
        $attrs['parent_id'] = $parent_id;


        $fo = Mailer::model()->findByPk($attrs['from']);
        if($fo === null) {
            $rtn['success'] = false;
            $rtn['message'] = "Failure, Please provide the right mailer account.";
            echo CJSON::encode($rtn);
            exit;
        }
        $attrs['email_from'] = $fo['email_from'];

        if(isset($_POST['actiontype']) && $acttype != 'sendall')
		{
            //##print_r($attrs);
			$model->attributes=$attrs;
			if($model->save()) {
                if ($attrs['domain_id'] > 0) {
                    $dm = Domain::model()->findByPk($attrs['domain_id']);
                    if ($dm) {
                        $attrs['content'] = str_ireplace('%DOMAIN%', $dm->domain, $attrs['content']);
                    }
                }

                //do nothing for now.
                if ($acttype == 'send') {
                    //$message = new YiiMailMessage;
                    $xhdr->setUniqueArgs(array('domain_id'=>$attrs['domain_id'], 'template_id'=>$attrs['template_id'], 'queue_id'=>$model->id));
                    $headers->addTextHeader('X-SMTPAPI', $xhdr->asJSON());
                    $pixel = "<img src='http://www.connectionseeker.com/index.php?r=email/open&id=".$model->id."' width='1' height='1' />";


                    $message->setSubject($attrs['subject'])
                        ->setTo($attrs['to'])
                        ->setFrom($fo->email_from, $fo->display_name)
                        ->setReplyTo($fo->reply_to, $fo->display_name)
                        ->setBody($attrs['content'].$pixel, 'text/html');

                    $m = Yii::app()->mail;
                    $m->transportOptions = array(
                                            'host' => $fo->smtp_host,
                                            'username' => $fo->username,
                                            'password' => $fo->password,
                                            'port' => $fo->smtp_port,);
                    $c = $m->send($message);

                    $_status = 2;
                } else {
                    $_status = 9;
                }

                if (isset($_POST['mail_domain_id'])) {
                    //#$dm = Domain::model()->findByPk($attrs['domain_id']);
                    if ($dm) {
                        if (!in_array($dm->touched_status, array(6,20))) {
                            $dm->touched_status = $_status;
                        }
                        $dm->touched = $_sendtime;
                        $dm->touched_by = Yii::app()->user->id;
                        $dm->save();
                    }
                }
                $rtn['success'] = true;
                /*
                Yii::app()->mail
                    ->setHost($fo->smtp_host)
                    ->setPort($fo->smtp_port)
                    ->setUsername($fo->username)
                    ->setPassword($fo->password)
                    ->send($message);
                    */
            } else {
                //##print_r($model->getErrors());
                $rtn['success'] = false;
                $rtn['message'] = "Failure, Please try again.";
            }
            echo CJSON::encode($rtn);
            exit;
		}
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Email::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='email-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
