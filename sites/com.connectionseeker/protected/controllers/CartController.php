<?php

class CartController extends RController
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
			'rights', // perform access control for CRUD operations
			'accessOwn + view,update,delete', // perform customize additional access control for CRUD operations
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
				'actions'=>array('index','view','admin','delete','create','update'),
				'users'=>array('@'),
			),
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
        $cuid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($cuid);

        if(isset($roles['Marketer'])){
            //Do some stuff first, 
            if ($_GET['id']) {
                $umodel = User::model()->findByPk($cuid);
                $model = $this->loadModel($_GET['id']);
                if ($umodel->client_id == $model->client_id) {
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
        } else {
            $filterChain->run();
        }
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
	 * add inventory domain to cart as batch.
	 * If creation is successful, the browser will be redirected to the cart 'index' page.
	 */
	public function actionAdd()
	{
		$model=new Cart;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Cart']))
		{
            $cartarr = $_POST['Cart'];
            if (empty($cartarr['inventory_ids']) || empty($cartarr['client_domain_id'])) {
                $rs[] = "Please choose one inventory domain first";
                $model->addErrors($rs);
            } else {
                $inventories = explode(",", $_POST['Cart']['inventory_ids']);
                $rs["ids"] = $inventories;
                $client_id = $_POST['Cart']['client_id'];
                $client_domain_id = $_POST['Cart']['client_domain_id'];
                $cmodel = ClientDomain::model()->findByPK($client_domain_id);
                foreach ($inventories as $ivt) {
                    $ivtmodel = Inventory::model()->findByPK($ivt);

                    $cartmodel = $model->findByAttributes(array('client_domain_id'=>$client_domain_id,
                                                   'client_id'=>$client_id, 'domain_id'=>$ivtmodel->domain_id));
                    if (!$cartmodel) {
                        $model->setIsNewRecord(true);
                        $model->id = NULL;
                        $model->domain_id = $ivtmodel->domain_id;
                        $model->domain = $ivtmodel->domain;
                        $model->client_id = $client_id;
                        $model->client_domain_id = $client_domain_id;
                        $model->client_domain = $cmodel->domain;
                        $model->save();
                    }
                }

            }

            
            /*
			$model->attributes=$_POST['Cart'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
                */
		}

        if (Yii::app()->request->isAjaxRequest) {
            $rs['msg'] = "The operation completed successfully!";
            $rs['success'] = true;
            echo CJSON::encode($rs);
            Yii::app()->end();
        } else {
            $this->redirect(array('index','client_id'=>$_POST['Cart']['client_id']));
        }
        /*
		$this->render('create',array(
			'model'=>$model,
		));
        */
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Cart;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Cart']))
		{
			$model->attributes=$_POST['Cart'];
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

		if(isset($_POST['Cart']))
		{
			$model->attributes=$_POST['Cart'];
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

		$model=new Cart('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Cart'])) {
			$model->attributes=$_GET['Cart'];
            if (!isset($_GET['Cart']['status'])) {
                $model->status = 0;
            }
        } else {
            $model->status = 0;//set the defalt, it will display the avaliable domain as default!
        }

        ##############################################5/24/2012#####################################
        $roles = Yii::app()->authManager->getRoles(Yii::app()->user->id);
        if(isset($roles['Marketer'])){
            $umodel = User::model()->findByPk(Yii::app()->user->id);
            if ($umodel) {
                $model->client_id = $umodel->client_id;

                if ($umodel->type == 0) {//owner or root!
                    //do nothing for now;
                } else {
                    $cmpids = array();
                    if ($umodel->duty_campaign_ids) {
                        $cmpids = unserialize($umodel->duty_campaign_ids);
                        $command = Yii::app()->db->createCommand();
                        $_domain_ids = $command->select('domain_id')->from('{{campaign}}')
                                               ->where("(id IN (".implode(",", $cmpids)."))")->queryAll();

                        //##$command->reset();//if you wanna reuse the $command, you have to use the reset() method
                        if ($_domain_ids) {
                            $_dids = array();
                            foreach($_domain_ids as $didv) {
                                $_dids[] = $didv['domain_id'];
                            }
                            $model->duty_domain_ids = $_dids;
                            //$onduty = array('condition'=>"client_domain_id IN (".implode(",", $_dids).")");
                        }
                    } else {
                        //$onduty = array('condition'=>"client_domain_id = -1");//return ;
                        $model->client_domain_id = -1;
                    }
                }
            } else {
                $model->client_id = 0;
            }
        }
        ##############################################5/24/2012#####################################

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
		$model=Cart::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='cart-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
