<?php

class CampaignController extends RController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using one-column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';

	/**
	 * @return array action filters
     * uncoment out the method filters, when you wanna override the rights.filters
	 */
	public function filters()
	{
		return array(
			//'accessControl',
			'rights', // perform access control for CRUD operations
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
		$model   = new Campaign;
        $ctmodel = new CampaignTask;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Campaign']))
		{
			$model->attributes=$_POST['Campaign'];
            $domain = trim($_POST['Campaign']['domain']);
            //if (empty($domain)) throw new CHttpException(400,'Invalid request. Please provide correct domain.');

            $cdmodel = new ClientDomain;
            $cdi = $cdmodel->findByAttributes(array('domain' => $domain));
            if (!empty($cdi)) {
                $model->domain_id = $cdi->id;
            } else {
                $cdmodel->domain = $domain;
                $cdmodel->client_id = $model->client_id;
                if ($cdmodel->save()) {
                    $model->domain_id = $cdmodel->id;
                }
            }

			if($model->save()) {
                $keywords = array();
                $target_urls = array();
                if (isset($_POST['CampaignTask'])) {
                    $kwcount = $_POST['CampaignTask']['kwcount'];
                    $i = 0;
                    foreach ($_POST['CampaignTask']['keyword'] as $k => $v) {
                        if (!empty($v) && $kwcount[$k] > 0) {
                            $keywords[$i]['kwcount'] = (int)$kwcount[$k];
                            $keywords[$i]['keyword'] = $v;
                            $keywords[$i]['used'] = 0;
                            $i++;
                        }
                    }
                    $i = 0;
                    foreach ($_POST['CampaignTask']['targeturl'] as $k => $v) {
                        if (!empty($v)) {
                            $target_urls[$i]['targeturl'] = $v;
                            $target_urls[$i]['used'] = 0;
                            $i++;
                        }
                    }
                    //$ctmodel->setIsNewRecord(true);
                    //$ctmodel->id=NULL;
                    $ctmodel->campaign_id = $model->id;
                    $ctmodel->keyword = serialize($keywords);
                    $ctmodel->targeturl = serialize($target_urls);
                    if ($ctmodel->save()) {
                        $this->redirect(array('view','id'=>$model->id));
                    } else {
                        $this->redirect(array('update','id'=>$model->id));
                    }
                }
				$this->redirect(array('view','id'=>$model->id));
            }
		}

		$this->render('create',array(
			'model'=>$model,
			'ctmodel'=>$ctmodel,
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
        $ctmodel = new CampaignTask;
        //$model->category = unserialize($model->category);

        $cti = $ctmodel->findByAttributes(array('campaign_id' => $model->id));
        if ($cti) {
            $ctmodel = $cti;
        }

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Campaign']))
		{
			$model->attributes=$_POST['Campaign'];
            $domain = trim($_POST['Campaign']['domain']);

            $cdmodel = new ClientDomain;
            $cdi = $cdmodel->findByAttributes(array('domain' => $domain));
            if (!empty($cdi)) {
                $model->domain_id = $cdi->id;
            } else {
                $cdmodel->domain = $domain;
                $cdmodel->client_id = $model->client_id;
                if ($cdmodel->save()) {
                    $model->domain_id = $cdmodel->id;
                }
            }

			if($model->save()) {
                $keywords = array();
                $target_urls = array();
                if (isset($_POST['CampaignTask'])) {
                    $kwcount = $_POST['CampaignTask']['kwcount'];
                    $i = 0;
                    foreach ($_POST['CampaignTask']['keyword'] as $k => $v) {
                        if (!empty($v) && $kwcount[$k] > 0) {
                            $keywords[$i]['kwcount'] = (int)$kwcount[$k];
                            $keywords[$i]['keyword'] = $v;
                            $keywords[$i]['used'] = 0;
                            $i++;
                        }
                    }
                    $i = 0;
                    foreach ($_POST['CampaignTask']['targeturl'] as $k => $v) {
                        if (!empty($v)) {
                            $target_urls[$i]['targeturl'] = $v;
                            $target_urls[$i]['used'] = 0;
                            $i++;
                        }
                    }

                    if ($cti) {
                        $ctmodel->setIsNewRecord(false);
                        $ctmodel->setScenario('update');
                    } else {
                        $ctmodel->setIsNewRecord(true);
                        $ctmodel->id=NULL;
                    }

                    $ctmodel->campaign_id = $model->id;
                    $ctmodel->keyword = serialize($keywords);
                    $ctmodel->targeturl = serialize($target_urls);
                    //print_r($ctmodel->attributes);
                    if ($ctmodel->save()) {
                        $this->redirect(array('view','id'=>$model->id));
                    } else {
                        $this->redirect(array('update','id'=>$model->id));
                    }

                }
				$this->redirect(array('view','id'=>$model->id));
            }
		}

        $ctmodel->keyword = unserialize($ctmodel->keyword);
        $ctmodel->targeturl = unserialize($ctmodel->targeturl);

		$this->render('update',array(
			'model'=>$model,
			'ctmodel'=>$ctmodel,
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

		$model=new Campaign('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Campaign']))
			$model->attributes=$_GET['Campaign'];

        ##############################################4/16/2012#####################################
        $roles = Yii::app()->authManager->getRoles(Yii::app()->user->id);
        if(isset($roles['Marketer'])){
            $cmodel = Client::model()->findByAttributes(array('user_id'=>Yii::app()->user->id));
            if ($cmodel) {
                $model->client_id = $cmodel->id;
            } else {
                $model->client_id = 0;
            }
        }
        ##############################################4/16/2012#####################################

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
		$model=Campaign::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='campaign-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
