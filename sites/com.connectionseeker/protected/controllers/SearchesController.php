<?php

class SearchesController extends RController
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
				'actions'=>array('index','view','create','update','admin','delete'),
				'users'=>array('@'),
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
        if (Yii::app()->request->isAjaxRequest) {
            $model = $this->loadModel($id);
            $rs['searches'] = unserialize($model->searches);
            echo CJSON::encode($rs);
            Yii::app()->end();
        } else {
            $this->render('view',array(
                'model'=>$this->loadModel($id),
            ));
        }
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
        $rs['success'] = false;
        $rs['msg'] = "Saved failure, Please try it again!";

        $attr = array();
        if (isset($_POST['r']) && $_POST['r']) {
            $rarr = explode("/", $_POST['r']);
            $attr["ctrl_name"] = $rarr[0];
            if (count($rarr) > 1) {
                $attr["view_name"] = $rarr[1];
            } else {
                $attr["view_name"] = "index";
            }
        } else {
            $attr["ctrl_name"] = "domain";
            $attr["view_name"] = "outreach";
        }

        $attr["name"] = $_POST['Searches']['name'];
        unset($_POST['Searches']);
        unset($_POST['r']);
        unset($_POST['searches_autofill']);
        $attr["searches"] = serialize($_POST);
        $model=new Searches;
		if(!empty($attr)){
			$model->attributes = $attr;
            if ($model->save()) {
                $rs['msg'] = "This auto-fill was saved successfully!";
                $rs['success'] = true;
                $rs['id'] = $model->id;
                $rs['name'] = $model->name;
            }
		}

        echo CJSON::encode($rs);
        Yii::app()->end();

        /*
		$model=new Searches;
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Searches']))
		{
			$model->attributes=$_POST['Searches'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
        */
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

		if(isset($_POST['Searches']))
		{
			$model->attributes=$_POST['Searches'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
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

		$model=new Searches('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Searches']))
			$model->attributes=$_GET['Searches'];

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
		$model=Searches::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='searches-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
