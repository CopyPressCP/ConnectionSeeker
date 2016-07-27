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
			//'accessOwn + view,update,delete', // perform customize additional access control for CRUD operations
		);
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
		if(isset($_GET['Email']))
			$model->attributes=$_GET['Email'];

		$this->render('index',array(
			'model'=>$model,
		));
	}

	/**
	 * Email Report, it will shows up the open rate & click rate for each template.
	 */
	public function actionTreport()
	{
        //create last 7 days date time;
        for ($i = 1; $i <=7; $i++) {
            $diff = 7 - $i;
            $last7[$i] = date("Y-m-d", strtotime("-{$diff} days"));;
        }
        //echo print_r($last7); exit;

        $total7days = Yii::app()->db->createCommand()->select('count(*) as total')->from('{{email_event}}')
             ->where("template_id > 0 AND (TO_DAYS(NOW())-TO_DAYS(FROM_UNIXTIME(created)) < 7)")->queryRow();
        $total7 = $total7days['total'];

        $total30days = Yii::app()->db->createCommand()->select('count(*) as total')->from('{{email_event}}')
             ->where("template_id > 0 AND (TO_DAYS(NOW())-TO_DAYS(FROM_UNIXTIME(created)) < 30)")->queryRow();
        $total30 = $total30days['total'];

        $totaldays = Yii::app()->db->createCommand()->select('count(*) as total')->from('{{email_event}}')
             ->where("template_id > 0")->queryRow();
        $total = $totaldays['total'];

        $events = array('processed','deferred','delivered','open','click','bounce','dropped','spamreport','unsubscribe');
        $day1 = array_fill_keys($events, '0');//fill the event array, 0 as default.
        $day2 = $day3 = $day4 = $day5 = $day6 = $day7 = $dayt7 = $dayt30 = $dayall = $day1;
        //print_r($dayt30);exit;
        $d1 = date("Y-m-d", strtotime("-6 days"));
        //echo $d1; exit;


        $report = array();
        $template = Yii::app()->db->createCommand()->select("id AS template_id, subject")->from('{{template}}')->queryAll();
        if ($template) {
            foreach ($template as $v) {
                $report[$v['template_id']]['template_id'] = $v['template_id'];
                $report[$v['template_id']]['subject'] = $v['subject'];
                $report[$v['template_id']]['total30open'] = 0;
                $report[$v['template_id']]['total30click'] = 0;
                $report[$v['template_id']]['total7open'] = 0;
                $report[$v['template_id']]['total7click'] = 0;
                $report[$v['template_id']]['totalopen'] = 0;
                $report[$v['template_id']]['totalclick'] = 0;
                $report[$v['template_id']]['total30'] = $total30;
                $report[$v['template_id']]['total7'] = $total7;
                $report[$v['template_id']]['total'] = $total;
                $report[$v['template_id']]['totalreply'] = 0;
                $report[$v['template_id']]['total7reply'] = 0;
                $report[$v['template_id']]['total30reply'] = 0;
            }
        }

        $events30 = Yii::app()->db->createCommand()
             ->select("template_id, event, count(*) AS count")->from('{{email_event}}')
             ->where("template_id > 0 AND (TO_DAYS(NOW())-TO_DAYS(FROM_UNIXTIME(created)) <30) AND event IN ('open', 'click')")
             ->group('template_id,event')->queryAll();
        if ($events30) {
            foreach ($events30 as $v) {
                $tmpid = $v['template_id'];
                if ($v['event'] == "open") $report[$tmpid]['total30open'] = $v['count'];
                if ($v['event'] == "click") $report[$tmpid]['total30click'] = $v['count'];
            }
        }

        $eventslifetime = Yii::app()->db->createCommand()
             ->select("template_id, event, count(*) AS count")->from('{{email_event}}')
             ->where("template_id > 0 AND event IN ('open', 'click')")
             ->group('template_id,event')->queryAll();
        if ($eventslifetime) {
            foreach ($eventslifetime as $v) {
                $tmpid = $v['template_id'];
                if ($v['event'] == "open") $report[$tmpid]['totalopen'] = $v['count'];
                if ($v['event'] == "click") $report[$tmpid]['totalclick'] = $v['count'];
            }
        }

        $last7days = Yii::app()->db->createCommand()
             ->select("template_id, event, count(*) AS count, FROM_UNIXTIME(created, '%Y-%m-%d') AS day")
             ->from('{{email_event}}')
             ->where("template_id > 0 AND (TO_DAYS(NOW())-TO_DAYS(FROM_UNIXTIME(created)) <7)")
             ->group('template_id,event,day')->queryAll();

        //print_r($report);
        if ($last7days) {
            foreach ($last7days as $v) {
                $tmpid = $v['template_id'];
                //$report[$tmpid]['template_id'] = $v['template_id'];
                $_dk = array_search($v['day'], $last7);
                $report[$tmpid]['days'][$_dk]['day'] = $v['day'];
                $report[$tmpid]['days'][$_dk][$v['event']] = $v['count'];
                $report[$tmpid]['days'][$_dk]['total'] += $v['count'];
                if (in_array($v['event'],array("open","click"))) {
                    $report[$tmpid]['total7'.$v['event']] += $v['count'];
                }
            }
        }
        //print_r($report);

        $last7sends = Yii::app()->db->createCommand()
             ->select("template_id, count(*) AS count, DATE_FORMAT(send_time, '%Y-%m-%d') AS day")->from('{{email_queue}}')
             //->where("template_id > 0 AND (DATE_FORMAT(send_time, '%Y-%m-%d') IN ('".implode("','", array_values($last7))."'))")
             ->where("template_id > 0 AND (TO_DAYS(NOW()) - TO_DAYS(send_time) < 7) AND (created_by > 0)")
             ->group('template_id,day')->queryAll();
        //print_r($last7sends);
        if ($last7sends) {
            foreach ($last7sends as $v) {
                $tmpid = $v['template_id'];
                //$report[$tmpid]['template_id'] = $v['template_id'];
                $_dk = array_search($v['day'], $last7);
                $report[$tmpid]['days'][$_dk]['day'] = $v['day'];
                $report[$tmpid]['days'][$_dk]['internalsend'] = $v['count'];
                $report[$tmpid]['total7internalsend'] += $v['count'];
            }
        }

        $last30sends = Yii::app()->db->createCommand()
             ->select("template_id, count(*) AS count")->from('{{email_queue}}')
             ->where("(TO_DAYS(NOW()) - TO_DAYS(send_time) < 30) AND (created_by > 0)")->group('template_id')->queryAll();
        if ($last30sends) {
            foreach ($last30sends as $v) {
                $tmpid = $v['template_id'];
                $report[$tmpid]['total30internalsend'] = $v['count'];
            }
        }

        $lifetimesends = Yii::app()->db->createCommand()
             ->select("template_id, count(*) AS count")->from('{{email_queue}}')
             ->where("(send_time > 0) AND (created_by > 0)")->group('template_id')->queryAll();
        if ($lifetimesends) {
            foreach ($lifetimesends as $v) {
                $tmpid = $v['template_id'];
                $report[$tmpid]['totalinternalsend'] = $v['count'];
            }
        }


        #############################################################################################
        $last7replies = Yii::app()->db->createCommand()
             ->select("template_id, count(*) AS count, DATE_FORMAT(send_time, '%Y-%m-%d') AS day")->from('{{email_queue}}')
             //->where("template_id > 0 AND (DATE_FORMAT(send_time, '%Y-%m-%d') IN ('".implode("','", array_values($last7))."'))")
             ->where("(template_id>0) AND (is_reply = 1) AND (parent_id>0) AND (created_by IS NULL) AND (TO_DAYS(NOW()) - TO_DAYS(send_time) < 7)")
             ->group('template_id,day')->queryAll();
        if ($last7replies) {
            foreach ($last7replies as $v) {
                $tmpid = $v['template_id'];
                //$report[$tmpid]['template_id'] = $v['template_id'];
                $_dk = array_search($v['day'], $last7);
                $report[$tmpid]['days'][$_dk]['day'] = $v['day'];
                $report[$tmpid]['days'][$_dk]['internalreply'] = $v['count'];
                $report[$tmpid]['total7reply'] += $v['count'];
            }
        }

        $last30replies = Yii::app()->db->createCommand()
             ->select("template_id, count(*) AS count")->from('{{email_queue}}')
             ->where("template_id > 0 AND (is_reply = 1) AND (parent_id>0) AND (created_by IS NULL) AND (TO_DAYS(NOW()) - TO_DAYS(send_time) < 30)")
             ->group('template_id')->queryAll();
        if ($last30replies) {
            foreach ($last30replies as $v) {
                $tmpid = $v['template_id'];
                $report[$tmpid]['total30reply'] = $v['count'];
            }
        }

        $lifetimereplies = Yii::app()->db->createCommand()
             ->select("template_id, count(*) AS count")->from('{{email_queue}}')
             ->where("template_id > 0 AND (is_reply = 1) AND (parent_id>0) AND (created_by IS NULL) AND (send_time > 0)")
             ->group('template_id')->queryAll();
        if ($lifetimereplies) {
            foreach ($lifetimereplies as $v) {
                $tmpid = $v['template_id'];
                $report[$tmpid]['totalreply'] = $v['count'];
            }
        }
        #############################################################################################


		$this->render('treport',array(
			'report'=>$report,
			'total7'=>$total7,
			'last7'=>$last7,
			'events'=>$events,
			'total30'=>$total30,
		));
        //Yii::app()->end();
	}

	/**
	 * Email Report, it will shows up the open rate & click rate for each template.
	 */
	public function actionReport()
	{
        $mmodel = new Mailer;
        if (isset($_GET['pageSize'])) {
            Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
            unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
        }

		$model=new Mailer('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Mailer']))
			$model->attributes=$_GET['Mailer'];

		$this->render('report',array(
			'model'=>$model,
		));
	}

	public function actionMailerreport()
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

            $inwhere = " AND `from` IN(".implode(",", array_values($ids)).")";
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
                $currentstamp = $$_curr;

                $lastopen = Yii::app()->db->createCommand()
                     ->select("`from`, count(*) AS count")->from('{{email_queue}}')
                     ->where("(template_id>0) AND (is_reply=0 OR (is_reply IS NULL)) AND (parent_id=0 OR (parent_id IS NULL)) AND (created_by>0) AND (opened>$currentstamp)".$inwhere)
                     ->group('from')->queryAll();
                if ($lastopen) {
                    foreach ($lastopen as $v) {
                        $mailerid = $v['from'];
                        $report[$mailerid]['open'][$_last] = $v['count'];
                    }
                }

                #####!!!!!!!!!!!!!!!!!!!!!!###############
                $currentstamp = date("Y-m-d H:i:s", $currentstamp);

                $lastsent = Yii::app()->db->createCommand()
                     ->select("`from`, count(*) AS count")->from('{{email_queue}}')
                     ->where("(template_id>0) AND (is_reply=0 OR (is_reply IS NULL)) AND (parent_id=0 OR (parent_id IS NULL)) AND (created_by>0) AND (send_time>'$currentstamp')".$inwhere)
                     ->group('from')->queryAll();
                if ($lastsent) {
                    foreach ($lastsent as $v) {
                        $mailerid = $v['from'];
                        $report[$mailerid]['sent'][$_last] = $v['count'];
                    }
                }

                $lastreply = Yii::app()->db->createCommand()
                     ->select("`from`, count(*) AS count")->from('{{email_queue}}')
                     ->where("(is_reply = 1) AND (parent_id>0) AND (created_by IS NULL) AND (send_time>'$currentstamp')".$inwhere)
                     ->group('from')->queryAll();
                if ($lastreply) {
                    foreach ($lastreply as $v) {
                        $mailerid = $v['from'];
                        $report[$mailerid]['reply'][$_last] = $v['count'];
                    }
                }
            }
            $rs["report"] = $report;

            /*
            $criteria = new CDbCriteria();
            $criteria->addInCondition('from', $ids);
            $numbers = Email::model()->findAll($criteria);
            if ($numbers) {
                //do nothing for now;
            }
            */
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
                                $do->touched = $created;
                                $do->touched_status = $_status;
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

            }
        }

        //print_r($_GET);
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

                    // where mailer_id = $e['from'] and domain_id = $e['domain_id'];
                    $parentmdl = Email::model()->findByAttributes(array('from'=>$e['from'],'domain_id'=>$e['domain_id']),
                                                                        'id!=:eid', array(':eid'=>$e['id']));
                    $parent_id = 0;
                    if ($parentmdl) {
                        $parent_id = $parentmdl->id;
                    }

                    $pixel = "<img src='http://www.connectionseeker.com/index.php?r=email/open&id=".$e['id']."' width='1' height='1' />";

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
                        $dm = Domain::model()->findByPk($e['domain_id']);
                        $dm->touched = $_sendtime;
                        $dm->touched_status = 2;
                        $dm->touched_by = Yii::app()->user->id;
                        $dm->save();
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
        $attrs['to'] = $_POST['mailto'];
        $attrs['subject'] = $_POST['subject'];
        $attrs['content'] = $_POST['message'];

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
			$model->attributes=$attrs;
			if($model->save()) {
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
                    $dm = Domain::model()->findByPk($attrs['domain_id']);
                    $dm->touched = $_sendtime;
                    $dm->touched_status = $_status;
                    $dm->touched_by = Yii::app()->user->id;
                    $dm->save();
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
