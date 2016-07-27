<?php

class TaskRatingController extends RController
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
			//'accessControl', // perform access control for CRUD operations
            'rights', // perform access control for CRUD operations
            //'accessOwn + view,update,delete', // perform customize additional access control for CRUD operations
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
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view','create','update'),
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
		$model=new TaskRating;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['TaskRating']))
		{
			$model->attributes=$_POST['TaskRating'];
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

		if(isset($_POST['TaskRating']))
		{
			$model->attributes=$_POST['TaskRating'];
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

		$model=new TaskRating('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['TaskRating']))
			$model->attributes=$_GET['TaskRating'];

		$this->render('index',array(
			'model'=>$model,
		));
	}

	/**
	 * Get the task's note status by task ids.
     * if task's note exist, then return true for this task id, it will change the task icon
     * @return array
	 */
	public function actionIcon()
	{
        $ids = $_REQUEST['ids'];
        if (!is_array($ids)) {
            $ids = array($ids);
        }

        $rs['success'] = true;
        if (empty($ids)) {
            $rs['msg'] = "Please provide at least one task there.";
            $rs['success'] = false;
        } else {
            $criteria = new CDbCriteria();
            $criteria->addInCondition('task_id', $ids);
            $notes = TaskRating::model()->findAll($criteria);
            if ($notes) {
                foreach ($notes as $k => $v) {
                    $rs["ids"][$v->task_id] = $v->task_id;
                }
            }
        }

        echo CJSON::encode($rs);
        Yii::app()->end();
	}

    public function actionRating($task_id)
    {
        $model = new TaskRating;

        if(isset($_POST['TaskRating'])) {
            $model->attributes=$_POST['TaskRating'];
            if($model->save()) {
                $tmodel = Task::model()->findByPk($model->task_id);
                if ($tmodel) {
                    $ratings = array("1"=>"Bad","2"=>"Neutral","3"=>"Good");
                    $np = array();
                    $np["subject"] = "Content Rating For Task:#".$model->task_id;
                    $np["tos"]     = "contentrating@copypress.com";
                    /*
                    $np["content"] = "Task ID:#".$model->task_id."<br />Anchor Text:".$tmodel->anchortext.
                                     "<br />Title:".$tmodel->title."<br />Rewritten Title:".$tmodel->rewritten_title.
                                     "<br />Campaign:".$tmodel->rcampaign->name."<br />Posted URL:".$tmodel->sourceurl.
                                     "<br />Rating:".$ratings[$model->rating]."<br />Reason:".$model->notes;
                    */
                    $np["content"] = "Article Title: ".$tmodel->rewritten_title."<br /><br />Campaign Name: ".$tmodel->rcampaign->name.
                                     "<br /><br />Added By: ".$model->rcreatedby->username."<br /><br />Writer's Name: ".$model->writer_name.
                                     "<br /><br />Rating: ".$ratings[$model->rating]."<br /><br />Comment:".$model->notes;
                    Utils::notice($np);
                    //print_r($np);
                }

                $this->renderPartial('_rating', array('model'=>$model)); 
                Yii::app()->end();
            }
        } else if ($task_id > 0) {
            $model->task_id = $task_id;
        }
        $data = $model->with('rcreatedby')->findAll('task_id=' . $model->task_id);
        $model->attributes = null;
        $this->renderPartial('rating',array(
            'model'=>$model,
            'notes' => $data
        ));
        Yii::app()->end();
    }

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=TaskRating::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='task-note-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
