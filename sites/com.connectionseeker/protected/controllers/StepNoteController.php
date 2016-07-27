<?php

class StepNoteController extends Controller
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
				'actions'=>array('index','view','create','update','icon','note','bulknote','alltypenotes'),
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
		$model=new StepNote;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['StepNote']))
		{
			$model->attributes=$_POST['StepNote'];
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

		if(isset($_POST['StepNote']))
		{
			$model->attributes=$_POST['StepNote'];
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
	 * Get the domain's note status by domain ids.
     * if domain's note exist, then return true for this domain id, it will change the domain icon
     * @return array
	 */
	public function actionIcon()
	{
        $ids = $_REQUEST['ids'];
        if (!is_array($ids)) {
            $ids = array($ids);
        }

        if (isset($_REQUEST['type'])) $type = $_REQUEST['type'];
        if (!$type) $type = 1;

        $rs['success'] = true;
        if (empty($ids)) {
            $rs['msg'] = "Please provide at least one content task there.";
            $rs['success'] = false;
        } else {
            $criteria = new CDbCriteria();
            $criteria->addCondition("type=:type");
            $criteria->params[':type'] = $type;
            $criteria->addInCondition('task_id', $ids);
            $notes = StepNote::model()->findAll($criteria);
            if ($notes) {
                foreach ($notes as $k => $v) {
                    $rs["ids"][$v->task_id] = $v->task_id;
                }
            }
        }

        echo CJSON::encode($rs);
        Yii::app()->end();
	}

    public function actionNote($task_id=0, $type=1)
    {
        $model = new StepNote;

        if(isset($_POST['StepNote']))
        {
            $model->attributes=$_POST['StepNote'];
            if($model->save()) {
                $this->renderPartial('_note', array('model'=>$model)); 
                Yii::app()->end();
            }
        } else if ($task_id > 0) {
            $model->task_id = $task_id;
            $model->type = $type;
        }

        //dispath to different view.
        $cuid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($cuid);

        $data = $model->with('rcreatedby')->findAll('(t.task_id=' . $model->task_id.') AND t.type=' . $model->type);
        $model->attributes = null;
        $this->renderPartial('note',array(
            'model'=>$model,
            'notes' => $data
        ));
        Yii::app()->end();
    }

    public function actionAlltypenotes($task_id=0, $type='all')
    {
        $model = new StepNote;

        $model->task_id = $task_id;
        $data = array();
        if (is_int($type) && $type > 0) {
            $model->type = $type;
            $data["type".$type] = $model->with('rcreatedby')->findAll('(t.task_id=' . $model->task_id.') AND t.type=' . $model->type);
        } else {
            for($i=1; $i<=3; $i++) {
                $model->type = $i;
                $data["type".$i] = $model->with('rcreatedby')->findAll('(t.task_id=' . $model->task_id.') AND t.type=' . $model->type);
            }
        }

        $this->renderPartial('_alltypenote',array(
            'notes' => $data
        ));
        Yii::app()->end();
    }

    public function actionBulknote()
    {
        $ids = $_REQUEST['ids'];
        if (!is_array($ids)) {
            $ids = array($ids);
        }

        if (empty($ids)) {
            $rs['msg'] = "Please provide at least one link building task";
            $rs['success'] = false;
            echo CJSON::encode($rs);
            Yii::app()->end();
        }

        $model = new StepNote;
        $note = $_REQUEST['note'];
        $type = $_REQUEST['type'];
        if (empty($note)) {
            $rs['msg'] = "Please provide the note";
            $rs['success'] = false;
            echo CJSON::encode($rs);
            Yii::app()->end();
        }
        if (empty($type)) $type = 2;

        $i = 0;
        foreach($ids as $id) {
            $model->setIsNewRecord(true);
            $model->id      = NULL;
            $model->notes   = $note;
            $model->type    = $type;
            $model->task_id = $id;
            $model->save();
            $i++;
        }

        $rs['msg'] = "Bulk Notes Were Created Successfully.";
        $rs['success'] = true;
        echo CJSON::encode($rs);
        Yii::app()->end();
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

		$model=new StepNote('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['StepNote']))
			$model->attributes=$_GET['StepNote'];

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
		$model=StepNote::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='step-note-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
