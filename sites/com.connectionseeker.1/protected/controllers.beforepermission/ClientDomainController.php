<?php

class ClientDomainController extends Controller
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
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view', 'competitors', 'domains'),
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
		$model=new ClientDomain;
		$kymodel=new ClientDomainKeyword;
		$cptmodel=new Competitor;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

        /*
		if(isset($_POST['ClientDomain']))
		{
			$model->attributes=$_POST['ClientDomain'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}
        */

        //echo $model->processConditions(array('and', 'type=1', array('or', 'id=1', 'id=2')));

		if(isset($_POST['ClientDomain'], $_POST['ClientDomainKeyword']))
		{
			$model->attributes=$_POST['ClientDomain'];
            if (isset($_POST['Competitor']) && !empty($_POST['Competitor']['domain'])) {

                $cpts = array();
                foreach ($_POST['Competitor']['domain'] as $k => $v) {
                    if (!empty($v)) {
                        $ci = $cptmodel->findByAttributes(array('domain' => $v));
                        if (!empty($ci)) {
                            $cpts[] = $ci->id;
                        } else {
                            $cptmodel->setIsNewRecord(true);
                            $cptmodel->id=NULL;
                            $cptmodel->domain=$v;
                            if ($cptmodel->save()) {
                                $cpts[] = $cptmodel->id;
                            } else {
                                $redirect = false;
                                //Yii::app()->user->setFlash('commentSubmitted','Thank you...');
                                //$this->refresh();
                            }
                        }
                    }
                }

                /*
                * Use CAdvancedArBehavior To Update the X-ref table {{client_domain_competitor}}(domain_id,competitor_id)
                * the behavior path is "application.components.CAdvancedArBehavior", you can put anywhere you like,
                * but before you call this behavior, you need import it
                * $model->rcompetitor = Competitor::model()->findAll();
                * Principle: delete their relationships first, then insert the new relationships into the X-ref table
                */
                if (!empty($cpts)) $model->rcompetitor = $cpts;
            }


            $redirect = true;
			$model->attributes=$_POST['ClientDomain'];
			if($model->save()) {
                if (!empty($_POST['ClientDomainKeyword']['keyword'])) {
                    foreach ($_POST['ClientDomainKeyword']['keyword'] as $k => $v) {
                        if (!empty($v)) {
                            $kymodel->id=NULL;
                            $kymodel->client_id=$model->client_id;
                            $kymodel->domain_id=$model->id;
                            $kymodel->keyword=$v;
                            if (!$kymodel->save()) {
                                $redirect = false;
                                throw new CHttpException(400,"Create Keyword: ({$v}) failure. Please try it again.");
                            }
                            $kymodel->setIsNewRecord(true);
                        }
                    }
                }
            } else {
                $redirect = false;
                throw new CHttpException(400,'Create domain failure. Please try it again.');
            }

            if ($redirect) {
                $this->redirect(array('view','id'=>$model->id));
            }
		}

		$this->render('create',array(
			'model'=>$model,
			'kymodel'=>$kymodel,
			'cptmodel'=>$cptmodel,
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
		$kymodel=new ClientDomainKeyword;
		$cptmodel=new Competitor;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['ClientDomain'], $_POST['ClientDomainKeyword']))
		{
			$model->attributes=$_POST['ClientDomain'];
            if (isset($_POST['Competitor']) && !empty($_POST['Competitor']['domain'])) {
                $cpts = array();
                $keepcpts = array();
                foreach ($_POST['Competitor']['domain'] as $k => $v) {
                    if (!empty($v)) {
                        /*
                        $ci = Competitor::model()->find(array('select'=>'id',
                                         'condition'=>'domain=:domain',
                                         'params'=>array(':domain'=>'test.com'),
                              ));
                        */
                        $ci = $cptmodel->findByAttributes(array('domain' => $v));
                        if (!empty($ci)) {
                            if (stripos($k, 'dk_') === false) {
                                $cpts[] = $ci->id;
                            } else {
                                $competitorid = str_ireplace("dk_", "", $k);
                                //didn't change the competitor domain in the "add client domain" page. then no need delete.
                                //this will avoid the X-ref.table.last_call_api_time was emptied by the behavior.
                                if ($competitorid == $ci->id) {
                                    $keepcpts[] = $competitorid;
                                } else {
                                    $cpts[] = $ci->id;
                                }
                            }
                        } else {
                            $cptmodel->setIsNewRecord(true);
                            $cptmodel->id=NULL;
                            $cptmodel->domain=$v;
                            if ($cptmodel->save()) {
                                $cpts[] = $cptmodel->id;
                            } else {
                                $redirect = false;
                                //Yii::app()->user->setFlash('commentSubmitted','Thank you...');
                                //$this->refresh();
                            }
                        }

                    }/* else {
                        if (stripos($k, 'dk_') === false) {
                            //do nothing for now
                        } else {
                            //remove the relationship between this domain and the competitor
                            //do nothing for now
                        }
                    }*/

                }

                /*
                * Use CAdvancedArBehavior To Update the X-ref table {{client_domain_competitor}}(domain_id,competitor_id)
                * the behavior path is "application.components.CAdvancedArBehavior", you can put anywhere you like,
                * but before you call this behavior, you need import it
                * $model->rcompetitor = Competitor::model()->findAll();
                * Principle: delete their relationships first, then insert the new relationships into the X-ref table
                */
                if (!empty($cpts)) $model->rcompetitor = $cpts;

                //Please reference the CAdvancedArbehavior
                if (!empty($keepcpts)) 
                    $model->deleteConditions = array('not in', 'competitor_id', $keepcpts);
            }

            //$kymodel->client_id=$model->client_id;
            $redirect = true;

			if($model->save()) {
                if (!empty($_POST['ClientDomainKeyword']['keyword'])) {
                    foreach ($_POST['ClientDomainKeyword']['keyword'] as $k => $v) {
                        if (stripos($k, 'ck_') === false) {
                            if (!empty($v)) {
                                $kymodel->setIsNewRecord(true);
                                $kymodel->id=NULL;
                                $kymodel->client_id=$model->client_id;
                                $kymodel->domain_id = $model->id;
                                $kymodel->keyword=$v;
                                if (!$kymodel->save()) {
                                    $redirect = false;
                                    throw new CHttpException(400,"Create Keyword: ({$v}) failure. Please try it again.");
                                }
                            }
                        } else {
                            $domainid = str_ireplace("ck_", "", $k);
                            $_kymodel = $kymodel->findByPk($domainid);
                            $_kymodel->client_id = $model->client_id;
                            $_kymodel->domain_id = $model->id;
                            $_kymodel->keyword = $v;
                            if (empty($v)) $_kymodel->status = 0;
                            if(!$_kymodel->save()) {
                                $redirect = false;
                                throw new CHttpException(400,'Create domain failure. Please try it again.');
                            }
                        }
                    }

                }

            } else {
                $redirect = false;
                throw new CHttpException(400,'Create domain failure. Please try it again.');
            }

            if ($redirect) {
                $this->redirect(array('view','id'=>$model->id));
            }

		}

		$this->render('update',array(
			'model'=>$model,
			'kymodel'=>$kymodel,
			'cptmodel'=>$cptmodel,
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

		$model=new ClientDomain('search');
		$model->unsetAttributes();  // clear any default values
        //set view active records default
        if(!isset($_GET['ClientDomain']['status'])) $_GET['ClientDomain']['status'] = 1;
		if(isset($_GET['ClientDomain']))
			$model->attributes=$_GET['ClientDomain'];

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
		$model=ClientDomain::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

    //for dropdown list
    public function actionCompetitors()
    {
        //print_r($_POST);
        $data = ClientDomainCompetitor::model()->findAll('domain_id=:domain_id',
                      array(':domain_id'=>(int) $_POST['domain_id']));
    
        $data = array('' => '-- Competitors --') + CHtml::listData($data,'competitor_id','rcompetitor.domain');
        $rs = array();
        $competitor = "";
        foreach($data as $value=>$name)
        {
             $competitor .= CHtml::tag('option',
                       array('value'=>$value),CHtml::encode($name),true);
        }
        
        if (empty($_POST['haskeyword'])) {
            echo $competitor;
        } else {
            $data = ClientDomainKeyword::model()->findAll('domain_id=:domain_id',
                          array(':domain_id'=>(int) $_POST['domain_id']));
        
            $data = array('' => '-- Keywords --') + CHtml::listData($data,'keyword','keyword');
            $keyword = "";
            foreach($data as $value=>$name)
            {
                 $keyword .= CHtml::tag('option',
                           array('value'=>$value),CHtml::encode($name),true);
            }

            $rs['keyword'] = $keyword;
            $rs['competitor'] = $competitor;
            echo CJSON::encode($rs);
        }
    }

    //for client domains dropdown list
    public function actionDomains($client_id = 0)
    {
        //fb($_GET);
        if (!$client_id) {
            return ;
        }
        $term = trim($_GET['term']);
        if (!empty($term)) {
            $data = ClientDomain::model()->findAll('client_id=:client_id AND domain LIKE :qterm',
                          array(':client_id'=>(int)$client_id, ':qterm'=> '%'.$term.'%'));
        } else {
            $data = ClientDomain::model()->findAll('client_id=:client_id',
                          array(':client_id'=>(int)$client_id));
        }

        $label = isset($_GET['label']) ? trim($_GET['label']) : "domain";
        $value = isset($_GET['value']) ? trim($_GET['value']) : "domain";
        $id = isset($_GET['id']) ? trim($_GET['id']) : "domain";

        $rs = array();
        if (!empty($data)) {
            foreach ($data as $p) {
               $rs[] = array(
                   // expression to give the string for the autoComplete drop-down
                   //'label' => $p->$label,
                   'label' => "{$p->rclient->name}: ".$p->$label,
                   'value' => $p->$value,
                   'id' => $p->$id, // return value from autocomplete
               );
            }
        }

        //$data = CHtml::listData($data, 'domain', 'domain');
        echo CJSON::encode($rs);
        Yii::app()->end();
    }

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='client-domain-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
