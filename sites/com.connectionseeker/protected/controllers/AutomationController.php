<?php

class AutomationController extends RController
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
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
        //##phpinfo();
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
     * this function was discarded
	 */
	public function actionSetting()
	{
        $this->redirect(array('index'));

        if (isset($_GET['id'])) {
		    $model=$this->loadModel($_GET['id']);
        } else {
            $model = Automation::model()->find(array('order'=>'id DESC'));
            if (!$model) $model=new Automation;
        }

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Automation']))
		{
            $model->setIsNewRecord(true);
            $model->id=NULL;
			$model->attributes=$_POST['Automation'];
			if($model->save()) {
				//$this->redirect(array('view','id'=>$model->id));
            }
		}

		$this->render('setting',array(
			'model'=>$model,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Automation;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
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
                    //##if (empty($frequency[$k])) $frequency[$k] = 5;
                    //##$mailers[$v]['frequency'] = $frequency[$k];
                    if (empty($frequency)) $frequency=5;
                    $mailers[$v]['frequency'] = $frequency;

                    $mailers[$v]['mailer'] = $v;
                    $mailers[$v]['template'] = implode("|", array_values($template[$k]));
                }
                $_POST['Automation']['mailers'] = serialize($mailers);
            }

            //print_r($_POST['Automation']);
			$model->attributes=$_POST['Automation'];
			if($model->save())
				$this->redirect(array('index'));
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
                    //##if (empty($frequency[$k])) $frequency[$k] = 5;
                    //##$mailers[$v]['frequency'] = $frequency[$k];
                    if (empty($frequency)) $frequency=5;
                    $mailers[$v]['frequency'] = $frequency;

                    $mailers[$v]['mailer'] = $v;
                    $mailers[$v]['template'] = implode("|", array_values($template[$k]));
                }
                $_POST['Automation']['mailers'] = serialize($mailers);
            }

			$model->attributes=$_POST['Automation'];
			if($model->save()) {
				//$this->redirect(array('index'));
            }
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionIndex()
	{
        //##$this->redirect(array('setting'));
        if (isset($_GET['pageSize'])) {
            Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
            unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
        }

        if (!isset($_GET['Automation_sort'])) {
            $_GET['Automation_sort'] = "id.desc";
        }

        if (!isset($_GET['Automation']['status'])) {
            $_GET['Automation']['status'] = 1;
        }

		$model=new Automation('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Automation']))
			$model->attributes=$_GET['Automation'];

        $cuid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($cuid);

		$this->render('index',array(
			'model'=>$model,
			'roles'=>$roles,
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
			// $this->loadModel($id)->delete();
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
	public function actionQueue()
	{
        if (isset($_GET['pageSize'])) {
            Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
            unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
        }

        $automodel = Automation::model()->actived()->find(array('order'=>'id DESC'));
        $model=new Domain('search');
        $model->unsetAttributes();  // clear any default values
        $musthasowner = 0;
        if ($automodel) {
            if(isset($_GET['Domain']))
                $model->attributes=$_GET['Domain'];

            if (!empty($automodel->touched_status)) $model->touched_status = explode("|", $automodel->touched_status);
            if (!empty($automodel->category)) $model->category = explode("|", $automodel->category);
            if (!empty($automodel->alexarank)) $model->alexarank = $automodel->alexarank;
            if (!empty($automodel->semrushkeywords)) $model->semrushkeywords = $automodel->semrushkeywords;
            if (!empty($automodel->mozauthority)) $model->mozauthority = $automodel->mozauthority;
            if (!empty($automodel->has_owner) && $automodel->has_owner == 1) {
                $musthasowner = 1;
            }

            if ($automodel->current_domain_id) {
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
			'automodel'=>$automodel,
			'musthasowner'=>$musthasowner,
		));
	}


	public function actionSent()
	{
        if (isset($_GET['pageSize'])) {
            Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
            unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
        }

        //##$_GET['AutomationSent']['sent'] = ">2015-10-01";

        $model=new AutomationSent('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['AutomationSent']))
            $model->attributes=$_GET['AutomationSent'];

        if ($_GET['type'] && strtolower($_GET['type']) == 'client_discovery_id') {
            $model->type_of_automation = 'client_discovery_id';
            if ($_GET['client_discovery_id']) {
                $model->client_discovery_id = $_GET['client_discovery_id'];
            }
        } else {
            $model->type_of_automation = 'automation_id';
        }

		$this->render('sent',array(
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
		$model=Automation::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='automation-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
