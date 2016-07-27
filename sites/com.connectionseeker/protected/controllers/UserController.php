<?php

class UserController extends RController
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
			//'accessControl', // perform access control for CRUD operations
			'rights', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
     /*
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform the following actions
				'actions'=>array('index','view','create','update'),
				//'users'=>array('*'),
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
    */

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
		$model=new User;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
        $auth = new AuthAssignment;

		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			if($model->save()) {
                $auth->attributes=$_POST['AuthAssignment'];
                $auth->userid = $model->id;
                $auth->save();
				$this->redirect(array('view','id'=>$model->id));
            }
		}

		$this->render('create',array(
			'model'=>$model,
			'auth'=>$auth,
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
        $auth = $model->rauthassignment;

		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			if($model->save()) {
                $auth->attributes=$_POST['AuthAssignment'];
                $auth->save();
				$this->redirect(array('view','id'=>$model->id));
            }
		}

        if ($model->duty_campaign_ids) $model->duty_campaign_ids = unserialize($model->duty_campaign_ids);
		$this->render('update',array(
			'model'=>$model,
			'auth'=>$auth,
		));
	}

	/**
	 * Generates apiKey & secretKey model.
	 * If generate is successful, the browser will shows up the APIKEY & SECRETKEY & CLIENT_ID
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionGenerate($id)
	{
		$model=$this->loadModel($id);

        if (Yii::app()->request->isAjaxRequest) {
            $rs = array();
            $rs["apikey"] = $model->apikey = md5($model->username.".".$id.".".time());
            $rs["secretkey"] = $model->secretkey = uniqid("ConnectionSeeker.", true);
            $rs["client_id"] = $model->client_id;

            $rs['msg'] = "Generate ApiKey Failure!";
            $rs['success'] = false;
            unset($model->password);
            if($model->save()) {
                $rs['msg'] = "Generate ApiKey Successfully!";
                $rs['success'] = true;
                echo CJSON::encode($rs);
                Yii::app()->end();
            } else {
                print_r($model->getErrors());
            }
        } else {
            $this->render('generate',array(
                'model'=>$model,
            ));
        }

	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
        if ($id == 1 || strtolower($this->loadModel($id)->username) == 'admin') {
            throw new CHttpException(901,'Invalid request. You have no permission delete admin.');
            exit ;
        }

        if ($id == Yii::app()->user->id) {
            throw new CHttpException(900,'Invalid request. Please do not delete yourself.');
            exit ;
        }

        if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			//$this->loadModel($id)->delete();

            $model=$this->loadModel($id);
            $model->status = 0;
            $model->password2 = $model->password;
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
        // page size drop down changed
        if (isset($_GET['pageSize'])) {
            Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
            unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
        }

		$model=new User('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['User']))
			$model->attributes=$_GET['User'];

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
		$model=User::model()->findByPk($id);
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
