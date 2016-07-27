<?php
/*
* When using the API, we would like to have the following URL scheme:
*
*    View all records: index.php/api/client (HTTP method GET)
*    View a single record: index.php/api/client/123 (also GET )
*    Create a new record: index.php/api/client (POST)
*    Update a records: index.php/api/client/123 (PUT)
*    Delete a records: index.php/api/client/123 (DELETE)
*    We will keep OPTIONS for the furture;
* 
*/
class ApiController extends Controller
{
    public $apiKey;//We can leave a public apikey for develope
    protected $model = null;

    //If you set the _debug as true, then the api will not authorize. when you lunch it as production, then set it to false.
    private $_debug = true;

    public $customizApis = array(
        //'client'  => array('POST' => 'createclient', 'GET' => 'getclient'),
        'user'    => array('POST' => 'createUser'),
        'clientDomain'    => array('POST' => 'createClientDomain'),
    );

	private $codes = array(
		'100' => 'Continue',
		'200' => 'OK',
		'201' => 'Created',
		'202' => 'Accepted',
		'203' => 'Non-Authoritative Information',
		'204' => 'No Content',
		'205' => 'Reset Content',
		'206' => 'Partial Content',
		'300' => 'Multiple Choices',
		'301' => 'Moved Permanently',
		'302' => 'Found',
		'303' => 'See Other',
		'304' => 'Not Modified',
		'305' => 'Use Proxy',
		'307' => 'Temporary Redirect',
		'400' => 'Bad Request',
		'401' => 'Unauthorized',
		'402' => 'Payment Required',
		'403' => 'Forbidden',
		'404' => 'Not Found',
		'405' => 'Method Not Allowed',
		'406' => 'Not Acceptable',
		'409' => 'Conflict',
		'410' => 'Gone',
		'411' => 'Length Required',
		'412' => 'Precondition Failed',
		'413' => 'Request Entity Too Large',
		'414' => 'Request-URI Too Long',
		'415' => 'Unsupported Media Type',
		'416' => 'Requested Range Not Satisfiable',
		'417' => 'Expectation Failed',
		'418' => 'Occurs Exception, Create Failed',
		'419' => 'Expired',
		'500' => 'Internal Server Error',
		'501' => 'Not Implemented',
		'503' => 'Service Unavailable',
		'504' => 'Keyword Format Not Match',
		'999' => 'Task Processing',
		'1000' => 'Task Pending'
	);

    /*
    public function beforeAction($event)
    {
        //echo $_GET['email'];
        //echo $_GET['id'];
        //exit;
        return parent::beforeAction($event);
    }
    */

    public function filters() {
        if (!$this->_debug) {
            $restFilters = array('restAccessRules+ list view create update delete client');
            if(method_exists($this, '_filters'))
                return CMap::mergeArray($restFilters, $this->_filters());
            else
                return $restFilters;
        }
    }

	/**
	 * Controls access to restfull requests
	 */ 
	public function filterRestAccessRules($c)
    {
        $rtn = array();
        Yii::app()->clientScript->reset(); //Remove any scripts registered by Controller Class
        //Yii::app()->onException = array($this, 'onException'); //Register Custom Exception

        if(!(isset($_SERVER['HTTP_X_CSREST_APIKEY'])) || !(isset($_SERVER['HTTP_X_CSREST_SIGNATURE']))
            || !(isset($_SERVER['HTTP_X_CSREST_EXPIRES']))) {
            // Error: Unauthorized
            $rtn = $this->raiseErrorByCode(400);
        } else {
            $now = time();
            $expires = $_SERVER['HTTP_X_CSREST_EXPIRES'];//we will keep a session, but for now, it is ok;
            if ($expires <= ($now - 86400) || $expires >= ($now + 86400)) {
                $rtn = $this->raiseErrorByCode(419);
            } else {
                $apikey = $_SERVER['HTTP_X_CSREST_APIKEY'];
                $signature = $_SERVER['HTTP_X_CSREST_SIGNATURE'];
                //We will get secretkey from DB via search $apikey in the furture;
                $secretkey = "";

                $_signature = hash_hmac('sha1', $apikey."\n".$expires, $secretkey, true);
                $_safesignature = urlencode(base64_encode($_signature));

                if($signature != $_safesignature) {
                    // Error: Unauthorized
                    $rtn = $this->raiseErrorByCode(401);
                }
            }
        }

        if ($rtn) {
            @header("{$_SERVER['SERVER_PROTOCOL']} {$rtn['code']} {$rtn['message']}");
            @header('Content-type: application/json');
            echo CJSON::encode($rtn);
            exit;
        }

        // This tells the filter chain $c to keep processing.
        $c->run();
    }

    public function actionView($id = null)
    {
        if(!isset($_GET['id'])) {
            $rtn = $this->raiseErrorByCode(500);
        } else {
            $cname = $_GET['model'];
            $cname = ucfirst($cname);

            switch($cname) {
                // Find respective model    
                case 'User':
                case 'Client':
                case 'Campaign':
                //case 'Domain':
                    $model = new $cname;
                    if (is_numeric($_GET['id'])) {//it is int type
                        $model = $model->findByPk($_GET['id']);
                    } elseif (filter_var($id, FILTER_VALIDATE_EMAIL) !== false) {//id is email
                        $model = $model->findByAttributes(array('email'=>$_GET['id']));
                    } else {
                        $model = null;
                    }
                    break;
                case 'Processing':
                    $rtn = $this->getCampaignDetails($id);
                    break;
                case 'Domain':
                    $rtn = $this->getDomainProfile($id);
                    break;
                default:
                    $rtn = $this->raiseErrorByCode(501);
            }

            if (empty($rtn)) {
                // Did we find the requested model? If not, raise an error
                if(is_null($model)) {
                    $rtn = $this->raiseErrorByCode(404);
                } else {
                    if(!array_key_exists('MorrayBehavior', $model->behaviors()))
                      $model->attachBehavior('MorrayBehavior', new MorrayBehavior());

                    $cname{0} = strtolower($cname{0});
                    $rtn = array('success' => true,
                                 'message' => "Record Retrieved Successfully",'code'=>200,
                                 'data'    => array('totalCount'=>1, $cname=>$model->toArray()));
                }
            }
        }

        if ($rtn) {
            @header("{$_SERVER['SERVER_PROTOCOL']} {$rtn['code']} {$rtn['message']}");
            @header('Content-type: application/json');
            echo CJSON::encode($rtn);
            exit;
        }
        Yii::app()->end();
    }

    public function actionCreate()
    {
        $cname = $_GET['model'];
        $cname = ucfirst($cname);

        switch(strtolower($cname)) {
            // Get an instance of the respective model
            case 'user':
            //case 'client':
                $model = new $cname;
                break;
            case 'clientdomain':
            case 'competitor':
                $rtn = $this->createClientDomains();
                break;
            case 'campaign':
                $rtn = $this->createCampaign();
                break;
            default:
                $rtn = $this->raiseErrorByCode(501);
        }

        if (!isset($rtn) && !empty($rtn)) {
            $postparas = $_POST;
            if (empty($_POST)) {
                $jsonpost = file_get_contents('php://input');
                $postparas = CJSON::decode($jsonpost,true);
            }

            // Try to assign POST values to attributes
            foreach($postparas as $var=>$value) {
                // Does the model have this attribute? If not raise an error
                if($model->hasAttribute($var)) {
                    $model->$var = $value;
                }/* else {
                    $this->raiseErrorByCode(500);
                }*/
            }

            // Try to save the model
            if($model->save()) {
                if(!array_key_exists('MorrayBehavior', $model->behaviors()))
                  $model->attachBehavior('MorrayBehavior', new MorrayBehavior());

                //$cname = get_class($model);
                $cname{0} = strtolower($cname{0});
                $rtn = array('success' => true,
                             'message' => "Record Retrieved Successfully", 'code'=>200,
                             'data'    => array('totalCount'=>$count, $cname=>$model->toArray()));
            } else {
                $ers = $model->getErrors();
                $rtn = $this->raiseErrorByCode(500, $ers);
            }
        }

        if ($rtn) {
            @header("{$_SERVER['SERVER_PROTOCOL']} {$rtn['code']} {$rtn['message']}");
            @header('Content-type: application/json');
            echo CJSON::encode($rtn);
            exit;
        }
        Yii::app()->end();
    }

    public function actionList()
    {
        $cname = $_GET['model'];
        $cname = ucfirst($cname);

        // Get the respective model instance
        switch($cname) {
            // Get an instance of the respective model
            case 'Userxxxx':
            //case 'User':
            //case 'Client'://Donot turn client list on, it is dangerous
                $model = new $cname;
                $model = $model->findAll();
                break;
            default:
                $rtn = $this->raiseErrorByCode(501);
        }

        if (!isset($rtn)) {
            // Get some results?
            if(empty($model)) {
                $rtn = $this->raiseErrorByCode(404);
            } else {
                // Prepare response
                $rs = array();
                foreach($model as $mdl) {
                    //$rs[] = (array)$mdl->attributes;
                    if(!array_key_exists('MorrayBehavior', $mdl->behaviors())) {
                      $mdl->attachBehavior('MorrayBehavior', new MorrayBehavior());
                      $rs[] = (array)$mdl->toArray();
                    }
                }

                $cname{0} = strtolower($cname{0});
                $rtn = array('success' => true,
                             'message' => "Record(s) Created", 'code'=>200,
                             'data'    => array('totalCount'=>$count, $cname=>$rs));

            }
        }

        if ($rtn) {
            @header("{$_SERVER['SERVER_PROTOCOL']} {$rtn['code']} {$rtn['message']}");
            @header('Content-type: application/json');
            echo CJSON::encode($rtn);
            exit;
        }
        Yii::app()->end();
    }

    /*
    * This one is a customize actionView, here the id isn't int type, it is string type(email)
    */
	public function actionCmview($id=null)
    {
        $this->actionView($id);
        exit;
    }

	public function actionUser($id=null)
    {
        $callback = NULL;
        $rtn = array();
        $httpmethod = $_SERVER['REQUEST_METHOD'];

        if (isset($this->customizApis['user'][$httpmethod])) {
            $callback = $this->customizApis['user'][$httpmethod];
        } else {
            $rtn = $this->raiseErrorByCode(500);
        }

        if (empty($rtn) && is_callable(array($this, $callback))) {
            // get the request data
            $data = NULL;
            if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                if ($_GET["id"] && is_numeric($_GET["id"])) {
                    $data["id"] = $_GET["id"];
                } else if($id && is_numeric($_GET["id"])) {
                    $data["id"] = $id;
                } else {
                    $rtn = $this->raiseErrorByCode(406);
                }
            } else if($_SERVER['REQUEST_METHOD'] == 'POST') {
                $data = empty($_POST) ? $GLOBALS['HTTP_RAW_POST_DATA'] : $_POST;
                $data = json_decode($data, true);
            } else if ($tmp = file_get_contents('php://input')) {
                $data = json_decode($tmp, true);
            }

            if (empty($rtn)) $rtn = call_user_func(array($this, $callback), $data);
        } else {
            $rtn = $this->raiseErrorByCode(405);
        }

        if ($rtn) {
            @header("{$_SERVER['SERVER_PROTOCOL']} {$rtn['code']} {$rtn['message']}");
            @header('Content-type: application/json');
            echo CJSON::encode($rtn);
            exit;
        }
        Yii::app()->end();
        //exit;
    }

    //Create an User
    public function createUser($data){
        $rtn = array();

        $ufields = array('username'=>'username','password'=>'password','password2'=>'password2','client_id'=>'client_id',
            'email'=>'email','type'=>'type','aschannel'=>'aschannel','display_mode'=>'display_mode','role'=>'role');
        $cfields = array('name'=>'username','company'=>'company','contact_name'=>'contact_name','email'=>'email',
                         'telephone'=>'telephone','assignee'=>'assignee','domain'=>'domain','status'=>'status');

        $role = 'Marketer';
        if (isset($data['role']) && !empty($data['role'])) {
            $role = $data['role'];
        }
        $count = 0;
        if (!isset($data['client_id'])) $data['client_id'] = 0;
        if (!is_numeric($data['client_id'])) $data['client_id'] = 0;

		if(isset($data))
		{
            if ($data['client_id']) {
                //do nothing;
            } else {
                $cmodel=new Client;
                $domodel=new ClientDomain;

                foreach($cfields as $ku => $vu) {
                    if($cmodel->hasAttribute($ku)) {
                        $cmodel->$ku = $data[$vu];
                    }
                }
                $cmodel->user_id = 1;
                $cmodel->assignee = 1;

                if($cmodel->save()) {
                    $data['client_id'] = $cmodel->id;
                    $count++;
                    if (!empty($data['domain'])) {
                        foreach ($data['domain'] as $k => $v) {
                            if (!empty($v)) {
                                $domodel->id=NULL;
                                $domodel->client_id=$cmodel->id;
                                $domodel->domain=$v;
                                $domodel->setIsNewRecord(true);
                                if (!$domodel->save()) {
                                    $ers = $domodel->getErrors();
                                    $rtn = $this->raiseErrorByCode(418, $ers);
                                } else {
                                    $count++;
                                }
                            }
                        }
                    }
                } else {
                    $ers = $cmodel->getErrors();
                    $rtn = $this->raiseErrorByCode(418, $ers);
                }
            }

            if (empty($rtn)) {
                $model=new User;
                //$model->attributes=$_POST['User'];
                foreach($ufields as $ku => $vu) {
                    if($model->hasAttribute($ku)) {
                        $model->$ku = $data[$vu];
                    }
                }
                $model->password2 = $data["password2"];
                if (is_numeric($data['client_id']) && $data['client_id']) {
                    $model->client_id = $data['client_id'];
                }

                if($model->save()) {
                    $count++;
                    $auth = new AuthAssignment;
                    $auth->attributes=array('itemname'=>$role);
                    $auth->userid = $model->id;
                    $auth->save();
                    $count++;
                } else {
                    $ers = $model->getErrors();
                    $rtn = $this->raiseErrorByCode(418, $ers);
                }
            }
		}

        if (empty($rtn)) {
            //$model = User::model()->findByPk($model->id);

            if(!array_key_exists('MorrayBehavior', $model->behaviors()))
              $model->attachBehavior('MorrayBehavior', new MorrayBehavior());

            $cname = get_class($model);
            $cname{0} = strtolower($cname{0});
            $rtn = array('success' => true,
                         'message' => "Record(s) Created", 'code'=>200,
                         'data'    => array('totalCount'=>$count, $cname=>$model->toArray()));
        }

        if ($rtn) {
            @header("{$_SERVER['SERVER_PROTOCOL']} {$rtn['code']} {$rtn['message']}");
            @header('Content-type: application/json');
            echo CJSON::encode($rtn);
            exit;
        }
        Yii::app()->end();
    }

    //Add related domains into client, add competitors to client domain
    public function createClientDomains(){
        $_modelname = strtolower($_GET['model']);
        switch($_modelname) {
            case 'clientdomain':
                $cname = "ClientDomain";
                break;
            case 'competitor':
                $cname = "Competitor";
                break;
            default:
                $cname = "ClientDomain";
        }

        //###exit;
        //$model=new ClientDomain;
        //Create a instant
        $model=new $cname;

        $data = $_POST;
        if (empty($_POST)) {
            $jsonpost = file_get_contents('php://input');
            $data = CJSON::decode($jsonpost,true);
        }
        if (empty($data)) {
            $rtn = $this->raiseErrorByCode(418);
        } else {
            //client_id
            //domains => array(array(client_id,domain,),array())

            $count = 0;
            if (!empty($data['domains'])) {
                $domains = $data['domains'];
                if (isset($domains["domain"])) $domains = array($domains);//transfer 1D array to 2D array.
                $domaincount = count($domains);

                $rows = array();
                $transaction = Yii::app()->db->beginTransaction();
                try {
                    $cpts = array();
                    $i = 0;
                    foreach ($data['domains'] as $k => $v) {
                        if (!empty($v)) {
                            $cpts[$i]["domain_id"] = $v["domain_id"];
                            if ($_modelname == 'competitor') {
                                $ci = $model->findByAttributes(array('domain' => $v["domain"]));
                                if (!empty($ci)) {
                                    //echo $v["domain"].$ci->id;
                                    $cpts[$i]["competitor_id"] = $ci->id;
                                    $i++;
                                    $count++;
                                    $rows[] = $ci->attributes;
                                    continue;
                                }
                            }

                            $model->id=NULL;
                            foreach($v as $var=>$value) {
                                // Does the model have this attribute? If not raise an error
                                if($model->hasAttribute($var)) {
                                    $model->$var = $value;
                                }
                            }
                            $model->setIsNewRecord(true);
                            if (!$model->save()) {
                                $ers = $model->getErrors();
                                $rows[] = $this->raiseErrorByCode(418, $ers);
                            } else {
                                if ($_modelname == 'competitor') $cpts[$i]["competitor_id"] = $model->id;
                                $count++;
                                $rows[] = $model->attributes;
                            }
                            $i++;
                        }
                    }

                    if ($_modelname == 'competitor' && !empty($cpts)) {
                        //print_r($cpts);
                        $cptmodle = new ClientDomainCompetitor;
                        //$cptmodle->rcompetitor = $cpts;//for ClientDomain model implement.
                        foreach($cpts as $cv){
                            $cptmodle->id=NULL;
                            $cptmodle->attributes=$cv;
                            $cptmodle->setIsNewRecord(true);
                            $cptmodle->save();
                        }
                    }

                    // Commit the transaction
                    $transaction->commit();

                    if ($count > 0) {
                        $cname{0} = strtolower($cname{0});
                        $rtn = array('success' => true,
                                     'message' => "Record(s) Created", 'code'=>200,
                                     'data'    => array('totalCount'=>$count, $cname=>$rows));
                    } else {
                        $rtn = array('success' => false,
                                     'message' => "Record(s) Created Failure", 'code'=>406,
                                     'data'    => array('errorCode'=>406, 'errorDetails'=>$rows));
                    }
                } catch (Exception $e) {
                    // Was there an error?
                    // Error, rollback transaction
                    //print_r($e);
                    $rtn = $this->raiseErrorByCode(418, $e);
                    $transaction->rollback();
                }//end transaction
            }

        }

        if ($rtn) {
            @header("{$_SERVER['SERVER_PROTOCOL']} {$rtn['code']} {$rtn['message']}");
            @header('Content-type: application/json');
            echo CJSON::encode($rtn);
        }
        exit;
        //Yii::app()->end();
    }

    /*
    * Create Campaign & Task
    *
    * 
    */
    public function createCampaign(){

        $data = $_POST;
        if (!isset($_POST['Campaign'])) {
            $jsonpost = file_get_contents('php://input');
            $data = CJSON::decode($jsonpost,true);
        }

		if(isset($data['Campaign']))
		{
            $model   = new Campaign;
            $ctmodel = new CampaignTask;
			$model->attributes=$data['Campaign'];

            if (isset($data['Campaign']['domain_id']) && is_numeric($data['Campaign']['domain_id'])) {
                $domain_id = (int)$data['Campaign']['domain_id'];
                $cdmodel = new ClientDomain;
                $cdi = $cdmodel->findByPk($domain_id);
                if (!empty($cdi)) {
                    $model->domain = $cdi->domain;
                } else {
                    return $rtn = $this->raiseErrorByCode(412);
                }
            } elseif (isset($data['Campaign']['domain'])) {
                $domain = trim($data['Campaign']['domain']);
                $cdmodel = new ClientDomain;
                $cdi = $cdmodel->findByAttributes(array('domain' => $domain,'client_id'=>$model->client_id));
                if (!empty($cdi)) {
                    $model->domain_id = $cdi->id;
                } else {
                    $cdmodel->domain = $domain;
                    $cdmodel->client_id = $model->client_id;
                    if ($cdmodel->save()) {
                        $model->domain_id = $cdmodel->id;
                    }
                }
            } else {
                 return $rtn = $this->raiseErrorByCode(412);
            }

            $taskmodel=new Task;
            $transaction = Yii::app()->db->beginTransaction();
            try {
                $rows = array();
                if($model->save()) {
                    $rows["Campaign"] = $model->attributes;
                    $keywords = array();
                    $target_urls = array();
                    if (isset($data['CampaignTask'])) {
                        $totalcount = 0;
                        $kwcount = $data['CampaignTask']['kwcount'];
                        $_urls = $data['CampaignTask']['targeturl'];
                        $tiers = $data['CampaignTask']['tierlevel'];
                        $anchortext = $data['CampaignTask']['keyword'];
                        $i = 0;
                        ////foreach ($data['CampaignTask']['keyword'] as $k => $v) {
                        foreach ($data['CampaignTask']['kwcount'] as $k => $v) {
                            $v = trim($v);
                            if (empty($v)) $v = $kwcount[$k] = 1;
                            if (!empty($v) && $kwcount[$k] > 0 && !empty($_urls[$k])) {
                                $totalcount += (int)$kwcount[$k];
                                $keywords[$i]['kwcount'] = (int)$kwcount[$k];
                                $keywords[$i]['keyword'] = trim($anchortext[$k]);
                                $keywords[$i]['targeturl'] = $_urls[$k];
                                $keywords[$i]['tierlevel'] = $tiers[$k];
                                $keywords[$i]['used'] = 0;

                                $taskids = array();
                                for ($j = 0; $j < $kwcount[$k]; $j++) {
                                    $taskmodel->setIsNewRecord(true);
                                    $taskmodel->id = NULL;
                                    $taskmodel->campaign_id = $model->id;
                                    $taskmodel->anchortext  = trim($anchortext[$k]);
                                    $taskmodel->targeturl   = trim($_urls[$k]);
                                    $taskmodel->tierlevel   = $tiers[$k];
                                    $taskmodel->save();
                                    $rows["CampaignTask"][] = $taskmodel->attributes;

                                    //$taskids[$taskmodel->id] = $taskmodel->id;
                                    $taskids[] = $taskmodel->id;
                                }

                                $keywords[$i]['taskids'] = array_values($taskids);
                                $i++;
                            }
                        }

                        // ############## we can remove the following code for the currently requirement 5/8/2012 ############//
                        //for capability, cause i worry about they will seperate it from keyword again, so i keep the following code.
                        $i = 0;
                        foreach ($_urls as $k => $v) {
                            if (!empty($v)) {
                                $target_urls[$i]['targeturl'] = $v;
                                $target_urls[$i]['used'] = 0;
                                $i++;
                            }
                        }
                        // ############### end of remove 5/8/2012 #############################//

                        //$ctmodel->setIsNewRecord(true);
                        //$ctmodel->id=NULL;
                        $ctmodel->total_count = $totalcount;
                        $ctmodel->campaign_id = $model->id;
                        $ctmodel->keyword = serialize($keywords);
                        $ctmodel->targeturl = serialize($target_urls);
                        $ctmodel->save();
                    }
                } else {
                    return $rtn = $this->raiseErrorByCode(418, $e);
                }

                // Commit the transaction
                $transaction->commit();

                return $rtn = array('success' => true,
                             'message' => "Record(s) Created", 'code'=>200,
                             'data'    => array('totalCount'=>count($rows) + 2, 'campaign'=>$rows));
            } catch (Exception $e) {
                // Was there an error? if there a error, rollback transaction
                //print_r($e);

                $rtn = $this->raiseErrorByCode(418, $e);
                $transaction->rollback();
                return $rtn;
            }//end transaction
		} else {
            return $rtn = $this->raiseErrorByCode(418);
        }
    }

    public function getCampaignDetails($id = null){
        if (!is_numeric($_GET['id'])) {//it is int type
            return $rtn = $this->raiseErrorByCode(406);
        }

        /*
        $select = array('iostatus','progressstatus','tierlevel','anchortext',
                        'targeturl','desired_domain','rewritten_title','livedate');

        $criteria=new CDbCriteria();
        //$criteria->select='iostatus,progressstatus,tierlevel,anchortext,targeturl,desired_domain,rewritten_title,livedate';
        $criteria->select=$select;
        $criteria->condition='campaign_id=:campaign_id';
        $criteria->params=array(':campaign_id'=>$_GET['id']);
        $criteria->order='id ASC';
        $model = Task::model()->find($criteria); // $params is not needed
        if (!$model) {
            return $rtn = $this->raiseErrorByCode(404);
        }

        $rs = array();
        foreach($model as $mdl) {
            //$rs[] = (array)$mdl->attributes;
            if(!array_key_exists('MorrayBehavior', $mdl->behaviors())) {
                $mdl->attachBehavior('MorrayBehavior', new MorrayBehavior());
                $rs[] = (array)$mdl->toArray();
            }
        }
        */

        $rs = Yii::app()->db->createCommand()
            ->select('iostatus,progressstatus,tierlevel,anchortext,targeturl,desired_domain,rewritten_title,sourceurl,livedate')
            ->from('{{inventory_building_task}}')
            ->where('(campaign_id = :campaign_id)', array(':campaign_id'=>$_GET['id']))
            ->queryAll();

        if (!$rs) {
            return $rtn = $this->raiseErrorByCode(404);
        }

        $iostatuses = Task::$iostatuses;
        $pgstatus = Task::$pgstatus;
        $tiers = CampaignTask::$tier;
        foreach($rs as $k => $r){
            $rs[$k]["iostatus"] = $iostatuses[$r["iostatus"]];
            $rs[$k]["progressstatus"] = $pgstatus[$r["progressstatus"]];
            $rs[$k]["tierlevel"] = $tiers[$r["tierlevel"]];
            $rs[$k]["title"] = $r["rewritten_title"];
            unset($rs[$k]["rewritten_title"]);
        }
        return $rtn = array('success' => true,
                         'message' => "Record Retrieved Successfully",'code'=>200,
                         'data'    => array('totalCount'=>count($rs), 'processing'=>$rs));
    }

    public function getDomainProfile($id = null){
        $columns = array('pr' => "googlepr",'mr'=>"mozrank",'ar'=>"alexarank");
        if (is_numeric($id)) {//it is int type
            $exports = implode(",", array_values($columns));
            $where = "(domain_id = :reqid)";
            $crlexports = "s".implode(",s", array_values($columns));
        } else {
            list($id, $clm) = explode("/", $id);
            if (is_numeric($id)) {
                $where = "(domain_id = :reqid)";
            } else {
                $where = "(domain = :reqid)";
            }
            if (empty($clm)) {
                $exports = implode(",", array_values($columns));
                $_cols = $columns;
                $crlexports = "s".implode(",s", array_values($columns));
            } else {
                $clms = explode(",", $clm);
                foreach($clms as $c) {
                    $_cols[$c] = $columns[$c];
                }
                $exports = implode(",", array_values($_cols));
                $crlexports = "s".implode(",s", array_values($_cols));
            }

            if (!is_numeric($id)) {
                //Search The domain to see if in the inventory or not, if not, save it into inventory
                $dmodel = new Domain;
                $_dmodel = $dmodel->findByAttributes(array('domain' => $id));
                if (empty($_dmodel)) {
                    Yii::import('application.vendors.*');

                    $dmodel->setIsNewRecord(true);
                    $dmodel->id = NULL;
                    $dmodel->domain = $id;
                    $dmodel->touched_status = 1;
                    $crsattrs = array();
                    $now = time();
                    foreach ($_cols as $v) {
                        $func = "s".$v;
                        $_crs = CrawlerUtils::$func($id);
                        $dmodel->$v = $_crs[$v];
                        //CrawlerUtils::sgooglepr($id);
                        $crsattrs["$func"] = $now;
                    }
                    if ($dmodel->save()){
                        //sleep(2);
                        $crlmodel = Crawler::model()->findByAttributes(array('domain_id' => $dmodel->id));
                        if ($crlmodel) {
                            $crlmodel->attributes = $crsattrs;
                            $crlmodel->save();
                        }
                    }
                }
            }
        }

        $exports = "domain_id,domain," . $exports;

        $rs = Yii::app()->db->createCommand()->select("$exports")
            ->from('{{domain_summary}}')
            ->where($where, array(':reqid'=>$id))
            ->queryAll();

        if (!$rs) {
            return $rtn = $this->raiseErrorByCode(404);
        }

        $crlrs = Yii::app()->db->createCommand()->select("$crlexports")
            ->from('{{domain_crawler}}')
            ->where($where, array(':reqid'=>$id))
            ->queryAll();
        //print_r($rs);
        $rs[0] += $crlrs[0];
        //$rs[0] = array_merge($rs[0], $crlrs[0]);

        return $rtn = array('success' => true,
                         'message' => "Record Retrieved Successfully",'code'=>200,
                         'data'    => array('totalCount'=>count($rs), 'domain'=>$rs));
    }

    protected function raiseErrorByCode($code = 400, $errors = array()){
        $desc = $this->codes[$code] .".";
        if (!empty($errors)) {
            foreach($errors as $ers){
                foreach($ers as $err){
                    if($err != '')
                        $desc .= " ".$err;
                }
            }
        }

        return $rtn = array('success' => false,
                            'message' => $desc, 'code'=>$code,
                            'data'    => array('errorCode'=>$code));
    }

}