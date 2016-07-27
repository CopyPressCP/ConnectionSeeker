<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
        $this->layout = "site";
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('index');
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionDashboard()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
        if (isset($_GET['pageSize'])) {
            Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
            unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
        }

        if (!isset($_GET['Announcement_sort'])) $_GET['Announcement_sort'] = "addeddate.desc";

		$model=new Announcement('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Announcement']))
			$model->attributes=$_GET['Announcement'];

        //dispath to different view.
        $cuid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($cuid);
        $currrole = key($roles);
        $model->roles = $currrole;

		$this->render('/announcement/list',array(
			'model'=>$model,
		));
        
		//$this->render('dashboard');
	}

	public function actionDashboard2()
	{
        $cuid = Yii::app()->user->id;
        if (!$cuid) {
            $this->redirect(array("site/login"));
            exit;
        }

        if (isset($_GET['pageSize'])) {
            Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
            unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
        }

        if (!isset($_GET['Email_sort'])) {
            $_GET['Email_sort'] = "send_time.desc";
        }

		$model=new Email('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Email']))
			$model->attributes=$_GET['Email'];
        $model->created_by = $cuid;
        if (!isset($_GET['Email']['domain_id'])) $model->domain_id = ">0";
        if (!isset($_GET['Email']['template_id'])) $model->template_id = ">0";
        $model->is_reply = "0";

        $this->render('/email/dashboard',array(
            'model'=>$model,
            'cuid'=>$cuid,
            //'roles'=>$roles,
        ));
	}

	public function actionAbout()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('about');
	}

	public function actionPrivacy()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('privacy');
	}

	public function actionHelp()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('help');
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
        $this->layout = "site";

        $model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$headers="From: {$model->email}\r\nReply-To: {$model->email}";
				mail(Yii::app()->params['adminEmail'],$model->subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the contact page
	 */
	public function actionPublisher()
	{
        $this->layout = "site";

        $model=new Client;
		$domodel=new ClientDomain;

		if(isset($_POST['SignupForm']))
		{
            //we need build one SignupForm for publisher & marketer
		}

		$this->render('publisher',array(
			'model'=>$model,
			'domodel'=>$domodel,
		));
	}

	public function actionMarketer()
	{
        $this->layout = "site";

        $model=new Client;
		$domodel=new ClientDomain;

		if(isset($_POST['Client'])){
            $model->attributes=$_POST['Client'];
        }
		if(isset($_POST['ClientDomain'])){
            $domodel->attributes=$_POST['ClientDomain'];
        }
        print_r($_POST);

		$this->render('publisher',array(
			'model'=>$model,
			'domodel'=>$domodel,
		));
	}


	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
        $this->layout = "login";

		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login()) {

                //Trail of the Login
                $log = new Trail;
                $log->description = 'User ' . Yii::app()->user->Name . ' login at '. date("M/d/Y H:i:s");;
                $log->operation   = 'LOGIN';
                $log->action      = 'login';
                $log->model       = 'LoginForm';
                $log->model_id    = 0;
                $log->field       = '';
                $log->created     = new CDbExpression('NOW()');
                $log->user_id     = Yii::app()->user->id;
                $log->save();

                $roles = Yii::app()->authManager->getRoles(Yii::app()->user->id);
                //if ($roles) Yii::app()->user->setState('roles', array_keys($roles));
                //put the roles into session. then you guys can use the following way:
                //if (in_array('Admin', Yii::app()->user->roles)) {}
                if(isset($roles['Marketer'])){
                    //$cmodel = Client::model()->findByAttributes(array('user_id'=>Yii::app()->user->id));
                    $umodel = User::model()->findByPk(Yii::app()->user->id);
                    if (!empty($umodel->client_id)) {
                        //nothing to do for now
                    } else {
                        $this->redirect(array('client/create'));
                    }
                }

                //$this->redirect(Yii::app()->user->returnUrl);
                $rtnurl = parse_url(Yii::app()->user->returnUrl);
                //$rtnurl = parse_url("/sites/com.connectionseeker/index.php?r=site/index");
                if (isset($rtnurl['query'])) {
                    parse_str($rtnurl['query'], $__rurl);
                    if (isset($__rurl['r']) && strtolower($__rurl['r']) == 'site/index') {
                        $this->redirect(array('site/dashboard'));
                    }
                    $this->redirect(Yii::app()->user->returnUrl);
                } else {
                    $this->redirect(array('site/dashboard'));
                }
                //print_r($rtnurl);
                //die();
            }
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();

        //Trail of the Login
        $log = new Trail;
        $log->description = 'User ' . Yii::app()->user->Name . ' logout at '. date("M/d/Y H:i:s");
        $log->operation   = 'LOGOUT';
        $log->action      = 'logout';
        $log->model       = 'LoginForm';
        $log->model_id    = 0;
        $log->field       = '';
        $log->created     = new CDbExpression('NOW()');
        $log->user_id     = Yii::app()->user->id;
        $log->save();

		//$this->redirect(Yii::app()->homeUrl);
		$this->redirect(array("site/login"));
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='site-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}