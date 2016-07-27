<?php
class SettingController extends RController
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
			//'rights', // perform access control for CRUD operations
			'accessOwn + profile', // perform customize additional access control for CRUD operations
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
			array('allow',  // allow all users to perform the following actions
				'actions'=>array('profile','mailer','template','createMailer','updateMailer','createTemplate','updateTemplate'),
				'users'=>array('@'),
			),
            /*
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
            */
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * @return array action filters
     * We can build one filter file, and put this function into the filter file
	 */
    public function filterAccessOwn($filterChain) {
        $allow = true;

        //$cuid = Yii::app()->user->id;
        //$roles = Yii::app()->authManager->getRoles($cuid);

        if ($_GET['id']) {
            $model=$this->loadModel($_GET['id']);
            if ($model->id == Yii::app()->user->id) {
                $filterChain->run();
            } else {
                $allow = false;
            }
        } else {
            $allow = false;
        }

        if ($allow === false) {
            $filterChain->controller->accessDenied();
            return false;
        }
    }

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionProfile($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			if($model->save()) {
                Yii::app()->user->setFlash('success', "Profile saved Successfully!");
				//$this->redirect(array('view','id'=>$model->id));
            }
		}

		$this->render('/user/profile',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will flush the success information.
	 * @no param
	 */
	public function actionMailer()
	{
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
        $cuid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($cuid);
        if (isset($roles["Outreach"]) || isset($roles["InternalOutreach"])) {
            //We do this, just for different tab. so here the action mailer will under setting tab.

            $model=new Mailer('search');
            $model->unsetAttributes();  // clear any default values
            if(isset($_GET['Mailer']))
                $model->attributes=$_GET['Mailer'];
            $model->created_by = $cuid;

            $this->render('/mailer/index',array(
                'model'=>$model,
                'roles'=>$roles,
            ));
        } else {
            //redirect it to the mailer managemant
            $this->redirect(array('/mailer'));
            Yii::app()->end();
        }

        /*
		$mdl = new Mailer;
        $model = $mdl->findByAttributes(array("created_by"=>$cuid));
        if ($model) {
            $model->setIsNewRecord(false);
            $model->setScenario('update');
        } else {
            $model = $mdl;
            $model->setIsNewRecord(true);
            $model->id=NULL;
        }

		if(isset($_POST['Mailer'])) {
			$model->attributes=$_POST['Mailer'];
			if($model->save())
                Yii::app()->user->setFlash('success', "Mailer saved successfully!");
		}

		$this->render('/mailer/_form',array(
			'model'=>$model,
		));
        */
	}

	/**
	 * Create a particular mailer item.
	 * If update is successful, the browser will flush the success information.
	 * @no param
	 */
	public function actionCreateMailer()
	{
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
        $cuid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($cuid);
        if (isset($roles["Outreach"]) || isset($roles["InternalOutreach"])) {
            //We do this, just for different tab. so here the action mailer will under setting tab.
            $model=new Mailer;

            // Uncomment the following line if AJAX validation is needed
            // $this->performAjaxValidation($model);

            if(isset($_POST['Mailer']))
            {
                $model->attributes=$_POST['Mailer'];
                if($model->save())
                    $this->redirect(array('mailer','id'=>$model->id));//redirect to setting/mailer
            }

            $this->render('/mailer/_form',array(
                'model'=>$model,
                'roles'=>$roles,
            ));
        } else {
            //redirect it to the mailer managemant
            $this->redirect(array('/mailer'));
            Yii::app()->end();
        }
	}

	/**
	 * Updates a particular mailer item.
	 * If update is successful, the browser will flush the success information.
	 * @no param
	 */
	public function actionUpdateMailer($id)
	{
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
        $cuid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($cuid);
        if (isset($roles["Outreach"]) || isset($roles["InternalOutreach"])) {
            //We do this, just for different tab. so here the action mailer will under setting tab.
            $model=Mailer::model()->findByPk($id);

            if ($cuid != $model->created_by) {
                throw new CHttpException(403,'You have no permission change this mailer item.');
            }

            // Uncomment the following line if AJAX validation is needed
            // $this->performAjaxValidation($model);
            if(isset($_POST['Mailer']))
            {
                $model->attributes=$_POST['Mailer'];
                if($model->save())
                    Yii::app()->user->setFlash('success', "Mailer saved successfully!");
                    //$this->redirect(array('view','id'=>$model->id));
            }

            $this->render('/mailer/_form',array(
                'model'=>$model,
                'roles'=>$roles,
            ));
        } else {
            //redirect it to the mailer managemant
            $this->redirect(array('/mailer'));
            Yii::app()->end();
        }
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will flush the success information.
	 * @no param
	 */
	public function actionTemplate()
	{
        $cuid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($cuid);
        if (isset($roles["Outreach"]) || isset($roles["InternalOutreach"])) {
            //We do this, just for different tab. so here the action mailer will under setting tab.

            $model=new Template('search');
            $model->unsetAttributes();  // clear any default values
            if(isset($_GET['Template']))
                $model->attributes=$_GET['Template'];
            $model->created_by = $cuid;

            $this->render('/template/index',array(
                'model'=>$model,
                'roles'=>$roles,
            ));
        } else {
            //redirect it to the Template managemant
            $this->redirect(array('/template'));
            Yii::app()->end();
        }

        /*
		$mdl = new Template;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
        $cuid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($cuid);
        if (isset($roles["Outreach"]) || isset($roles["InternalOutreach"])) {
            //do nothing for now;
        } else {
            //redirect it to the mailer managemant
            $this->redirect(array('/template'));
            Yii::app()->end();
        }

        $model = $mdl->findByAttributes(array("created_by"=>$cuid));
        if ($model) {
            $model->setIsNewRecord(false);
            $model->setScenario('update');
        } else {
            $model = $mdl;
            $model->setIsNewRecord(true);
            $model->id=NULL;
        }

		if(isset($_POST['Template'])) {
			$model->attributes=$_POST['Template'];
			if($model->save())
                Yii::app()->user->setFlash('success', "Template saved successfully!");
		}

		$this->render('/template/_form',array(
			'model'=>$model,
		));
        */
	}

	/**
	 * Create a particular template item.
	 * If update is successful, the browser will flush the success information.
	 * @no param
	 */
	public function actionCreateTemplate()
	{
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
        $cuid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($cuid);
        if (isset($roles["Outreach"]) || isset($roles["InternalOutreach"])) {
            //We do this, just for different tab. so here the action mailer will under setting tab.
            $model=new Template;

            // Uncomment the following line if AJAX validation is needed
            // $this->performAjaxValidation($model);

            if(isset($_POST['Template']))
            {
                $model->attributes=$_POST['Template'];
                if($model->save())
                    $this->redirect(array('template','id'=>$model->id));//redirect to setting/mailer
            }

            $this->render('/template/_form',array(
                'model'=>$model,
                'roles'=>$roles,
            ));
        } else {
            //redirect it to the Template managemant
            $this->redirect(array('/template'));
            Yii::app()->end();
        }
	}

	/**
	 * Updates a particular template item.
	 * If update is successful, the browser will flush the success information.
	 * @no param
	 */
	public function actionUpdateTemplate($id)
	{
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
        $cuid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($cuid);
        if (isset($roles["Outreach"]) || isset($roles["InternalOutreach"])) {
            //We do this, just for different tab. so here the action mailer will under setting tab.
            $model=Template::model()->findByPk($id);

            if ($cuid != $model->created_by) {
                throw new CHttpException(403,'You have no permission change this template item.');
            }

            // Uncomment the following line if AJAX validation is needed
            // $this->performAjaxValidation($model);
            if(isset($_POST['Template']))
            {
                $model->attributes=$_POST['Template'];
                if($model->save())
                    Yii::app()->user->setFlash('success', "Template saved successfully!");
                    //$this->redirect(array('view','id'=>$model->id));
            }

            $this->render('/template/_form',array(
                'model'=>$model,
                'roles'=>$roles,
            ));
        } else {
            //redirect it to the Template managemant
            $this->redirect(array('/template'));
            Yii::app()->end();
        }
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
