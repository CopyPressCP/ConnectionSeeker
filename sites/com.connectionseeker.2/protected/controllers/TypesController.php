<?php

class TypesController extends RController
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
			//'accessOwn + view,update,delete', // perform customize additional access control for CRUD operations
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
		$model=new Types;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Types']))
		{
			$model->attributes=$_POST['Types'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

        $maxref = self::actionMaxref('site', false);
        $model->refid = $maxref['maxrefid'];

		$this->render('create',array(
			'model'=>$model,
		));
	}

    public function actionMaxref($type = 'site', $endapp = true)
    {
        $rs = array();

        $rs = Yii::app()->db->createCommand()->select("MAX(refid) AS maxrefid")->from('{{types}}')
            ->where("type=:type", array(':type'=>$type,))
            ->queryRow();

        if ($rs['maxrefid']) {
            $rs['maxrefid'] += 1;
        } else {
            $rs['maxrefid'] = 1;
        }

        if ($endapp) {
            echo CJSON::encode($rs);
            Yii::app()->end();
        } else {
            return $rs;
        }
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

		if(isset($_POST['Types']))
		{
			$model->attributes=$_POST['Types'];
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
            $model=$this->loadModel($id);
            $model->status = 0;

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if($model->save() && !isset($_GET['ajax']))
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

		$model=new Types('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Types']))
			$model->attributes=$_GET['Types'];

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
		$model=Types::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='types-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}



	/**
	 * Set the copypress perference.
	 * 
	 */
    public function actionPreference() {
        $preffile = dirname(dirname(__FILE__))."/config/preferences.xml";
        $prefs = @simplexml_load_file($preffile, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (!$prefs) {
            throw new CHttpException(400,'Preferences File Is Invalid, Please contact system admin.');
        }

        /*
        $articletype = $prefs->xpath('//inventories/articletype');
        $styleguide = $prefs->xpath('//inventories/styleguide');
        $articletype = $ivts[0]->articletype;
        $styleguide = $ivts[0]->styleguide;
        */

        $ivts = $prefs->xpath('//inventories');

        $hits = "";
		if (empty($_POST['articletype'])) {
            $articletype = (string)$ivts[0]->articletype;
            $styleguide = (string)$ivts[0]->styleguide;
		} else {
            $sg = str_replace(array("¡°", "¡±", "¡®", "¡¯", " "), array('"', '"', "'", "'", " "), $_POST['styleguide']);
            //$sg = preg_replace('/&((#(\d{3,5}|x[a-fA-F0-9]{4})|[a-zA-Z][a-z0-9]{2,5});)/', '&\\1', $sg);
            //var_dump($articletype);
            $ivts[0]->articletype = $_POST['articletype'];
            $ivts[0]->styleguide = htmlspecialchars($sg);

            $fh = fopen($preffile, "wb");
            if($fh == false) die("Unable to create file {$preffile}");
            fputs($fh, pack("CCC", 0xef, 0xbb, 0xbf));//$newxmlcfg = "\xEF\xBB\xBF" . $newxmlcfg;
            fputs($fh, $prefs->asXML());
            fclose($fh);

            //there are 2 ways to create utf8 file. here is another way.
            /*
            this is another way.
            $newxmlcfg = utf8_encode($str->asXML());
            $newxmlcfg = "\xEF\xBB\xBF" . $newxmlcfg;
            fputs($fh, $newxmlcfg);
            */

            $hits = "Preferences Was Changed, Please Make Sure.";

            $articletype = $_POST['articletype'];
            $styleguide = $sg;
        }

        $this->render('preference',array(
            'hits'=>$hits,
            'articletype'=>$articletype,
            'styleguide'=>$styleguide,
        ));
    }
}
