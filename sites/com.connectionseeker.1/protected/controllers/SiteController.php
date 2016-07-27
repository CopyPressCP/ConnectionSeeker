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
		$this->render('dashboard');
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

                $roles = Yii::app()->authManager->getRoles(Yii::app()->user->id);
                if(isset($roles['Marketer'])){
                    $cmodel = Client::model()->findByAttributes(array('user_id'=>Yii::app()->user->id));
                    if ($cmodel) {
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