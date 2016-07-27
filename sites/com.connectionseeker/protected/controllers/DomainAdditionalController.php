<?php
define('DS', DIRECTORY_SEPARATOR);
class DomainAdditionalController extends RController
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
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id)
    {
        $model=Domain::model()->findByPk($id);
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
        if(isset($_POST['ajax']) && $_POST['ajax']==='domain-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    ##############################################################
	/**
	 * Upload file and parse it, then store the data into the database.
	 * If it is successful, the browser will be redirected to the 'index' page.
	 */
	public function actionUpload()
	{
        ini_set("memory_limit", "768M");
        set_time_limit(1200);
        ini_set("max_execution_time", "1200");

        //The following ini_set, Doesn't take affect due to it is PHP_INI_PERDIR
        ini_set("post_max_size", "128M");
        ini_set("max_input_time", "1200");
        ini_set("upload_max_filesize", "64M");

		$model=new Domain;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

        if(isset($_POST['Domain']))
        {
            //print_r($_POST['Domain']);
            //echo Yii::app()->basePath;
            $model->attributes=$_POST['Domain'];
            $model->upfile=CUploadedFile::getInstance($model,'upfile');
            $file_ext = strtolower($model->upfile->getExtensionName());
            if (!in_array($file_ext, array("csv","xls","xlsx","ods","slk","xml"))) {
                $rs[] = 'We are not support '.$file_ext.' for now.';
            }
            $fsrtn = $model->upfile->saveAs(Yii::app()->basePath.'/runtime/domain/dlist_'.$model->upfile);
            if ($fsrtn && !$rs) {
                $_POST['Domain']['filename'] = $model->upfile->getName();
                $_POST['Domain']['file_ext'] = $file_ext;

                switch ($file_ext) {
                    case 'xls':
                    case 'xlsx':
                    case 'ods':
                    case 'slk':
                    case 'xml':
                    case 'csv':
                        $rs = $this->__addBatchTasksFromFile($_POST['Domain']);
                        break;
                    case 'txt': 
                        $rs[] = "We are not support txt file any more";
                        // do nothing right now 
                        break;
                }
            }

            if ($rs === true) {
                $this->redirect(array('/domain/domainmetrics'));
            } else {
                $model->addErrors($rs);
            }
        }


		$this->render('/domain/domainmetrics',array(
			'model'=>$model,
		));
	}

    /*
    * Parse the file, and insert the data into the table.
    */
    private function __addBatchTasksFromFile($p){
        $errarr = array();//error array
        extract($p);
        $fpath = Yii::app()->basePath.'/runtime/domain/dlist_'.$filename;
        if (!file_exists($fpath)) {
            $errarr[] = "File not exists";
            return $errarr;
        }


        header( 'Content-Type: text/csv' );
        header( 'Content-Disposition: attachment;filename=domainlist.csv');
        $out = fopen('php://output', 'w');
        fputcsv($out, array('ID','Domain', 'PR', 'Moz', 'Domain Authority', 'SEM KW', 'Alexa Rank', 'Online Since'));


        $touched_statuses = array_flip(Domain::$status);
        $touched_statuses = array_change_key_case($touched_statuses, CASE_UPPER);

        //We need add transaction into here.
        $model = New Domain;

        //if the file extension is csv, then we need set the auto_detect_line_endings as true;
        ini_set("auto_detect_line_endings", 1);

        //Autoload fix
        spl_autoload_unregister(array('YiiBase','autoload'));
        Yii::import('system.vendors.phpexcel.PHPExcel', true);

        $objPHPExcel = new PHPExcel();
        //IOFactory will call the right reader class base one your file ext.
        $objPHPExcel = PHPExcel_IOFactory::load($fpath);
        spl_autoload_register(array('YiiBase','autoload'));

        Yii::import('application.vendors.SeoUtils');

        $rowIterator = $objPHPExcel->getActiveSheet()->getRowIterator();
        foreach($rowIterator as $row){
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
            //if(1 == $row->getRowIndex()) continue;//skip first row
            $rowIndex = $row->getRowIndex();
            if (1 == $rowIndex) {
                //skip first row
            } else {
                //echo $cellIterator->getCalculatedValue();
                //echo "sss";
                foreach ($cellIterator as $cell) {
                    if ($cell->getColumn() == "A") {
                        $_currd = trim($cell->getCalculatedValue());
                        $_currd = SeoUtils::getSubDomain($_currd);
                        $dmodel = $model->with("rsummary")->findByAttributes(array('domain' => $_currd));
                        if ($dmodel) {
                            fputcsv($out, array($dmodel->id, $_currd, $dmodel->googlepr, $dmodel->rsummary->mozrank, $dmodel->rsummary->mozauthority, $dmodel->rsummary->semrushkeywords , $dmodel->rsummary->alexarank, ($dmodel->rsummary->onlinesince)>658454400 ? date("Y-m-d", $dmodel->rsummary->onlinesince) : "" ));
                        } else {
                            fputcsv($out, array('',$_currd, '', '', '', '', '', ''));
                        }
                    }
                    //echo $cell->getColumn(). " __ " . $cell->getCalculatedValue();
                    //$arr[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
                }
            }
        }

        fclose($out);

        exit;
    }
    ##############################################################
}
