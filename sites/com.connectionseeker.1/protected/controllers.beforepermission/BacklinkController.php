<?php

class BacklinkController extends Controller
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
				'actions'=>array('index','view','download','dlaudit'),
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
		$model=new Backlink;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Backlink']))
		{
			$model->attributes=$_POST['Backlink'];
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

		if(isset($_POST['Backlink']))
		{
			$model->attributes=$_POST['Backlink'];
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

        if(isset($_GET['competitor'])) {
            $cptmodel = new Competitor;
            $cpti = $cptmodel->findByAttributes(array('domain' => $_GET['competitor']));
            if (!empty($cpti)) {
                $_GET['Backlink']['competitor_id'] = $cpti->id;
                //$_GET['Backlink']['competitor'] = $cpti->domain;
            }
            if(isset($_GET['datasource'])) {
                if ($_GET['datasource'] == 'historic') {
                    $_GET['Backlink']['historic_called'] = $cpti->historic_called;
                } else {
                    $_GET['Backlink']['fresh_called'] = $cpti->fresh_called;
                }
            }
        }

		$model=new Backlink('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Backlink']))
			$model->attributes=$_GET['Backlink'];

		$this->render('index',array(
			'model'=>$model,
		));
	}

	public function actionDownload()
	{
        set_time_limit(3600);
        ini_set("memory_limit", "512M");

        if(isset($_GET['competitor'])) {
            $cptmodel = new Competitor;
            $cpti = $cptmodel->findByAttributes(array('domain' => $_GET['competitor']));
            if (!empty($cpti)) {
                $_GET['Backlink']['competitor_id'] = $cpti->id;
                //$_GET['Backlink']['competitor'] = $cpti->domain;
            }
            if(isset($_GET['datasource'])) {
                if ($_GET['datasource'] == 'historic') {
                    $_GET['Backlink']['historic_called'] = $cpti->historic_called;
                } else {
                    $_GET['Backlink']['fresh_called'] = $cpti->fresh_called;
                }
            }
        } else
			throw new CHttpException(400,'Invalid request. Please provide the domain.');

        //10000, means we may get all of the backlinks.
        //Yii::app()->user->setState('pageSize',20);
		$model=new Backlink('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Backlink']))
			$model->attributes=$_GET['Backlink'];

        /*
        =COUNTIF(E2:E491,">=1")
        =COUNTIF(E2:E491,">=1")-COUNTIF(E2:E491,">5")
        =COUNTIF(E2:E491,">=5")
        =AVERAGE(E2:E494)
        =AVERAGEIF(E2:E491,">=1")
        */

        //evalcol = c, means we will calculate the C column in Excel,
        //_EXP_START_END means we will evaluate the value from C$start:C$end
        $customizedata = array(
            array('label'=> 'Count 1+', 'value'=>'=COUNTIF(_EXP_START_END,">=1")', 'evalcol'=>'C'),
            array('label'=> 'Count 1-4', 'value'=>'=COUNTIF(_EXP_START_END,">=1")-COUNTIF(_EXP_START_END,">=5")', 'evalcol'=>'C'),
            array('label'=> 'Count 5+', 'value'=>'=COUNTIF(_EXP_START_END,">=5")', 'evalcol'=>'C'),
            array('label'=> 'Avg', 'value'=>'=AVERAGE(_EXP_START_END)', 'evalcol'=>'C'),
            //array('label'=> 'Avg 1+', 'value'=>'=AVERAGEIF(_EXP_START_END,">=1")', 'evalcol'=>'C'),
            array('label'=> 'Avg 1+', 'value'=>'=SUMIF(_EXP_START_END,">=1")/COUNTIF(_EXP_START_END,">=1")', 'evalcol'=>'C'),
        );

        $this->widget('application.extensions.lkgrid.EExcelView', array(
            'id'=>'backlink-grid',
            'pageSize'=>10000,
            'filename'=>$_GET['competitor']."_audit_backlinks",
            'customizedata'=>$customizedata,
            'dataProvider'=>$model->search(),
            'columns'=>array(
                'id',
                'domain',
                'acrank',
                'url',
                'anchortext',
                'targeturl',
                array(
                    'name' => 'api_called',
                    'type' => 'raw',
                    'value' => 'date("Y-m-d", ($data->fresh_called) ? $data->fresh_called : $data->historic_called)',
                ),

            ),
        ));
	}

    public function actionDlaudit(){
        set_time_limit(3600);
        ini_set("memory_limit", "128M");

        $_domain = $_GET['domain'];
        $_domain = str_replace(" ", "", $_domain);
        if (strlen($_domain) <= 3) 
            throw new CHttpException(400,'Invalid request. Please type the correct domains into the box.');

        list($rd, $cpt) = explode("|", $_domain);
        $ds = array();
        $filename = "";
        if (isset($rd)) {
            $dms = explode(",", $rd);
            $filename .= $dms[0]."_";
        }
        /*
        if (isset($cpt)) {
            $cpts = explode(",", $cpt);
        }
        */
        $ds = explode(",", str_replace("|", ",", $_domain));

        if ($_GET['datasource']) {
            $datasource = 'historic';
        } else {
            $datasource = 'fresh';
        }

        //Autoload fix
        spl_autoload_unregister(array('YiiBase','autoload'));
        Yii::import('system.vendors.phpexcel.PHPExcel', true);
        $objPHPExcel = new PHPExcel();
        spl_autoload_register(array('YiiBase','autoload'));
        // Creating a workbook
        $objPHPExcel->getProperties()->setCreator("Connection Seeker");

        $lables = array("domain"       => "Domain",
                        "extbacklinks" => "ExtBacklinks",
                        "refdomains"   => "RefDomains",
                        "indexedurls"  => "Indexed",
                        "avghis"       => "Avg monthly backlink",
                        "toptext"      => "Top 10 achor texts",
                        "ac1"          => "Backlinks with ac rank 1+",
                        "ac1to4"       => "Backlinks with ac rank 1-4",
                        "ac5"          => "Backlinks with ac rank 5+",
                        "quality"      => "%. Of quality links",
                        "acmax"        => "Highest Backlink AC Rank",
                        "acavg"        => "Avg AC Rank of Backlink with AC Rank 1+");

        $a = 0;
        $idx = 1;
        foreach($lables as $lk => $lv) {
            $a++;
            $objPHPExcel->getActiveSheet()->setCellValue(Utils::columnName($a).$idx, $lv);
        }
        $idx++;

        if ($ds) {
            foreach($ds as $k => $v) {
                $auditmodel = new Audit;
                $ai = $auditmodel->findByAttributes(array('domain' => $v));
                $profile = array();
                if (!empty($ai)) {
                    if(!empty($ai->profile)) $profile = CJSON::decode($ai->profile, true);
                }
                $a = 0;
                if ($profile && isset($profile[$datasource])) {
                    $pf = $profile[$datasource];
                    foreach($lables as $lk => $lv) {
                        $a++;
                        if ($lk == "toptext") {
                            //write a newline character in a cell (ALT + ENTER)
                            $pf[$lk] = str_replace("<br />", "\n", $pf[$lk]);
                            $objPHPExcel->getActiveSheet()->getColumnDimension(Utils::columnName($a))->setAutoSize(true);
                            $objPHPExcel->getActiveSheet()->getStyle(Utils::columnName($a).$idx)->getAlignment()->setWrapText(true);
                        }
                        $objPHPExcel->getActiveSheet()->setCellValue(Utils::columnName($a).$idx, $pf[$lk]);
                    }
                    $idx ++;
                }
            }
        }

        //create writer for saving
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");

        $filename .= "audit_result";
        ob_end_clean();
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
        Yii::app()->end();
    }

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Backlink::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='backlink-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
