<?php

class DiscoveryBacklinkController extends RController
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
			//'accessControl', // perform customize additional access control for CRUD operations
			'rights', // perform access control for CRUD operations
			//'accessOwn + view,update', // perform customize additional access control for CRUD operations
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
		$model=new DiscoveryBacklink;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['DiscoveryBacklink']))
		{
			$model->attributes=$_POST['DiscoveryBacklink'];
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

		if(isset($_POST['DiscoveryBacklink']))
		{
			$model->attributes=$_POST['DiscoveryBacklink'];
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

		$model=new DiscoveryBacklink('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['DiscoveryBacklink']))
			$model->attributes=$_GET['DiscoveryBacklink'];

		$this->render('index',array(
			'model'=>$model,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionCompared()
	{
        if (isset($_GET['pageSize'])) {
            Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
            unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
        }

		//##$model=new DiscoveryBacklink('search');
		$model=new DiscoveryBackdomain('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['DiscoveryBackdomain']))
			$model->attributes=$_GET['DiscoveryBackdomain'];
        $model->autorule = null;
        //print_r($model->attributes);

		$this->render('compared',array(
			'model'=>$model,
		));
	}

	/**
	 * Blacklist Back Domain.
	 */
	public function actionBlacklist()
	{
        $ids = $_REQUEST['ids'];
        if (!is_array($ids)) {
            $ids = array($ids);
        }

        if (empty($ids)) {
            $rs['msg'] = "Please provide at least one back domain";
            $rs['success'] = false;
            echo CJSON::encode($rs);
            Yii::app()->end();
        }

        $model = new DiscoveryBackdomain;
        $blmodel = new Blacklistforauto;

        $i = 0;
        foreach($ids as $id) {
            $m = $model->findByPk($id);
            if ($m) {
                $bmdl = $blmodel->findByAttributes(array('domain_id' => $m->domain_id));
                if (!$bmdl) {
                    $blmodel->setIsNewRecord(true);
                    $blmodel->id=NULL;
                    $blmodel->domain_id = $m->domain_id;
                    $blmodel->domain = $m->domain;
                    if($blmodel->save()){
                        //do nothing for now;
                    }
                    $i++;
                }
            }//else we should contine it.
        }

        $was = ($i>1) ? "were" : "was";
        $rs['msg'] = "$i domains $was sent to blacklist.";
        $rs['success'] = true;
        echo CJSON::encode($rs);
        Yii::app()->end();
	}


	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=DiscoveryBacklink::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='discovery-backlink-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
