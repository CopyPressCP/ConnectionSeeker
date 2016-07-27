<?php

class BloggerProgramController extends RController
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
			//'accessOwn + view,update,delete,processing', // perform customize additional access control for CRUD operations
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
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','index','view'),
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
		$model=new BloggerProgram;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['BloggerProgram']))
		{
			$model->attributes=$_POST['BloggerProgram'];
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

		if(isset($_POST['BloggerProgram']))
		{
			$model->attributes=$_POST['BloggerProgram'];
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
			//##$this->loadModel($id)->delete();

            $model=$this->loadModel($id);
            $model->isdelete = 1;
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

		$model=new BloggerProgram('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['BloggerProgram']))
			$model->attributes=$_GET['BloggerProgram'];

		$this->render('index',array(
			'model'=>$model,
		));
	}


    /**
     * Updates a particular model.
     * AJAX Updately the attributes
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionSetattr($id)
    {
        $model=$this->loadModel($id);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        $rs = array('success' => true, 'msg' => Yii::t('BloggerProgram', 'Updated was Successful.'));
        if(isset($_GET['attrname']) && isset($_GET['attrvalue']))
        {
            $attrname = str_replace("[]", "", $_GET['attrname']);
            if (stripos($_GET['attrname'], "category") !== false || stripos($_GET['attrname'], "activeprogram") !== false) {
                $attrvalue = explode(",", $_GET['attrvalue']);
                $model->$attrname = $attrvalue;
            } else {
                $model->$attrname = $_GET['attrvalue'];
            }

            if($model->save()) {
                //do nothing;
            } else {
                $rs['success'] = false;
                $rs['msg'] = Yii::t('BloggerProgram', 'Updated was Failure.');
            }
        }

        echo CJSON::encode($rs);

        Yii::app()->end();
    }

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

		$model=new BloggerProgram;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

        if(isset($_POST['BloggerProgram']))
        {
            //print_r($_POST['BloggerProgram']);
            //echo Yii::app()->basePath;
            $model->attributes=$_POST['BloggerProgram'];
            $model->upfile=CUploadedFile::getInstance($model,'upfile');
            $file_ext = strtolower($model->upfile->getExtensionName());
            if (!in_array($file_ext, array("csv","xls","xlsx","ods","slk","xml"))) {
                $rs[] = 'We are not support '.$file_ext.' for now.';
            }
            $fsrtn = $model->upfile->saveAs(Yii::app()->basePath.'/runtime/bpdomain/'.$model->upfile);
            if ($fsrtn && !$rs) {
                $_POST['BloggerProgram']['filename'] = $model->upfile->getName();
                $_POST['BloggerProgram']['file_ext'] = $file_ext;

                switch ($file_ext) {
                    case 'xls':
                    case 'xlsx':
                    case 'ods':
                    case 'slk':
                    case 'xml':
                    case 'csv':
                        $rs = $this->__addBatchFromFile($_POST['BloggerProgram']);
                        break;
                    case 'txt': 
                        $rs[] = "We are not support txt file any more";
                        // do nothing right now 
                        break;
                }
            }

            if ($rs === true) {
                $this->redirect(array('index'));
            } else {
                $model->addErrors($rs);
            }
        }


		$this->render('upload',array(
			'model'=>$model,
		));
	}

    public function actionNote($blogger_program_id)
    {
        $model = new BloggerProgramNote;

        if(isset($_POST['BloggerProgramNote']))
        {
            $bpmdl = BloggerProgram::model()->findByPk($blogger_program_id);
            $model->attributes=$_POST['BloggerProgramNote'];
            $model->domain_id = $bpmdl->domain_id;
            if($model->save()) {
                // $this->redirect(array('view','id'=>$model->id));
                /*$this->render('note',array(
                    'model'=>$model));*/
                $this->renderPartial('_note', array('model'=>$model)); 
                Yii::app()->end();
            }
            $blogger_program_id = $_POST['BloggerProgramNote']['blogger_program_id'];
        } else if ($blogger_program_id > 0) {
            $model->blogger_program_id = $blogger_program_id;
        }
        $data = $model->with('rcreatedby')->findAll('blogger_program_id=' . $model->blogger_program_id);
        $model->attributes = null;
        $this->renderPartial('note',array(
            'model'=>$model,
            'notes' => $data
        ));
        Yii::app()->end();
    }

    public function actionPrice($blogger_program_id)
    {
        $model = new BloggerProgramPrice;

        if(isset($_POST['BloggerProgramPrice']))
        {
            $bpmdl = BloggerProgram::model()->findByPk($blogger_program_id);
            $model->attributes=$_POST['BloggerProgramPrice'];
            $model->domain_id = $bpmdl->domain_id;
            $model->domain = $bpmdl->domain;
            if($model->save()) {
                // $this->redirect(array('view','id'=>$model->id));
                /*$this->render('price',array(
                    'model'=>$model));*/
                $this->renderPartial('_price', array('model'=>$model)); 
                Yii::app()->end();
            }
            $blogger_program_id = $_POST['BloggerProgramPrice']['blogger_program_id'];
        } else if ($blogger_program_id > 0) {
            $model->blogger_program_id = $blogger_program_id;
        }
        $data = $model->with('rcreatedby')->findAll('blogger_program_id=' . $model->blogger_program_id);
        $model->attributes = null;
        $this->renderPartial('price',array(
            'model'=>$model,
            'prices' => $data
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
		$model=BloggerProgram::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='blogger-program-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}


    /*
    * Parse the file, and insert the data into the table.
    */
    private function __addBatchFromFile($p){
        $errarr = array();//error array
        extract($p);
        $fpath = Yii::app()->basePath.'/runtime/bpdomain/'.$filename;
        if (!file_exists($fpath)) {
            $errarr[] = "File not exists";
            return $errarr;
        }

        $types = Types::model()->actived()->bytype(array("bloggerprogram","activeprogram"))->findAll();
        //create an array, typename as the key, and the refid as the value,so that you no need array_flip it again
        $gtps = CHtml::listData($types, 'typename', 'refid', 'type');
        $categories = $gtps['bloggerprogram'] ? $gtps['bloggerprogram'] : array();
        $activeprogrames = $gtps['activeprogram'] ? $gtps['activeprogram'] : array();
        //$cms_usernames = $gtps['cms_username'] ? $gtps['cms_username'] : array();

        $bpstatuses = array_flip(BloggerProgram::$bpstatuses);
        $bpstatuses = array_change_key_case($bpstatuses, CASE_UPPER);

        $syndicationes = array("1"=>"Yes","0"=>"No");
        $syndicationes = array_flip($syndicationes);
        $syndicationes = array_change_key_case($syndicationes, CASE_UPPER);

        //echo "<pre>";
        //for case insensitive
        foreach (array("bpstatuses", "categories","activeprogrames","syndicationes") as $_sv) {
            if ($$_sv) {
                $_s = array_keys($$_sv);
                $_r = "_r_{$_sv}";
                $$_r = array_combine(array_map('strtolower', $_s), $_s);
                //print_r($$_r);
            }
        }

        //We need add transaction into here.
        $domodel = New Domain;
        $model = New BloggerProgram;

        //if the file extension is csv, then we need set the auto_detect_line_endings as true;
        ini_set("auto_detect_line_endings", 1);

        //Autoload fix
        spl_autoload_unregister(array('YiiBase','autoload'));
        Yii::import('system.vendors.phpexcel.PHPExcel', true);
        //$objReader = PHPExcel_IOFactory::createReader('Excel2007');
        //$objPHPExcel = $objReader->load($fpath);
        /*
        If you wanna your code support xlsx(Excel 2007), you have to enable zlib extension,
        so that means you should change the setting of the php.ini file:
        extension=php_zip.dll
        zlib.output_compression = On
        */

        $objPHPExcel = new PHPExcel();
        //IOFactory will call the right reader class base one your file ext.
        $objPHPExcel = PHPExcel_IOFactory::load($fpath);
        spl_autoload_register(array('YiiBase','autoload'));

        Yii::import('application.vendors.SeoUtils');

        $rowIterator = $objPHPExcel->getActiveSheet()->getRowIterator();

        $header = array("Domain"=>"domain","Per Word Rate"=>"per_word_rate", "Contact First Name"=>"first_name",
            "Contact Email"=>"contact_email","Contact Last Name"=>"last_name", "Syndication"=>"syndication",
            "Category"=>"category_str","Domain Auth"=>"mozauthority","DA"=>"mozauthority","Status"=>"status",
            "Active Program"=>"activeprogram_str","CMS Username"=>"cms_username","CMS User ID"=>"cms_user_id");

        $arr = array();
        $nmp = array();//the excel Column Index mapping to $header array
        $nos = 0; // number of success
        $nof = 0; // number of failure;
        foreach($rowIterator as $row){
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
            //if(1 == $row->getRowIndex()) continue;//skip first row
            $rowIndex = $row->getRowIndex();
            if (1 == $rowIndex) {
                foreach ($cellIterator as $cell) {
                    //Header mapping for excel Column Index. for example: Domain=>A, Category=>B
                    //$nmp[$cell->getColumn()] = $cell->getCalculatedValue();
                    $nmp[trim(strtolower($cell->getCalculatedValue()))] = $cell->getColumn();
                }
            } else {
                foreach ($cellIterator as $cell) {
                    $arr[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
                }
                //save data into database.

                //save data into tbl.domain
				$price = 0;
                $p = array();
                $pdm = array();
                foreach ($header as $hk => $hv) {
                    $hk = strtolower($hk);
                    if (!isset($nmp[$hk])) {
                        continue;
                    }
                    $pv = trim($arr[$rowIndex][$nmp[$hk]]);
                    if ($hv == 'domain' && empty($pv)) {
                        break;
                    }

                    if ($hv == 'domain') {
                        $pdm['rootdomain'] = SeoUtils::getDomain($pv);
                        $pv = SeoUtils::getSubDomain($pv);
                        if ($pv) {
                            $pdm['tld'] = substr(strrchr($pv, "."), 1);
                        }
                    } elseif ($hv == 'category_str') {
                        $p['category'] = array();
                        if ($pv) {
                            $_tmps = array();
                            $_tmps = explode(",", $pv);
                            $c = array();
                            foreach ($_tmps as $v) {
                                $v = trim($v);
                                $v = strtolower($v);
                                $v = $_r_categories[$v];
                                //if (!is_numeric($categories[$v])) continue;
                                $c[$v] = $categories[$v];
                            }

                            //reset the value, if it was category.
                            $p['category'] = $c;
                        }
                    } elseif ($hv == 'activeprogram_str') {
                        $p['activeprogram'] = array();
                        if ($pv) {
                            $_tmps = array();
                            $_tmps = explode(",", $pv);
                            $c = array();
                            foreach ($_tmps as $v) {
                                $v = trim($v);
                                $v = strtolower($v);
                                $v = $_r_activeprogrames[$v];
                                //if (!is_numeric($activeprogrames[$v])) continue;
                                $c[$v] = $activeprogrames[$v];
                            }
                            //reset the value
                            $p['activeprogram'] = $c;
                        }
                    } elseif ($hv == 'status') {
                        $pv = strtolower($pv);
                        $pv = $_r_bpstatuses[$pv];
                        $pv = $bpstatuses[$pv];
                        $p['status'] = $pv;
                    } elseif ($hv == 'syndication') {
                        if (is_numeric($pv) && in_array($pv, array(0,1))) {
                        } else {
                            $pv = strtolower($pv);
                            $pv = $_r_syndicationes[$pv];
                            $pv = $syndicationes[$pv];
                            //echo $p['syndication'] = $pv;
                        }
                    }

                    if (empty($pv) && ($hv != 'status') && ($hv != 'syndication')) $pv = "";
                    if (strtoupper($pv) == "NULL" 
                        && in_array($hv, array("first_name","last_name","contact_email","per_word_rate","mozauthority","cms_username","cms_user_id"))) {
                        $pv = "";
                        if ($hv == "per_word_rate" || $hv == "mozauthority" || $hv == "cms_user_id") $pv = 0;
                    }

                    $p[$hv] = $pv;
                }
                //print_r($p);
                //exit;

                if (empty($p['category']) && !empty($category)) $p['category'] = $category;//see extract above;
                if (empty($p['activeprogram']) && !empty($activeprogram)) $p['activeprogram'] = $activeprogram;
                if (empty($p['status']) && !empty($status)) $p['status'] = $status;
                if (!is_numeric($p['syndication'])) {
                    if (empty($p['syndication']) && !empty($syndication)) $p['syndication'] = $syndication;
                    if (empty($p['syndication'])) $p['syndication'] = NULL;
                }

                $pdm["status"] = 1;//set the domain active.
                $pdm['touched_status'] = 1;
                if ($pdm['rootdomain'] != $p['domain']) {
                    $_rdmi = $domodel->findByAttributes(array('domain' => $pdm['rootdomain']));
                    if (!$_rdmi) {
                        $_rdmi = $domodel;
                        $_rdmi->setIsNewRecord(true);
                        $_rdmi->id=NULL;
                        $_rdmi->attributes=$pdm;
                        //Comment out the following line @2015/12/18
                        //$_rdmi->touched_status = 1;//##!!!##11/5/2013
                        //Set the outreach type as "Blogger Program"
                        $_rdmi->otype = 6;
                        $_rdmi->save();
                    }
                    unset($_rdmi->attributes);
                }

                $p["isdelete"] = 0;

                //Be able to upload the same domain with different contact name (contact first name + contact last name)
                if (!empty($p["first_name"]) || !empty($p["last_name"])) {
                    $_pcon = array();
                    $_pcon['domain'] = $p['domain'];
                    if (!empty($p["first_name"])) $_pcon['first_name'] = $p['first_name'];
                    if (!empty($p["last_name"])) $_pcon['last_name'] = $p['last_name'];
                    $_bpmdl = $model->findByAttributes($_pcon);
                } else {
                    $_bpmdl = $model->findByAttributes(array('domain' => $p['domain']));
                }
                if ($_bpmdl) {
                    if ($action == 1) {
                        if ($_bpmdl->isdelete == 1) {
                            $errarr[$rowIndex] .= "Row #{$rowIndex} ".$p['domain'].": Domain re-activated.";
                            $_bpmdl->setIsNewRecord(false);
                            $_bpmdl->setScenario('update');
                            $_bpmdl->isdelete = 0;
                            $_succss = $_bpmdl->save();
                        } else {
                            $errarr[$rowIndex] .= "Row #{$rowIndex} ".$p['domain']." not added due to: existing domain.";
                        }
                    } else {
                        if ($_bpmdl->isdelete == 0) {
                            $errarr[$rowIndex] .= "Row #{$rowIndex} is existing domain: ".$p['domain'].". information updated.";
                        } else {
                            $errarr[$rowIndex] .= "Row #{$rowIndex} ".$p['domain'].": Domain re-activated with the right information.";
                        }
                        $_bpmdl->setIsNewRecord(false);
                        $_bpmdl->setScenario('update');
                        $_bpmdl->attributes=$p;
                        //$_bpmdl->isdelete = 0;
                        $_succss = $_bpmdl->save();
                    }
                } else {
                    $isnew = true;
                    $_bpmdl = $model;
                    $_bpmdl->setIsNewRecord(true);
                    $_bpmdl->id=NULL;
                    $_bpmdl->attributes=$p;
                    //$_bpmdl->isdelete = 0;
                    $_succss = $_bpmdl->save();
                }
                $nof++;



                if ($isnew && !$_succss){
                    //print_r($model->getErrors());
                    //$errarr[$rowIndex] = "Row #{$rowIndex} didn't save, it may already exists in the domain.";

                    $ers = $_bpmdl->getErrors();
                    if (!empty($ers)) {
                        foreach($ers as $errors){
                            foreach($errors as $error){
                                if($error != '')
                                    $errarr[$rowIndex] .= "Row #{$rowIndex} ".$p['domain']." not added due to: ".$error;
                            }
                        }
                    }
                    $nof++;
                } else {
                    if ($isnew) $nos++;
                }

                unset($_bpmdl->attributes);
                //print_r($p);

            }
        }

        //print_r($nmp);
        //print_r($arr);
        if ($errarr) {
            $rowIndex++;
            $errarr[$rowIndex] = "You have added $nos domains totally, and about $nof domains not added.";
            return $errarr;
        } else {
            return true;
        }

        Yii::app()->end();
    }
}
