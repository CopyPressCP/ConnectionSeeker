<?php

class ContentController extends RController
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
     * Download copypress content.
     */
    public function actionDownload()
    {
        set_time_limit(3600);
        ini_set("memory_limit", "512M");
        //$this->layout = '';
        extract($_GET);

        //the path of the articles should be: ../runtime/articles/contentcampaignid-campaignname/name.html...
        //the path of the zip file should be: ../runtime/articles/name.zip 

        $reg_str = array('/\//', '/\\\/', '/\*/', '/\?/', '/\:/', '/\"/', '/\</', '/\>/', '/\|/');
        if (!in_array($format, array('text', 'html'))) $format = 'text'; //default format is text.
        $suffix = ($format == 'text') ? '.txt' : '.html';

        $filename = preg_replace( '#\s+#', '_', trim($article_info['anchortext']) );
        $filename = preg_replace( $reg_str, '_', $filename ) . "-" . $article_info['article_id'] . $suffix;


        /*
        $options['addHeaders']['Expires'] = "Mon, 26 Jul 1997 05:00:00 GMT";
        $options['addHeaders']['Cache-Control'] = "no-cache, must-revalidate";
        $options['addHeaders']['Pragma'] = "no-cache";
        */
        Yii::app()->request->xSendFile('/filedown/test.zip',array(
                'saveName'=>$filename,
                //'mimeType'=>'application/octet-stream',
                'xHeader'=>'X-Accel-Redirect',                          
                'terminate'=>false,
                //'addHeaders'=>$options,
        ));


        Yii::app()->end();
    }

    /**
     * Save copypress content as html format.
     */
    protected function _saveAsHtml(){
    }

    /**
     * Save copypress content as test format.
     */
    protected function _saveAsText(){
    }


	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id)
    {
        $model=Task::model()->findByPk($id);
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
        if(isset($_POST['ajax']) && $_POST['ajax']==='task-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}