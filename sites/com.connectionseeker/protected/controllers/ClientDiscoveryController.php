<?php

class ClientDiscoveryController extends RController
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
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('index','view','create','update','queue','cloneit','delete'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				//'actions'=>array('admin','delete'),
				'actions'=>array('admin'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
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
		$model=new ClientDiscovery;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['ClientDiscovery']))
		{
            if(isset($_POST['Automation']))
            {
                if (isset($_POST['Automation']['mailer']) && $_POST['Automation']['mailer']) {
                    $mailer = $_POST['Automation']['mailer'];
                    $template = $_POST['Automation']['template'];
                    $frequency = $_POST['Automation']['frequency'];
                    $mailers = array();
                    foreach ($mailer as $k => $v) {
                        if (empty($v)) continue;
                        if (empty($template[$k])) continue;
                        if (empty($frequency)) $frequency=5;
                        $mailers[$v]['frequency'] = $frequency;

                        $mailers[$v]['mailer'] = $v;
                        $mailers[$v]['template'] = implode("|", array_values($template[$k]));
                    }
                    $_POST['Automation']['mailers'] = serialize($mailers);
                }
                $_POST['ClientDiscovery']['automation_setting'] = json_encode($_POST['Automation']);
            }
			$model->attributes=$_POST['ClientDiscovery'];
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

		if(isset($_POST['ClientDiscovery']))
		{
            if(isset($_POST['Automation']))
            {
                if (isset($_POST['Automation']['mailer']) && $_POST['Automation']['mailer']) {
                    $mailer = $_POST['Automation']['mailer'];
                    $template = $_POST['Automation']['template'];
                    $frequency = $_POST['Automation']['frequency'];
                    $mailers = array();
                    foreach ($mailer as $k => $v) {
                        if (empty($v)) continue;
                        if (empty($template[$k])) continue;
                        if (empty($frequency)) $frequency=5;
                        $mailers[$v]['frequency'] = $frequency;

                        $mailers[$v]['mailer'] = $v;
                        $mailers[$v]['template'] = implode("|", array_values($template[$k]));
                    }
                    $_POST['Automation']['mailers'] = serialize($mailers);
                }
                $_POST['ClientDiscovery']['automation_setting'] = json_encode($_POST['Automation']);
            }
			$model->attributes=$_POST['ClientDiscovery'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionCloneit($id)
	{
		$model=$this->loadModel($id);

		$this->render('cloneit',array(
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
			// we only allow deletion via POST request
			//##$this->loadModel($id)->delete();

            $model=$this->loadModel($id);
            $model->status = 0;
            $model->save();

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

		$model=new ClientDiscovery('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['ClientDiscovery']))
			$model->attributes=$_GET['ClientDiscovery'];

        define('DS', DIRECTORY_SEPARATOR);
        $queuefile = dirname(dirname(__FILE__)) . DS . "runtime" . DS . "nofqueues.json";
        $queueobj = @file_get_contents($queuefile);
        if (!$queueobj) {
            $queue = array("querytime"=>date("Y-m-d H:i:s"),"total"=>0,"total_potential"=>0);
            $queueobj = json_encode($queue);
        }

		$this->render('index',array(
			'model'=>$model,
			'queueobj'=>$queueobj,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionQueue($id)
	{
        $automodel=$this->loadModel($id);

        if (isset($_GET['pageSize'])) {
            Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
            unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
        }

        //###$model=new Domain('search');
        $model=new DiscoveryBackdomain('search');
        $model->unsetAttributes();  // clear any default values
        $musthasowner = 0;
        if ($automodel && $automodel->status==1 && $automodel->complete_with_automation == 1) {
            if(isset($_GET['Domain']))
                $model->attributes=$_GET['Domain'];

            $autorule = json_decode($automodel->automation_setting);
            $model->autorule = $autorule;
            $model->discovery_id = $id;
            $model->mailer_id = 0;
            /*
            print_r($autorule);
            if (!empty($autorule->touched_status)) $model->touched_status = $autorule->touched_status;
            //##if (!empty($autorule->category)) $model->category = array_values($autorule->category);
            if (!empty($autorule->alexarank)) $model->alexarank = $autorule->alexarank;
            if (!empty($autorule->semrushkeywords)) $model->semrushkeywords = $autorule->semrushkeywords;
            if (!empty($autorule->mozauthority)) $model->mozauthority = $autorule->mozauthority;
            if (!empty($autorule->has_owner) && $autorule->has_owner == 1) {
                $musthasowner = 1;
            }
            */

            if ($autorule->current_domain_id) {
                /*
                if ($automodel->sortby) { //if it sort by id desc;
                    $model->id = "<".$automodel->current_domain_id;
                } else { //sort by id asc
                    $model->id = ">".$automodel->current_domain_id;
                }
                */
            }
        } else {
            $model->id = -1;
        }

		$this->render('queue',array(
			'model'=>$model,
			'automodel'=>$autorule,
			//'musthasowner'=>$musthasowner,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=ClientDiscovery::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='client-discovery-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
