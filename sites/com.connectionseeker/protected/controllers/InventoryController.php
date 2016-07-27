<?php

class InventoryController extends RController
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
	 * @return array action filters
     * We can build one filter file, and put this function into the filter file
	 */
    public function filterAccessOwn($filterChain) {
        $allow = true;

        $cuid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($cuid);

        if(isset($roles['Publisher'])){
            //Do some stuff first, 
            if ($_GET['id']) {
                $model=$this->loadModel($_GET['id']);
                if ($model->user_id == Yii::app()->user->id) {
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
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Inventory;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Inventory']))
		{
            $model->attributes=$_POST['Inventory'];
            if($model->save())
                $this->redirect(array('view','id'=>$model->id));
            /*
            //cause in the model::beforeSave, we stored the domain info into the tbl.domain,
            //if we stored the domain sucessful, but the inventory didn't save, it doesn't matter. so we no need the Transaction.
            //then we can keep the code simple
            $transaction = Yii::app()->db->beginTransaction();
            try {
                $model->attributes=$_POST['Inventory'];
                if($model->save())
                    $this->redirect(array('view','id'=>$model->id));
            } catch (Exception $e) {
                // Was there an error?
                // Error, rollback transaction
                //print_r($e);
                $transaction->rollback();
            }//end transaction
            */
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

    //for client domains dropdown list
    public function actionDomains()
    {

        $limit = (isset($_GET['limit']) && $_GET['limit']) ? $_GET['limit'] : 10;

        $term = trim($_GET['term']);
        if (!empty($term)) {
            $criteria=new CDbCriteria;
            //$criteria->select='title'; // only select the 'title' column
            $criteria->condition='domain LIKE :qterm';
            //$criteria->params=array(':qterm'=>'%'.$term.'%');
            $criteria->params=array(':qterm'=>$term.'%');
            $criteria->limit=$limit; // only select the 'title' column
            $data = Domain::model()->findAll($criteria);
            //$data = Domain::model()->findAll('domain LIKE :qterm',
            //              array(':qterm'=> '%'.$term.'%'));
        } else {
            return ;
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
                   'label' => $p->$label,
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
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Inventory']))
		{
			$model->attributes=$_POST['Inventory'];
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

		$model=new Inventory('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Inventory']))
			$model->attributes=$_GET['Inventory'];
        $model->currentaction = "acquired";

        $uid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($uid);
        if(isset($roles['Publisher'])){
            $model->user_id = $uid;
        } else {
            if (isset($roles['Admin']) || isset($roles['InternalOutreach'])) {
                //do nothing for now;
            } else {
                $model->created_by = $uid;
            }
        }
        $model->ispublished = 0;
        //$model->ispublished = "!=1";
        //##$model->isdenied = "!=1";

		$this->render('index',array(
			'model'=>$model,
			'roles'=>$roles,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionPublished()
	{

        if (isset($_GET['pageSize'])) {
            Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
            unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
        }

		$model=new Inventory('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Inventory']))
			$model->attributes=$_GET['Inventory'];

        $uid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($uid);
        if(isset($roles['Publisher'])){
            $model->user_id = $uid;
        } else {
            if (isset($roles['Admin']) || isset($roles['InternalOutreach'])) {
                //do nothing for now;
            } else {
                $model->created_by = $uid;
            }
        }
        $model->currentaction = "published";
        $model->ispublished = 1;
        //##$model->isdenied = "!=1";

		$this->render('index',array(
			'model'=>$model,
			'roles'=>$roles,
		));
	}

	public function actionDenied()
	{

        if (isset($_GET['pageSize'])) {
            Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
            unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
        }

		$model=new Inventory('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Inventory']))
			$model->attributes=$_GET['Inventory'];

        $uid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($uid);
        if(isset($roles['Publisher'])){
            $model->user_id = $uid;
        } else {
            if (isset($roles['Admin']) || isset($roles['InternalOutreach'])) {
                //do nothing for now;
            } else {
                $model->created_by = $uid;
            }
        }
        $model->currentaction = "denied";
        $model->isdenied = 1;

		$this->render('index',array(
			'model'=>$model,
			'roles'=>$roles,
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
        $rs = array('success' => true, 'msg' => Yii::t('Task', 'Updated was Successful.'));
        if(isset($_GET['attrname']) && isset($_GET['attrvalue'])) {
            Yii::import('application.vendors.*');
            $attrname = str_replace("[]", "", $_GET['attrname']);
            $attrvalue = trim($_GET['attrvalue']);

            if (stripos($attrname, "category") !== false || stripos($attrname, "owner_channel_id") !== false 
                || stripos($attrname, "client_id") !== false) {
                //$model->category = explode(",", $attrvalue);
                $attrvalue = explode(",", $attrvalue);
                $model->$attrname = $attrvalue;
            }/* else if (stripos($attrname, "owner_channel_id") !== false) {
                $model->owner_channel_id = explode(",", $attrvalue);
                //$model->$attrname = $attrvalue;
            } else if (stripos($attrname, "probability") !== false) {
                $model->$attrname = $attrvalue;
            }*/ else {
                $model->$attrname = $attrvalue;
            }

            if($model->save()) {
                //do nothing;
            } else {
                $rs['success'] = false;
                $rs['msg'] = Yii::t('Task', 'Updated was Failure.');
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
        ini_set("memory_limit", "512M");
        ini_set("post_max_size", "128M");//remove it from here, move it into the .htaccess
        ini_set("max_execution_time", "1200");
        ini_set("max_input_time", "1200");//remove it, move it into the .htaccess
        ini_set("upload_max_filesize", "64M");//remove it from here, move it into the .htaccess

		$model=new Inventory;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

        if(isset($_POST['Inventory']))
        {
            //print_r($_POST['Inventory']);
            //echo Yii::app()->basePath;
            $model->attributes=$_POST['Inventory'];
            $model->upfile=CUploadedFile::getInstance($model,'upfile');
            $file_ext = strtolower($model->upfile->getExtensionName());
            if (!in_array($file_ext, array("csv","xls","xlsx","ods","slk","xml"))) {
                $rs[] = 'We are not support '.$file_ext.' for now.';
            }
            $fsrtn = $model->upfile->saveAs(Yii::app()->basePath.'/runtime/inventory/'.$model->upfile);
            $_ecode = $model->upfile->getError();
            if ($_ecode > 0) {
                $rs[] = Utils::getUploadError($_ecode);
            }
            if ($fsrtn && !$rs) {
                $_POST['Inventory']['filename'] = $model->upfile->getName();
                $_POST['Inventory']['file_ext'] = $file_ext;

                switch ($file_ext) {
                    case 'xls':
                    case 'xlsx':
                    case 'ods':
                    case 'slk':
                    case 'xml':
                    case 'csv':
                        $rs = $this->__addInventoriesFromFile($_POST['Inventory']);
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

/*
Array
(
    [action] => 1
    [client_id] => 1
    [campaign_id] => 1
    [channel_id] => 2
    [upfile] => 
    [filename] => accounting_report-2011122-1325519521.csv
)
*/
    /*
    * Parse the file, and insert the data into the table.
    */
    //public function actionTest(){
    private function __addInventoriesFromFile($p){
        $errarr = array();//error array

        extract($p);
        $fpath = Yii::app()->basePath.'/runtime/inventory/'.$filename;
        //$fpath = Yii::app()->basePath.'/runtime/inventory/Peachy Blog LIst (March) - updated.xls';
        //$fpath = Yii::app()->basePath.'/runtime/inventory/accounting_report-2011122-1325519521.csv';
        if (!file_exists($fpath)) {
            $errarr[] = "File not exists";
            return $errarr;
        }

        $types = Types::model()->actived()->findAll();
        /*
        $gtps = CHtml::listData($types, 'refid', 'typename', 'type');
        $stypes = array_flip($gtps['site']);
        $categories = array_flip($gtps['category']);
        */
        //create an array, typename as the key, and the refid as the value,so that you no need array_flip it again
        $gtps = CHtml::listData($types, 'typename', 'refid', 'type');
        $stypes = $gtps['site'];
        $categories = $gtps['category'];
        $channels = $gtps['channel'];
        $linktypes = $gtps['linktask'];
        //print_r($categories);

        //We need add transaction into here.
        //$domodel = New Domain;
        $model = New Inventory;
        if ($action == 1) $lnkmodel = New Link;

        #####################################4/16/2012#############################################
        //we can put this part into the beforeSave also, 
        //But if we did batch upload, the performance will be low, So we put it to here!
        $__uid = Yii::app()->user->id;
        $__roles = Yii::app()->authManager->getRoles($__uid);
        $__ivtuid = 0;
        if(isset($__roles['Publisher'])){
            $__ivtuid = $__uid;
        }
        #####################################4/16/2012#############################################

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
        /*
        $header = array("Domain","Category","Site Type","Channel","Link Type","Notes");
        $domainheader = array("Domain"=>"domain","Site Type"=>"stype");
        */
        $ivtheader = array("Domain"=>"domain","Category"=>"category_str","Link Type"=>"accept_tasktype_str","Status"=>"status",
             "Channel"=>"channel_str","Notes"=>"notes","Site Type"=>"stype","Target URL" => 'targeturl',"Source URL"=>"sourceurl",
             "Date Added"=>"added","Last Checked"=>"checked","Anchor Text"=>"anchortext","Internal URL"=>"sourceurl");

        $arr = array();
        $nmp = array();//the excel Column Index mapping to $header array
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

                //save data into tbl.domain & tbl.inventory,
                $p = array();
                foreach ($ivtheader as $hk => $hv) {
                    $hk = strtolower($hk);
                    if (!isset($nmp[$hk])) {
                        continue;
                    }
                    $pv = trim($arr[$rowIndex][$nmp[$hk]]);
                    if ($hv == 'category_str') {
                        $p['category'] = array();
                        if ($pv) {
                            $_tmps = array();
                            $_tmps = explode(",", $pv);
                            $c = array();
                            foreach ($_tmps as $v) {
                                $v = trim($v);
                                $c[$v] = $categories[$v];
                            }

                            //reset the value, if it was category.
                            $p['category'] = $c;
                        }
                    } elseif ($hv == 'accept_tasktype_str') {
                       $p['accept_tasktype'] = array();
                       if ($pv) {
                            $_tmps = array();
                            $_tmps = explode(",", $pv);
                            $c = array();
                            foreach ($_tmps as $v) {
                                $v = trim($v);
                                $c[$v] = $linktypes[$v];
                            }

                            //reset the value, if it was tasktype.
                            $p['accept_tasktype'] = $c;
                        }
                    } elseif ($hv == 'channel_str') {
                       $p['channel_id'] = array();
                       if ($pv) {
                            $_tmps = array();
                            $_tmps = explode(",", $pv);
                            $c = array();
                            foreach ($_tmps as $v) {
                                $v = trim($v);
                                $c[$v] = $channels[$v];
                            }

                            //reset the value, if it was channel.
                            $p['channel_id'] = $c;
                        }
                    } elseif ($hv == 'stype') {
                        //string to id;
                        $pv = $stypes[$pv];
                    }/* elseif ($hv == 'channel_id') {
                        //string to id;
                        $pv = $channels[$pv];
                        if (!$pv) $pv = $channel_id;
                    }*/ elseif ($hv == 'status') {
                        $pv = (strtolower($pv) == "inactive" || $pv == "0") ? 0 : 1;
                    } elseif (in_array($hv, array("added","checked"))) {
                        $pv = (empty($pv)) ? time() : strtotime($pv);
                    }

                    if (empty($pv) && ($hv != 'status')) $pv = "";
                    $p[$hv] = $pv;
                }

                if ($__ivtuid) {
                    $p['user_id'] = $__ivtuid;
                }

                if ($action == 1 || $action == 4) {
                    $dmi = $model->findByAttributes(array('domain' => $p['domain']));
                    if (!empty($dmi)) {
                        $dmi->setIsNewRecord(false);
                        $dmi->setScenario('update');
                        $isnew = false;
                    } else {
                        $isnew = true;
                        $dmi = $model;
                        $dmi->setIsNewRecord(true);
                        $dmi->id=NULL;
                    }

                    $_succss = false;

                    //#####################################3/27/2012#######################//
                    /*
                    //uncomment following code, it will store the domain directly into inventory if it is not exist by default.
                    if (($action == 4) ||
                        ($action == 1 && $isnew)) {
			            $dmi->attributes=$p;
                        $_succss = $dmi->save();
                    }
                    */
                    if ($action == 1 && $isnew) {
                        $errarr[$rowIndex] .= "Row #{$rowIndex}: Stored failure due to the domain does not exist in inventory";
                        continue;
                    }
                    if ($action == 4) {
                        $dmi->attributes=$p;
                        $_succss = $dmi->save();
                    }
                    //#####################################3/27/2012#######################//

                    if (!$_succss){
                        //print_r($model->getErrors());
                        //$errarr[$rowIndex] = "Row #{$rowIndex} didn't save, it may already exists in the inventory.";

                        $ers = $dmi->getErrors();
                        if (!empty($ers)) {
                            foreach($ers as $errors){
                                foreach($errors as $error){
                                    if($error != '')
                                        $errarr[$rowIndex] .= "Row #{$rowIndex}: ".$error;
                                }
                            }
                        }
                    }

                    if ($action == 1) {
                        //we store the record into the tbl.inventory_link directly without QA.
                        if ($dmi->id) {
                            $lnkmodel->setIsNewRecord(true);
                            $lnkmodel->id=NULL;
                            if (empty($p['inventory_id'])) $p['inventory_id'] = $dmi->id;
                            $p['targetdomain'] = SeoUtils::getDomain($p['targeturl']);
                            if (strlen($dmi->category) > 0) {
                                $p['category_id'] = substr($dmi->category, 1, -1);
                                $_cats = explode("|", $p['category_id']);
                                $p['category_id'] = $_cats[0];
                            }
                            if (strlen($dmi->accept_tasktype) > 0) {
                                $p['tasktype_id'] = substr($dmi->accept_tasktype, 1, -1);
                                $_types = explode("|", $p['tasktype_id']);
                                $p['tasktype_id'] = $_types[0];
                            }
                            $p['campaign_id'] = $campaign_id;

                            $lnkmodel->attributes=$p;
                            if (!$lnkmodel->save()) {
                                $ers = $lnkmodel->getErrors();
                                if (!empty($ers)) {
                                    foreach($ers as $errors){
                                        foreach($errors as $error){
                                            if($error != '')
                                                $errarr[$rowIndex] .= "Row #{$rowIndex}: ".$error."  ".$p['category_id'];
                                        }
                                    }
                                }
                            }
                            unset($lnkmodel->attributes);
                        } else {
                            $errarr[$rowIndex] .= "Row #{$rowIndex}: Link stored failure, Please try it again.";
                        }
                    }

                    unset($dmi->attributes);
                    //print_r($p);
                } else {
                    //do batch QA;
                }
            }
        }
        //print_r($nmp);
        //print_r($arr);
        if ($errarr) {
            return $errarr;
        } else {
            return true;
        }

        Yii::app()->end();
    }

	/**
	 * Returns the number of tasks and number of the link index of the inventory domain.
	 */
	public function actionNumber()
	{
        Yii::app()->end();
	}

    public function actionGetattr()
    {
        $ids = $_REQUEST['ids'];
        if (!is_array($ids)) {
            $ids = array($ids);
        }

        $attrs = $_REQUEST['attrs'];
        if (empty($attrs)) $attrs = "channel_id";//set a default
        if (!is_array($attrs)) {
            $attrs = array($attrs);
        }

        $rs = array();
        $model = new Inventory;
        $i = 0;
        foreach($ids as $id) {
            if (isset($_REQUEST['byattr']) && strtolower($_REQUEST['byattr']) != "id") {
                $byattr = strtolower($_REQUEST['byattr']);
                $m = $model->findByAttributes(array($byattr => $id));
            } else {
                $m = $model->findByPk($id);
            }
            foreach ($attrs as $v) {
                $v = strtolower($v);
                if ($v == "channel_id") {
                    if ($m->$v) {
                        //$tmp = substr($m->$v, 1, (strlen($m->$v) -2));
                        $tmp = substr($m->$v, 1, -1);
                        $rs[$i][$v] = explode("|", $tmp);
                    } else {
                        $rs[$i][$v] = "";
                    }
                } else {
                    if ($m->$v) {
                        $rs[$i][$v] = $m->$v;
                    } else {
                        $rs[$i][$v] = "";
                    }
                }
                $rs[$i]['idx'] = $id;
            }
            $i++;
        }

        $rtn = array();
        $rtn['rs'] = $rs;
        $rtn['success'] = true;
        echo CJSON::encode($rtn);
        Yii::app()->end();
    }

    public function actionExportacquired(){
        set_time_limit(3600);
        ini_set("memory_limit", "512M");

        $model=new Inventory('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['Inventory'])) {
            if (isset($_GET['Inventory']['acquireddate_start'])) {
                $_GET['Inventory']['acquireddate'] = ">=" . $_GET['Inventory']['acquireddate_start'];
                unset($_GET['Inventory']['acquireddate_start']);
            }
            $model->attributes=$_GET['Inventory'];
        }
        $model->currentaction = "acquired";
        $model->ispublished = 0;

        $exportType = "Excel5";
        if (strtolower($_GET['Domain']['export']) == 'csv') {
            $exportType = "CSV";
        }

		/*
		//caution: this codes burns memory!
        $this->widget('application.extensions.lkgrid.EExcelView', array(
            'id'=>'inventory-grid',
            'pageSize'=>$model->actived()->simpleSearch()->getTotalItemCount(),
            'filename'=>date("Y-m-d")."_acquired_domain",
            'exportType' => $exportType,
            'dataProvider'=>$model->actived()->getDomainNote()->simpleSearch(),
            'columns'=>array(
                'domain',
                'ispublished',
                'rdnote.allnotes',
            ),
        ));
		*/
        if ($model->actived()->search()->getTotalItemCount() > 20000) {
            exit("Out of export limit, Please narrow your search result.");
        }

        $this->widget('application.extensions.lkgrid.EExcelView', array(
            'id'=>'inventory-grid',
            //##'pageSize'=>$model->with('ronenote','roneprice')->actived()->search()->getTotalItemCount(),
            'pageSize'=>20000,
            'filename'=>date("Y-m-d")."_acquired_domain",
            'exportType' => $exportType,
            'dataProvider'=>$model->with('ronenote','roneprice')->actived()->search(),
            //'dataProvider'=>$model->actived()->search(),
            'columns'=>array(
                'domain',
                'acquireddate',
                'category_str',
                'owner_channel_str',
                'ronenote.notes',
                'roneprice.price',
                //'ispublished',
            ),
        ));
    }

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Inventory::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='inventory-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
