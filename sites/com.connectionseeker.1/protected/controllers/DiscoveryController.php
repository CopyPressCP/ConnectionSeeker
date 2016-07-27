<?php

class DiscoveryController extends RController
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
		$model=new Discovery;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Discovery']))
		{
			$model->attributes=$_POST['Discovery'];
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

		if(isset($_POST['Discovery']))
		{
			$model->attributes=$_POST['Discovery'];
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
			//$this->loadModel($id)->delete();

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

        if (isset($_GET['client_id']) && $_GET['client_id']) {
            $_firstone = ClientDomain::model()->find("client_id=:client_id AND status=1",
                                                  array(':client_id'=>$_GET['client_id']));
            if ($_firstone) {
                $_GET['client_domain_id'] = $_firstone->id;
            }
        }

        if ( (empty($_GET['client_domain_id']) && empty($_GET['competitor_id']))
          || (!empty($_GET['client_domain_id']) && !isset($_GET['competitor_id'])) ) {
            if (!empty($_GET['client_domain_id'])) {
                $w = array("AND", "domain_id=:cdcid",
                                  array("OR", "cdc.fresh_called > 0", "cdc.historic_called > 0"));
            } else {
                $w = array("OR", "cdc.fresh_called > 0", "cdc.historic_called > 0");
            }
            //we can put the lasted one competitor as the default search parameters when we didn't;
            $lastcpt = Yii::app()->db->createCommand()
                ->select("cdc.domain_id, cdc.competitor_id, cdc.fresh_called, cdc.historic_called")
                ->from('{{client_domain_competitor}} cdc')
                ->join('{{client_domain}} cd', '(cd.id=cdc.domain_id AND cd.status=1)')
                ->join('{{client}} c', '(c.id=cd.client_id AND c.status=1)')
                ->where($w, array(':cdcid'=>$_GET['client_domain_id']))->order('cdc.id DESC')->limit(1)
                ->queryRow();
            if ($lastcpt) {
                $cmpids = $lastcpt['competitor_id'];
                if ($lastcpt['fresh_called'] > 0) {
                    $_GET['fresh_called'] = $lastcpt['fresh_called'];
                } else {
                    $_GET['historic_called'] = $lastcpt['historic_called'];
                }
                $_GET['client_domain_id'] = $lastcpt['domain_id'];
                $_GET['competitor_id'] = $lastcpt['competitor_id'];
            }
        }

		$model=new Discovery('search');
		$model->unsetAttributes();  // clear any default values
        /*
		if(isset($_GET['Discovery']))
			$model->attributes=$_GET['Discovery'];
        */
		$model->attributes=$_GET;

        $types = Types::model()->actived()->findAll();
        $gtps = CHtml::listData($types, 'refid', 'typename', 'type');

        $stypes = $gtps['site'];
        $otypes = $gtps['outreach'];
        $stypestr = Utils::array2String(array("" => '[Site Type]') + $stypes);
        $otypestr = Utils::array2String(array("" => '[Outreach Type]') + $otypes);


		$this->render('index',array(
			'model'=>$model,
			'stypes'=>$stypes,
			'otypes'=>$otypes,
			'stypestr'=>$stypestr,
			'otypestr'=>$otypestr,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionBacklink($id)
	{
        $model=$this->loadModel($id);

        if (isset($_GET['client_domain_id']) && $_GET['client_domain_id'] > 0) {
            $cdmodel = ClientDomain::model()->findByPk($_GET['client_domain_id']);
            if ($cdmodel->use_historic_index) {
                $call = $model->historic_called;
                $where = "((competitor_id=:cid) AND (domain_id=:did) AND (historic_called=:call))";
            } else {
                $call = $model->fresh_called;
                $where = "((competitor_id=:cid) AND (domain_id=:did) AND (fresh_called=:call))";
            }
        } else {
            if ($model->fresh_called > $model->historic_called) {
                // the newer is fresh
                $call = $model->fresh_called;
                $where = "((competitor_id=:cid) AND (domain_id=:did) AND (fresh_called=:call))";
            } else {
                //call historic
                $call = $model->historic_called;
                $where = "((competitor_id=:cid) AND (domain_id=:did) AND (historic_called=:call))";
            }
        }

        $rs = array();
        if ($model->domain_id) {
            $rs = Yii::app()->db->createCommand()->select()->from('{{competitor_backlink}}')
                ->where($where, array(':cid'=>$model->competitor_id,
                                      ':did'=>$model->domain_id,
                                      ':call'=>$call,))
                ->queryAll();
        }


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
		$model=Discovery::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='discovery-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

    public function actionTouch($id)
    {

        if(Yii::app()->request->isPostRequest)
        {
            // we only allow deletion via POST request
            //$this->loadModel($id)->delete();
            $domain = Domain::model()->findbyPk($id);
            $domain->created = date("Y-m-d H:i:s");
            $domain->created_by = Yii::app()->user->id;
            $domain->touched_status = 1;
            $domain->save();
            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if(!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
        }
        else
            throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
    }
}
