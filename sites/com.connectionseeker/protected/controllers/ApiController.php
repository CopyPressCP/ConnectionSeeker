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

    //GET The User object instant;
    protected $umodel = null;

    public $roles = null;

    //If you set the _debug as true, then the api will not authorize. when you lunch it as production, then set it to false.
    private $_debug = false;

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

    public function init(){

        $rtn = array();
        Yii::app()->clientScript->reset(); //Remove any scripts registered by Controller Class
        //Yii::app()->onException = array($this, 'onException'); //Register Custom Exception

        //print_r($_SERVER);
        //die();
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

                //9bb7ce9f63f8366dc5bd9bfb231d8a67----ConnectionSeeker.516e8dec8a1da7.19536168
                $this->umodel = User::model()->findByAttributes(array('apikey' => $apikey));
                if (!$this->umodel) {
                    $rtn = $this->raiseErrorByCode(406);
                } else {
                    //We will get secretkey from DB via search $apikey in the furture;
                    $secretkey = $this->umodel->secretkey;

                    $_signature = hash_hmac('sha1', $apikey."\n".$expires, $secretkey, true);
                    $_safesignature = urlencode(base64_encode($_signature));

                    if($signature != $_safesignature) {
                        // Error: Unauthorized
                        $rtn = $this->raiseErrorByCode(401);
                    } else {
                        $cuid = $this->umodel->id;
                        $this->roles = Yii::app()->authManager->getRoles($cuid);
                        Yii::app()->user->id = $this->umodel->id;
                    }
                    //filter AccessOwn HERE!!
                }
            }
        }

        if ($rtn) $this->returnResult($rtn);

        return parent::init();
    }

    /*
    public function beforeAction($event)
    {
        //echo $_GET['email'];
        //exit;
        return parent::beforeAction($event);
    }
    */

    public function filters() {
        if (!$this->_debug) {
            //$restFilters = array('restAccessRules+ list view create update delete client');
            /*
            $restFilters = array('restAccessRules+ list view create update delete client',
                                 'AccessOwn+       list view create update delete client',);
                                 */
            $restFilters = array(
                               //'restAccessRules+ list view create update delete client',
                               array('application.filters.api.CampaignFilter + list cmview view create update delete client',
                                     'umodel' => $this->umodel,
                           ),);

            if(method_exists($this, '_filters'))
                return CMap::mergeArray($restFilters, $this->_filters());
            else
                return $restFilters;
        }
    }

    public function actionView($id = null)
    {
        $cname = $_GET['model'];
        $cname = ucfirst($cname);

        if (isset($this->roles['Marketer']) && $cname == "Client") {
            if (isset($_GET["id"]) && is_numeric($_GET["id"]) && $_GET["id"] != $this->umodel->client_id) {
                //echo $this->umodel->client_id;
                $rtn = $this->raiseErrorByCode(406);
                $this->returnResult($rtn);
            }
            if (isset($_GET["id"]) && !is_numeric($_GET["id"])) {
                //do nothing for now; when we call api via email, 
                //such as: http://api.connectionseeker.com/client/leo@infinitenine.com
            } else {
                $_GET["id"] = $this->umodel->client_id;
            }
        }

        if(!(isset($this->roles['Marketer']) || isset($this->roles['Admin'])) && !isset($_GET['id'])) {
            $rtn = $this->raiseErrorByCode(500);
        } else {
            switch($cname) {
                // Find respective model    
                case 'User':
                case 'Client':
                case 'Campaign':
                //case 'Domain':
                    $model = new $cname;
                    if (is_numeric($_GET['id'])) {//it is int type
                        if ($_GET['id'] > 0) {
                            $model = $model->findByPk($_GET['id']);
                            if (isset($this->roles['Marketer']) && $cname == 'Campaign' 
                             && $model && $model->client_id != $this->umodel->client_id) {
                                //echo $model->client_id;
                                //echo $this->umodel->client_id;
                                $rtn = $this->raiseErrorByCode(406);
                            }
                        } else {
                            //echo "Do Nothing For Now.";
                            //die();
                        }
                    } elseif (filter_var($id, FILTER_VALIDATE_EMAIL) !== false) {//id is email
                        $model = $model->findByAttributes(array('email'=>$_GET['id']));
                        if (isset($this->roles['Marketer']) && $cname == "Client" 
                         && $model && $model->id != $this->umodel->client_id) {
                            $rtn = $this->raiseErrorByCode(406, array('Invalid Email.'));
                        }
                    } else {
                        $model = null;
                    }
                    break;
                case 'Existaccount':
                    if ($id) {
                        $acctinfo = explode("/", $id);
                        $cnt = count($acctinfo);
                        if ($cnt <= 2) {
                            $cname{0} = strtolower($cname{0});
                            $model = User::model()->findByAttributes(array('username'=>$acctinfo[0]));
                            if ($model) {
                                $rtn = array('success' => true,
                                             'message' => "Record Retrieved Successfully",'code'=>200,
                                             'data'    => array('totalCount'=>1, $cname=>array('user_id'=>$model->id,
                                                                              'client_id'=>$model->client_id,
                                                                              'token'=>$model->password.$model->salt)));
                            } else {
                                $rtn = $this->raiseErrorByCode(404);
                            }
                        } else {
                            $rtn = $this->raiseErrorByCode(400);
                        }
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
                    $rsarr = $model->toArray();
                    if ($cname == "campaign") {
                        /*
                        $count = Task::model()->count("campaign_id=:cid",array(":cid"=>$id));
                        $rsarr["total_ordered"] = "{$count}";
                        */
                        //####$cmpinfomdl = CampaignTask::model()->find("campaign_id=:cid",array(":cid"=>$id));
                        $cmpinfomdl = CampaignTask::model()->findByAttributes(array('campaign_id'=>$id));
                        if ($cmpinfomdl) {
                            $rsarr["total_ordered"] = $cmpinfomdl->total_count;
                            $rsarr["total_live"] = $cmpinfomdl->published_count;
                        }
                    }

                    $rtn = array('success' => true,
                                 'message' => "Record Retrieved Successfully",'code'=>200,
                                 'data'    => array('totalCount'=>1, $cname=>$rsarr));
                }
            }
        }

        if ($rtn) $this->returnResult($rtn);
        Yii::app()->end();
    }

    public function actionCreate()
    {
        $cname = $_GET['model'];
        $cname = ucfirst($cname);

        switch(strtolower($cname)) {
            // Get an instance of the respective model
            case 'domain':
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

        //if (!isset($rtn) && !empty($rtn)) {
        if (!isset($rtn)) {
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
                             'message' => "Record(s) Created Successfully", 'code'=>200,
                             'data'    => array('totalCount'=>1, $cname=>$model->toArray()));
            } else {
                $ers = $model->getErrors();
                $rtn = $this->raiseErrorByCode(500, $ers);
            }
        }

        if ($rtn) $this->returnResult($rtn);
        Yii::app()->end();
    }

    public function actionUpdate($id)
    {
        $cname = $_GET['model'];
        $cname = ucfirst($cname);
        //##$model = new $cname;

        switch(strtolower($cname)) {
            // Get an instance of the respective model
            case 'task':
            case 'client':
                $model = new $cname;
                break;
            default:
                $rtn = $this->raiseErrorByCode(501);
                $this->returnResult($rtn);
                break;
        }

        $model=$model->findByPk($id);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        $params = $_POST;
        if (empty($_POST)) {
            $jsonpost = file_get_contents('php://input');
            $params = CJSON::decode($jsonpost,true);
        }

        if (isset($this->roles['Marketer']) && strtolower($cname) == "task") {
            if (isset($params["iostatus"])) {
                //##if(!in_array((int)$model->iostatus, array(1,21)) || !in_array((int)$params["iostatus"], array(1,3,4))) {
                if((int)$model->iostatus != 21 || !in_array((int)$params["iostatus"], array(1,3))) {
                    $rtn = $this->raiseErrorByCode(406, array("Over Permission"));
                    $this->returnResult($rtn);
                }
                if ((int)$params["iostatus"] == 1) {//means deny by marketer
                    $params["desired_domain"] = null;
                    $params["desired_domain_id"] = 0;
                }
            }
        }

        // Try to assign POST values to attributes
        foreach($params as $var=>$value) {
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
            $rows = $model->toArray();
            if (isset($this->roles['Marketer']) && strtolower($cname) == "task") {
                $columns = Utils::taskDisplayMode(6);
                foreach($rows as $k => $v) {
                    if (!in_array($k, $columns) && !in_array($k, array("id","iostatus"))) unset($rows[$k]);
                }
            }
            $rtn = array('success' => true,
                         'message' => "Record(s) Updated Successfully", 'code'=>200,
                         'data'    => array('totalCount'=>1, $cname=>$rows));
        } else {
            $ers = $model->getErrors();
            $rtn = $this->raiseErrorByCode(500, $ers);
        }


        if ($rtn) $this->returnResult($rtn);
        Yii::app()->end();
    }

    public function actionList()
    {
        $cname = $_GET['model'];
        $cname = ucfirst($cname);

        // Get the respective model instance
        switch(strtolower($cname)) {
            // Get an instance of the respective model
            case 'userxxxx':
            //case 'User':
            //case 'Client'://Donot turn client list on, it is dangerous
                $model = new $cname;
                $model = $model->findAll();
                break;
            case 'clientdomain':
            case 'campaign':
                if (strtolower($cname) == "clientdomain") $cname = "ClientDomain";
                $model = new $cname;

                if (isset($this->roles['Marketer'])) {
                    $condition = array('condition'=>'client_id=:cid','params'=>array(':cid'=>$this->umodel->client_id),);
                } else {
                    //echo file_get_contents("php://input");
                    if (isset($_GET['id'])) {
                        $id = $_GET['id'];
                        if (is_numeric($id)) {
                            //do nothing for now;
                            $condition = array('condition'=>'client_id=:cid','params'=>array(':cid'=>$id),);
                        } else {
                            list($cid, $id) = explode("/", $id);
                            if (strtolower($cid) == 'cid') 
                                $condition = array('condition'=>'client_id=:cid','params'=>array(':cid'=>$id),);
                        }
                    }
                }
                $model = $model->findAll($condition);
                break;
            case 'cscategory':
            /*
            case 'cstierlevel':
            case 'cssite':
            case 'csoutreach':
            case 'cslinktask':
            case 'cschannel':
            */
                $rtn = $this->getTypesByType($cname);
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
                        if (strtolower($cname) == "campaign") {
                            $rs[] = (array)$mdl->toArray() + array('total_count'=>$mdl->rcampaigntask->total_count,
                                                                   'published_count'=>$mdl->rcampaigntask->published_count,
                                                                   'percentage_done'=>$mdl->rcampaigntask->internal_done);
                                                                   //'percentage_done'=>$mdl->rcampaigntask->percentage_done);
                        } else {
                            $rs[] = (array)$mdl->toArray();
                        }
                    }
                }

                $cname{0} = strtolower($cname{0});
                $rtn = array('success' => true,
                             'message' => "Record(s) Received Successfully", 'code'=>200,
                             'data'    => array('totalCount'=>count($rs), $cname=>$rs));

            }
        }

        if ($rtn) $this->returnResult($rtn);
        Yii::app()->end();
    }

    /*
    * This one is a customize actionView, here the id isn't int type, it is string type(email)
    */
	public function actionCmview($id=null)
    {
        if (strtolower($_GET['model']) == 'clientdomain') {
            $this->actionList();
        } elseif (strtolower($_GET['model']) == 'ioreporting') {
            $this->getIOReporting($id);
        } elseif (strtolower($_GET['model']) == 'automationreporting') {
            $this->getAutomationReporting($id);
        } else {
            $this->actionView($id);
        }
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

        if ($rtn) $this->returnResult($rtn);
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

        if ($rtn) $this->returnResult($rtn);
        Yii::app()->end();
    }

    //Add related domains into client, add competitors to client domain
    public function createClientDomains(){
        $_modelname = strtolower($_GET['model']);

        $cname = "ClientDomain";
        if ($_modelname == 'competitor') $cname = "Competitor";

        //if (strtolower($cname) == "clientdomain") {
            if (isset($this->roles['Marketer'])) {
                $client_id = $this->umodel->client_id;
            } elseif (isset($this->roles['Admin'])) {//we need leave an interface for super admin
                //do nothing for now;
            } else {
                return $rtn = $this->raiseErrorByCode(405, array('Please contact admin.'));
            }
        //}

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
                Yii::import('application.vendors.*');

                $rows = array();
                $transaction = Yii::app()->db->beginTransaction();
                try {
                    $cpts = array();
                    $i = 0;
                    foreach ($data['domains'] as $k => $v) {
                        if (!empty($v)) {
                            $cpts[$i]["domain_id"] = $v["domain_id"];
                            if ($_modelname == 'competitor') {
                                $ccmodel = ClientDomain::model()->findByPk($v["domain_id"]);
                                if (!$ccmodel || (isset($this->roles['Marketer'])&& $client_id != $ccmodel->client_id)) {
                                    //return $rtn = $this->raiseErrorByCode(405, array("Invalid Domain ID #".$v["domain_id"]));
                                    throw new Exception("Invalid Domain ID #".$v["domain_id"]);
                                }

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
                                    if ($var == 'domain' && !empty($value)) $value = SeoUtils::getSubDomain($value);
                                    $model->$var = $value;
                                }

                                if (strtolower($cname) == "clientdomain" && $var == "client_id" && isset($client_id)) {
                                    if ($client_id > 0) $model->client_id = $client_id;
                                }
                            }

                            if (strtolower($cname) == "clientdomain" && empty($model->client_id)) {
                                if (isset($this->roles['Marketer'])) {
                                    $model->client_id = $client_id;
                                    if (empty($client_id)) throw new Exception("Invalid Client ID #".$client_id);
                                } else {
                                    throw new Exception("Invalid Client ID #".$client_id);
                                }
                                /*
                                $rtn = $this->raiseErrorByCode(406, array('Please provide a valid client ID.'));
                                $this->returnResult($rtn);
                                exit;
                                */
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
                    $rtn = $this->raiseErrorByCode(418, $e->getMessage());
                    $transaction->rollback();
                }//end transaction
            }

        }

        if ($rtn) $this->returnResult($rtn);
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
            $newtaskcount = 0;//for how many tasks were created by client 

            if (isset($this->roles['Marketer'])) {
                $model->client_id = $this->umodel->client_id;
            } elseif (isset($this->roles['Admin'])) {
                if (empty($model->client_id)) {
                    return $rtn = $this->raiseErrorByCode(406, array('Please provide a valid client ID.'));
                }
            } else {
                return $rtn = $this->raiseErrorByCode(405, array('Please contact admin.'));
            }

            if (isset($data['Campaign']['client_domain_id']) && is_numeric($data['Campaign']['client_domain_id'])) {
                $client_domain_id = (int)$data['Campaign']['client_domain_id'];
                $cdmodel = new ClientDomain;
                $cdi = $cdmodel->findByPk($client_domain_id);
                if (!empty($cdi)) {
                    $model->domain = $cdi->domain;
                    $model->domain_id = $cdi->domain_id;
                } else {
                    return $rtn = $this->raiseErrorByCode(412, array('Unauthorized Client Domain ID.'));
                }
            } elseif (isset($data['Campaign']['domain_id']) && is_numeric($data['Campaign']['domain_id'])) {
                $domain_id = (int)$data['Campaign']['domain_id'];
                $cdmodel = new ClientDomain;
                $cdi = $cdmodel->findByAttributes(array('domain_id' => $domain_id,'client_id'=>$model->client_id));
                if (!empty($cdi)) {
                    $model->domain = $cdi->domain;
                    $model->domain_id = $cdi->domain_id;
                } else {
                    return $rtn = $this->raiseErrorByCode(412, array('Unauthorized Domain ID.'));
                }
            } elseif (isset($data['Campaign']['domain'])) {
                $domain = trim($data['Campaign']['domain']);
                if ($domain) {
                    Yii::import('application.vendors.*');
                    $domain = SeoUtils::getSubDomain($domain);
                }
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
            $notemodel=new TaskNote;

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
                        $tasknotes = $data['CampaignTask']['tasknote'];
                        $others = $data['CampaignTask']['other'];
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
                                $keywords[$i]['tasknote'] = trim($tasknotes[$k]);
                                $keywords[$i]['other'] = trim($others[$k]);
                                $keywords[$i]['duedate'] = $model->duedate;

                                $taskids = array();
                                for ($j = 0; $j < $kwcount[$k]; $j++) {
                                    $taskmodel->setIsNewRecord(true);
                                    $taskmodel->id = NULL;
                                    $taskmodel->campaign_id = $model->id;
                                    $taskmodel->duedate = $model->duedate;
                                    $taskmodel->anchortext  = trim($anchortext[$k]);
                                    $taskmodel->targeturl   = trim($_urls[$k]);
                                    $taskmodel->tierlevel   = $tiers[$k];
                                    $taskmodel->other = trim($others[$k]);

                                    if ($taskmodel->save()) {
                                        $newtaskcount++;
                                        $rows["CampaignTask"][] = $taskmodel->attributes;

                                        //$taskids[$taskmodel->id] = $taskmodel->id;
                                        $taskids[] = $taskmodel->id;

                                        $tknote = trim($tasknotes[$k]);
                                        if (!empty($tknote)) {
                                            $notemodel->setIsNewRecord(true);
                                            $notemodel->id = NULL;
                                            $notemodel->task_id = $taskmodel->id;
                                            $notemodel->notes = $tknote;
                                            $notemodel->save();
                                        }
                                    }
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
                    return $rtn = $this->raiseErrorByCode(418, array('Campaign Saved Failure.'));
                }

                // Commit the transaction
                $transaction->commit();

                //Send notice email to the csclientissues@copypress.com
                $prclink = "http://dev.connectionseeker.com/index.php?r=task/processing&campaign_id=".$model->id;
                //$rowcnt = count($rows["CampaignTask"]); //it is the same as $newtaskcount;
                $clientname = $this->umodel->username;
                $np["subject"] = "New Campaign Created by {$clientname}";
                $np["tos"]     = "csclientissues@copypress.com";
                $np["content"] = "{$clientname} has just created a new campaign in Connection Seeker for $newtaskcount tasks. Click Here(<a href='$prclink' target='_blank'>Link</a>) to view the campaign.";
                Utils::notice($np);

                return $rtn = array('success' => true,
                             'message' => "Record(s) Created", 'code'=>200,
                             'data'    => array('totalCount'=>$newtaskcount + 2, 'campaign'=>$rows));
            } catch (Exception $e) {
                // Was there an error? if there a error, rollback transaction
                //print_r($e);

                $rtn = $this->raiseErrorByCode(418, $e);
                $transaction->rollback();
                return $rtn;
            }//end transaction
		} else {
            return $rtn = $this->raiseErrorByCode(418, array('Please Post The Campaign Data.'));
        }
    }

    public function getCampaignDetails($id = null){
        if (!is_numeric($_GET['id'])) {//it is int type
            return $rtn = $this->raiseErrorByCode(406);
        }

        /*
        $select = array('iostatus','tierlevel','anchortext',
                        'targeturl','desired_domain','rewritten_title','livedate');

        $criteria=new CDbCriteria();
        //$criteria->select='iostatus,tierlevel,anchortext,targeturl,desired_domain,rewritten_title,livedate';
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
            ->select('id,iostatus,tierlevel,anchortext,targeturl,desired_domain,other,rewritten_title,sourceurl,livedate')
            ->from('{{inventory_building_task}}')
            ->where('(campaign_id = :campaign_id)', array(':campaign_id'=>$_GET['id']))
            ->queryAll();

        if (!$rs) {
            return $rtn = $this->raiseErrorByCode(404);
        }

        $iostatuses = Task::$iostatuses;
        $tiers = CampaignTask::$tier;
        foreach($rs as $k => $r){
            $rs[$k]["iostatus"] = $iostatuses[$r["iostatus"]];
            $rs[$k]["tierlevel"] = $tiers[$r["tierlevel"]];
            $rs[$k]["title"] = $r["rewritten_title"];
            $qnote = Yii::app()->db->createCommand()->select('notes')->from('{{task_note}}')
                 ->where('(task_id = :tid)', array(':tid'=>$r["id"]))->queryAll();
            $rs[$k]["tasknote"] = $qnote;
            unset($rs[$k]["rewritten_title"]);
        }
        return $rtn = array('success' => true,
                         'message' => "Record(s) Retrieved Successfully",'code'=>200,
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

    public function getTypesByType($id){
        $id = substr($id, 2);
        $id = strtolower($id);
        //if (stripos("/", $id) === false) { }
        $types = Types::model()->actived()->bytype($id)->findAll();
        $rs = CHtml::listData($types, 'refid', 'typename', 'type');

        return $rtn = array('success' => true,
                         'message' => "Record(s) Retrieved Successfully",'code'=>200,
                         'data'    => array('totalCount'=>count($rs[$id]), $id => $rs[$id]));
    }

    public function getIOReporting($id){
        if (isset($this->roles["Admin"])) {
            list($filterid, $fid, $day) = explode("/", $id);
            $filterid = strtolower($filterid);
            if (!isset($day)) $day = date("Y-m-d", time()-86400);//yesterday
            if ($fid && !is_numeric($fid) && strtolower($fid) == "all") $fid = 0;
            if (!in_array($filterid, array('user','campaign','channel')) || !is_numeric($fid)){
                $rtn = $this->raiseErrorByCode(406, array('Wrong request format.'));
                $this->returnResult($rtn);
            }
            $dayts = strtotime($day);
            $day = date("Y-m-d", $dayts);

            $qs = "COUNT( if( (t.oldiostatus='2' AND t.iostatus='21'), true, null) ) AS pending,
                   COUNT( if( (t.oldiostatus='21' AND t.iostatus='1' AND t.role='Admin'), true, null) ) AS denybyadmin,
                   COUNT( if( (t.oldiostatus='21' AND t.iostatus='1' AND t.role='Marketer'), true, null) ) AS denybyclient,
                   COUNT( if(t.iostatus='3',true,null) ) AS approved,
                   COUNT( if(t.iostatus='5',true,null) ) AS completed,
                   COUNT( if( (t.iostatus='5' AND ibt.rebuild='1'), true, null) ) AS rebuilt,
                   COUNT( if(t.iostatus='32',true,null) ) AS inrepair,
                   COUNT( if(t.iostatus='31',true,null) ) AS preqa, '$day' AS date";

            $tday = "%".$day."%";

            //$fid == 0 means get all
            if ($fid == 0) {
                if ($filterid == 'campaign') {
                    $qs .= ", ibt.campaign_id";
                    $_group = "ibt.campaign_id";
                } else {
                    $qs .= ", ibt.channel_id, u.id AS user_id";
                    $_group = "ibt.channel_id";
                }
                $rs = Yii::app()->db->createCommand()->select($qs)->from('{{io_history}} t')
                    ->join('{{inventory_building_task}} ibt', 't.task_id = ibt.id')
                    ->join('{{user}} u', 'ibt.channel_id = u.channel_id')
                    ->where("t.created LIKE :tday", array(":tday"=>$tday))->group($_group)->queryAll();
                $total = $rs ? count($rs) : 0;
                if ($total) {
                    //###START GET THE newcompleted amount; 3/5/2014###//
                    foreach($rs as $_rk => $_rv) {
                        $rs[$_rk]["newcompleted"] = 0;
                        if ($_rv["completed"]>0) {
                            if ($filterid == 'campaign') {
                                $where = "ibt.campaign_id=:filterid";
                                $fid = $_rv["campaign_id"];
                            } else {
                                $where = "ibt.channel_id=:filterid";
                                $fid = $_rv["channel_id"];
                            }
                            $newdesired = Yii::app()->db->createCommand()->select("ibt.desired_domain_id,ibt.id")
                                ->from('{{io_history}} t')->join('{{inventory_building_task}} ibt', 't.task_id = ibt.id')
                                ->where($where." AND t.created LIKE :tday AND ibt.iostatus=5 AND ibt.desired_domain_id>0",
                                    array(":filterid"=>$fid,":tday"=>$tday))->group("ibt.desired_domain_id")->queryAll();
                            //print_r($newdesired);
                            if ($newdesired) {
                                $where = "t.created < '$day 00:00:00' AND t.iostatus=5";
                                foreach($newdesired as $ndi) {
                                    $nc = Yii::app()->db->createCommand()->select("ibt.id,t.created")->from('{{io_history}} t')
                                        ->join('{{inventory_building_task}} ibt', 't.task_id = ibt.id')
                                        ->where($where." AND ibt.desired_domain_id='".$ndi["desired_domain_id"]."'")
                                        ->queryRow();
                                    if (!$nc) {
                                        $rs[$_rk]["newcompleted"] += 1;
                                    }
                                }
                            }//end if $newdesired;

                            //####7/10/2014 Fixed the Complete NUMBER = (Complete - Rebuilt) ####//
                            if ($_rv["rebuilt"] > 0) $_rv["completed"] = $_rv["completed"] - $_rv["rebuilt"];
                        }
                    }
                    //###END GET THE newcompleted amount; 3/5/2014###//
                }
            } else {
                if ($filterid == 'user') {
                    $quser = User::model()->findByPk($fid);
                    if ($quser && !empty($quser->channel_id)) {
                        $fid = $quser->channel_id;
                        $where = "ibt.channel_id=:filterid AND t.created LIKE :tday";//for channel
                        $rs = Yii::app()->db->createCommand()->select($qs)->from('{{io_history}} t')
                            ->join('{{inventory_building_task}} ibt', 't.task_id = ibt.id')
                            ->where($where, array(":filterid"=>$fid,":tday"=>$tday))->queryRow();
                    } else {
                        $where = "t.created_by=:filterid AND t.created LIKE :tday";
                        $rs = Yii::app()->db->createCommand()->select($qs)->from('{{io_history}} t')
                            ->where($where, array(":filterid"=>$fid,":tday"=>$tday))->queryRow();
                    }
                } else {
                    if ($filterid == 'campaign') {
                        $where = "ibt.campaign_id=:filterid AND t.created LIKE :tday";
                    } else {
                        $where = "ibt.channel_id=:filterid AND t.created LIKE :tday";//for channel
                    }
                    $rs = Yii::app()->db->createCommand()->select($qs)->from('{{io_history}} t')
                        ->join('{{inventory_building_task}} ibt', 't.task_id = ibt.id')
                        ->where($where, array(":filterid"=>$fid,":tday"=>$tday))->queryRow();
                }
                $total = $rs ? 1 : 0;

                //###END GET THE newcompleted amount; 3/5/2014###//
                if ($rs) $rs["newcompleted"] = 0;
                if ($rs && $rs["completed"]) {
                    $newdesired = Yii::app()->db->createCommand()->select("ibt.desired_domain_id,ibt.id")
                        ->from('{{io_history}} t')->join('{{inventory_building_task}} ibt', 't.task_id = ibt.id')
                        ->where($where." AND t.iostatus=5 AND ibt.desired_domain_id>0",
                            array(":filterid"=>$fid,":tday"=>$tday))->group("ibt.desired_domain_id")->queryAll();
                    if ($newdesired) {
                        //print_r($newdesired);
                        $where = "t.created < '$day 00:00:00' AND t.iostatus=5";
                        foreach($newdesired as $ndi) {
                            $nc = Yii::app()->db->createCommand()->select("ibt.id,t.created")->from('{{io_history}} t')
                                ->join('{{inventory_building_task}} ibt', 't.task_id = ibt.id')
                                ->where($where." AND ibt.desired_domain_id='".$ndi["desired_domain_id"]."'")
                                ->queryRow();
                            if (!$nc) {
                                $rs["newcompleted"] += 1;
                            }
                        }
                    }

                    //####7/10/2014 Fixed the Complete NUMBER = (Complete - Rebuilt) ####//
                    if (isset($rs["rebuilt"]) && $rs["rebuilt"] > 0) $rs["completed"] = $rs["completed"] - $rs["rebuilt"];
                }
                //###END GET THE newcompleted amount; 3/5/2014###//
            }

            $rtn = array('success' => true,
                         'message' => "Record(s) Retrieved Successfully",'code'=>200,
                         'data'    => array('totalCount'=>$total, 'ioreporting' => $rs));
        } else {
            $rtn = $this->raiseErrorByCode(406, array('You have no permission to access this feature.'));
        }

        if ($rtn) $this->returnResult($rtn);
    }

    public function getAutomationReporting($id){
        if (isset($this->roles["Admin"])) {
            $day = strtotime($id);
            $nextday = $day + 86400;
            $day = date("Y-m-d", $day);
            $nextday = date("Y-m-d", $nextday);
            $tday = "%".$day."%";
            $qs = "SELECT COUNT(*) FROM {{automation_sent}} `t` WHERE type_of_automation='client_discovery_id'";
            $qs = "COUNT( if(t.sent LIKE '$tday', true, null) ) AS nofsent,
                   COUNT( if(t.opened_time >= '$day', true, null) ) AS nofopened,
                   COUNT( if(t.replied_time >= '$day',true,null) ) AS nofreplied, '$day' AS date";
                   /*
                   COUNT( if(t.opened_time LIKE '$tday', true, null) ) AS nofopened,
                   COUNT( if(t.replied_time LIKE '$tday',true,null) ) AS nofreplied, '$day' AS date";
                   */

            $rs = Yii::app()->db->createCommand()->select($qs)->from('{{automation_sent}} t')
                //->where("t.type_of_automation = 'client_discovery_id' AND t.sent>= :tday", array(":tday"=>$tday))->queryAll();
                ->where("t.type_of_automation = 'client_discovery_id' AND t.sent>='$day' AND t.sent<'$nextday'", array(":tday"=>$tday))->queryRow();
            //#print_r($rs);
            $total = $rs ? 1 : 0;
            $rtn = array('success' => true,
                         'message' => "Record(s) Retrieved Successfully",'code'=>200,
                         'data'    => array('totalCount'=>$total, 'automationreporting' => $rs));
        } else {
            $rtn = $this->raiseErrorByCode(406, array('You have no permission to access this feature.'));
        }
        if ($rtn) $this->returnResult($rtn);
    }

    public function raiseErrorByCode($code = 400, $errors = array()){
        //echo __line__;
        $desc = $this->codes[$code] .".";
        if (!empty($errors)) {
            if (is_string($errors)) $errors = array($errors); 
            foreach($errors as $ers){
                if (is_array($ers)) {
                    foreach($ers as $err){
                        if($err != '')
                            $desc .= " ".$err;
                    }
                } else {
                    $desc .= " ".$ers;
                }
            }
        }

        return $rtn = array('success' => false,
                            'message' => $desc, 'code'=>$code,
                            'data'    => array('errorCode'=>$code));
    }

    /*
    * Return Result
    */
    public function returnResult($rtn) {
        if ($rtn) {
            $this->apiLog($rtn);
            //@header("{$_SERVER['SERVER_PROTOCOL']} {$rtn['code']} {$rtn['message']}");
            @header("{$_SERVER['SERVER_PROTOCOL']} 200 {$rtn['message']}");
            @header('Content-type: application/json');
            echo CJSON::encode($rtn);
            exit;
        }
    }

    /*
    * Log all of the API calls history.
    */
    public function apiLog($rtn) {
        DEFINE('DS', DIRECTORY_SEPARATOR);

        //we can use the $_SERVER['REQUEST_TIME'];
        $logs = "\n\n============REQUEST TIME: ".date("Y-m-d H:i:s", time())."==============";
        $logs .= "\nAPIKEY: ".$_SERVER['HTTP_X_CSREST_APIKEY'];
        $logs .= "\nREQUEST URI: ".$_SERVER['REQUEST_URI'];
        $logs .= "\nREQUEST METHOD: ".$_SERVER['REQUEST_METHOD']." - RETURN CODE: ".$rtn['code'];

        $params = file_get_contents('php://input');
        if (!$params) {
            if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                $params = $_GET;
                $params = CJSON::encode($params);
            } else if($_SERVER['REQUEST_METHOD'] == 'POST') {
                $params = empty($_POST) ? $GLOBALS['HTTP_RAW_POST_DATA'] : $_POST;
                $params = CJSON::encode($params);
            }
        }
        $logs .= "\nREQUEST DATA: ".$params;
        //print_r($_SERVER);

        if ($rtn['code'] == 200) {
            $logs .= "\nRETURN DATA: ".$rtn['message'];
        } else {
            $logs .= "\nRETURN DATA: ".CJSON::encode($rtn);
        }

        $logfile = dirname(dirname(__FILE__)) . DS . "runtime" . DS . "apicalls.log";
        file_put_contents($logfile, $logs, FILE_APPEND | LOCK_EX);
    }
}