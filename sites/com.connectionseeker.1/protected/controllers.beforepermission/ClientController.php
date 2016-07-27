<?php

class ClientController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
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
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view','campaigns'),
				//'users'=>array('*'),
				'users'=>array('@'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
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
		$model=new Client;
		$domodel=new ClientDomain;
		//$kymodel=new ClientKeyword;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		// $this->performAjaxValidation(array($model, $domodel));

		//if(isset($_POST['Client']))
		if(isset($_POST['Client'], $_POST['ClientDomain']))
		{
            $redirect = true;
			$model->attributes=$_POST['Client'];
			if($model->save()) {
                if (!empty($_POST['ClientDomain']['domain'])) {
                    foreach ($_POST['ClientDomain']['domain'] as $k => $v) {
                        if (!empty($v)) {
                            $domodel->id=NULL;
                            $domodel->client_id=$model->id;
                            $domodel->domain=$v;
                            if (!$domodel->save()) {
                                $redirect = false;
                                throw new CHttpException(400,'Client domains did not stored. Please try it again.');
                            }
                            $domodel->setIsNewRecord(true);
                        }
                    }
                }
            } else {
                $redirect = false;
                throw new CHttpException(400,'Client basic information did not stored. Please try it again.');
            }

            if ($redirect) {
                $this->redirect(array('view','id'=>$model->id));
            }
		}

		$this->render('create',array(
			'model'=>$model,
			'domodel'=>$domodel,
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
        //$domodel = ClientDomain::model()->findAllByAttributes(array('client_id'=>$id));
        //$domodel = ClientDomain::model()->findByAttributes(array('client_id'=>$id));
        $domodel = new ClientDomain;


		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Client'], $_POST['ClientDomain']))
		{
			$model->attributes=$_POST['Client'];
            //print_r($_POST['ClientDomain']);

            //$domodel->client_id=$model->id;
            $redirect = true;

			if($model->save()) {
                if (!empty($_POST['ClientDomain']['domain'])) {
                    foreach ($_POST['ClientDomain']['domain'] as $k => $v) {
                        //$cbpos = stripos($k, 'cd_');
                        if (!empty($v)) {
                            $_domodel = $domodel->find("domain=:domain AND client_id=:client_id",
                                                  array(':domain'=>$v,':client_id'=>$model->id));
                            if ($_domodel) {
                                $_domodel->setIsNewRecord(false);
                                $_domodel->setScenario('update');
                                $_domodel->domain=$v;
                                $_domodel->status=1;
                                if(!$_domodel->save()) {
                                    $redirect = false;
                                    throw new CHttpException(400,'Client domain '.$v.' did not stored. Please try it again.');
                                }
                                continue;
                            }
                        }

                        //if ($cbpos === false) {
                        if (stripos($k, 'cd_') === false) {
                            if (!empty($v)) {
                                $domodel->setIsNewRecord(true);
                                $domodel->id=NULL;
                                $domodel->client_id=$model->id;
                                $domodel->domain=$v;
                                if (!$domodel->save()) {
                                    $redirect = false;
                                    throw new CHttpException(400,'Client domains did not stored. Please try it again.');
                                }
                            }
                        } else {
                            /*
                            $domodel->id = $domainid;
                            $domodel->client_id = $model->id;
                            $domodel->domain = $v;
                            if (empty($v)) $domodel->status = 0;
                            if(!$domodel->save()) {
                            */

                            $domainid = str_ireplace("cd_", "", $k);
                            $_domodel = $domodel->findByPk($domainid);
                            $_domodel->client_id = $model->id;
                            if (empty($v)) {
                                $_domodel->status = 0;
                            } else {
                                $_domodel->domain = $v;
                            }
                            if(!$_domodel->save()) {
                                $redirect = false;
                                throw new CHttpException(400,'Client domains did not stored. Please try it again.');
                            }
                        }

                    }

                }

            } else {
                $redirect = false;
                throw new CHttpException(400,'Client basic information did not stored. Please try it again.');
            }

            if ($redirect) {
                $this->redirect(array('view','id'=>$model->id));
            }

		}

		$this->render('update',array(
			'model'=>$model,
			'domodel'=>$domodel,
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
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

    //for dropdown list
    public function actionCampaigns()
    {
        //$_GET['client_id'] = 1;
        $attrs = $rs = $data = array();
        $rs['attrs'] = "";
        $rs['campaigns'] = "";
        if ($_GET['client_id']) {
            $data = Campaign::model()->findAll('client_id=:client_id',
                      array(':client_id'=>(int) $_GET['client_id']));
        }
        if ($data) {
            if (isset($_GET['attrs']) && $_GET['attrs']) {
                $attrs = explode(",", strtolower($_GET['attrs']));
            }
            //$data = array('' => '-- Campaigns --') + CHtml::listData($data,'id','name');
            $campaigns = "";
            foreach($data as $k => $v) {
                $campaigns .= CHtml::tag('option',array('value'=>$v['id']),CHtml::encode($v['name']),true);
                if ($attrs) {
                    foreach($attrs as $av) {
                        if ($av == 'category') {
                            if ($v[$av]) {
                                $_v = explode("|", $v[$av]);
                                array_pop($_v);
                                array_shift($_v);
                                //set the first one as the default value; we will change this one when we use the multiple attr
                                $_v = $_v[0];
                            } else {
                                $_v = "";
                            }

                            $rs['attrs'][$v['id']][$av] = $_v;
                        } else {
                            $rs['attrs'][$v['id']][$av] = $v[$av];
                        }
                    }
                }
            }
            $rs['campaigns'] = $campaigns;
        }

        echo CJSON::encode($rs);
        Yii::app()->end();
    }


	/**
	 * Manages all models.
	 */
	public function actionIndex()
	{
        // page size drop down changed
        if (isset($_GET['pageSize'])) {
            Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
            unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
        }

		$model=new Client('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Client']))
			$model->attributes=$_GET['Client'];

		$this->render('index',array(
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
		$model=Client::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='client-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
