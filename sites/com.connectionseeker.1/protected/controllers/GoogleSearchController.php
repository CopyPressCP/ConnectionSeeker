<?php
Yii::import('application.vendors.*');
require_once('simplehtmldom/simple_html_dom.php');
require_once('Snoopy/Snoopy.class.php');
require_once('SeoUtils.php');
require_once('SEOstats/src/class.seostats.php');

class GoogleSearchController extends Controller
{
    /**
    * @var mixed integer: the number of seconds in which the cached forecast will expire; array: (the number of seconds in which the cached forecast will expire, cache dependency object); boolean false: no cacheing
    * @link http://www.yiiframework.com/doc/api/CCache
    * @link http://www.yiiframework.com/doc/api/CCacheDependency
    */
    public $cache = 7776000; // cache 90 days: 3600 sec * 24 hour * 90 days

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
        //how many result you wanna get from google? default is 30;
        $nser = 30;

        /*
        $keyword = trim(strtolower($_GET['GoogleSearch']['keyword']));
        SeoUtils::paserGoogleResults($keyword, $nser);
        SeoUtils::getGoogleResultsViaSnoopy($keyword, $nser);
        die();
        */
        if (isset($_GET['GoogleSearch'])) {
            $keyword = trim(strtolower($_GET['GoogleSearch']['keyword']));

            if (!empty($keyword)) {
                //$rs = SeoUtils::paserGoogleResults($keyword, $nser);
                //$config = Yii::app()->getComponents(false);
                //if ($this->cache !== false && isset($config['cache'])) {
                if ($this->cache !== false && is_object(Yii::app()->getComponent('cache'))) {
                    $rs = Yii::app()->cache->get("{$keyword}");
                    if (empty($rs)) {
                        //$rs = SeoUtils::paserGoogleResults($keyword, $nser);
                        $rs = SeoUtils::googleAjaxSER($keyword, $nser);
                        //$kwcompetitor = $rs['cpt_domain'];
                        if (is_integer($this->cache)) {
                            $this->cache = array($this->cache, null);
                        }
                        Yii::app()->cache->set("{$keyword}", $rs, $this->cache[0], $this->cache[1]);
                    } else {
                        //sync the cache data
                        //$dmarr = array();
                        //print_r($rs);
                        if (!empty($rs['cpt_domain'])) {
                            $dmarr = array_keys($rs['cpt_domain']);
                            if (!empty($dmarr)) {
                                //implode("", $dmarr);
                                $gsmodel = new GoogleSearch;
                                $criteria=new CDbCriteria;
                                $criteria->addInCondition('domain',$dmarr);
                                $criteria->select = 'domain,seostatus';
                                $gs = $gsmodel->findAll($criteria);
                                if ($gs) {
                                    foreach ($gs as $gsv) {
                                        $hubcount = $rs['cpt_domain'][$gsv->domain]['hubcount'];
                                        $rs['cpt_domain'][$gsv->domain] = unserialize($gsv->seostatus);
                                        $rs['cpt_domain'][$gsv->domain]['hubcount'] = $hubcount;
                                        $rs['cpt_domain'][$gsv->domain]['domain'] = $gsv->domain;
                                        //print_r($rs['cpt_domain'][$gsv->domain]);
                                    }
                                }
                            }
                        } else {
                            $rs = SeoUtils::googleAjaxSER($keyword, $nser);
                            if (is_integer($this->cache)) {
                                $this->cache = array($this->cache, null);
                            }
                            Yii::app()->cache->set("{$keyword}", $rs, $this->cache[0], $this->cache[1]);
                        }

                    }
                } else {
                    //$rs = SeoUtils::paserGoogleResults($keyword, $nser);
                    $rs = SeoUtils::googleAjaxSER($keyword, $nser);
                }
                //$kwcompetitor = $rs['cpt_domain'];
            }

            $rs['google_results'] = null;
            $rs['keyword'] = $keyword;
            //print_r($rs);
        }

        //echo CJSON::encode($kwcompetitor);
        echo CJSON::encode($rs);

        Yii::app()->end();

		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		//$this->render('index');

	}

	/**
	 * This is the action to handle external exceptions.
     * when we get a chance, we need improve this function/method
	 */
	public function actionStatus()
	{
        ini_set('max_execution_time', 180);

        $rs = array();
        if (isset($_GET['GoogleSearch'])) {
            $domain = trim(strtolower($_GET['GoogleSearch']['domain']));
            $keyword = trim(strtolower($_GET['GoogleSearch']['keyword']));

            /*
            //get the keyword competitors cache file
            $kwrs = array();
            if (!empty($keyword)) {
                if ($this->cache !== false && is_object(Yii::app()->getComponent('cache'))) {
                    $kwrs = Yii::app()->cache->get("{$keyword}");
                    if (!empty($kwrs)) {
                        $kwcompetitor = $kwrs['cpt_domain'];
                    }
                }
            }
            */

            if (!empty($domain)) {
                if (is_array($domain)) {
                    foreach ($domain as $dm) {
                        $_rs = $this->seoCompetitorCache($dm, $keyword);
                        if (!empty($_rs)) $rs += $_rs;
                    }
                } else {
                    $rs = $this->seoCompetitorCache($domain, $keyword);

                }
            } else {
                $kwrs = array();
                if (!empty($keyword)) {
                    if ($this->cache !== false && is_object(Yii::app()->getComponent('cache'))) {
                        $kwrs = Yii::app()->cache->get("{$keyword}");
                        if (!empty($kwrs) && !empty($kwrs['cpt_domain'])) {
                            foreach($kwrs['cpt_domain'] as $v) {
                                $_rs = $this->seoCompetitorCache($v['domain'], $keyword);
                                if (!empty($_rs)) {
                                    $rs += $_rs;
                                }
                            }
                        }
                    }
                }
            }

            //print_r($rs);
        }

        echo CJSON::encode($rs);

        Yii::app()->end();
	}

    private function seoCompetitorCache($domain, $keyword){

        if (empty($domain)) {
            return null;
        }

        if (stripos($domain, 'www.') === 0) $domain = str_ireplace("www.", "", $domain);

        
        $cptmodel=new Competitor;
        $di = $cptmodel->findByAttributes(array('domain' => $domain));

        $gsmodel=new GoogleSearch;
        $dgs = $gsmodel->findByAttributes(array('domain' => $domain));

        $exec = array();
        //get the search status. like PR/Alexa/MozRank/Age/
        $homepage = "http://www." . $domain;
        $rs = array();

        /*
        $kwrs = array();
        if (!empty($keyword)) {
            if ($this->cache !== false && is_object(Yii::app()->getComponent('cache'))) {
                $kwrs = Yii::app()->cache->get("{$keyword}");
                if (!empty($kwrs)) {
                    $kwcompetitor = $kwrs['cpt_domain'];
                }
            }
        }
        */

        $seostatus = array();
        if (!empty($dgs)) {
            $seoinfo = $dgs->seostatus;
            if (!empty($seoinfo)) $seostatus = unserialize($seoinfo);

            /*
            if (!empty($kwcompetitor)) {
                $_dminfo = array();
                $_dminfo = $kwcompetitor[$domain];
                if ($_dminfo['googlepr'] > 0) $seostatus['googlepr'] = $_dminfo['googlepr'];
                if ($_dminfo['alexarank'] > 0) $seostatus['alexarank'] = $_dminfo['alexarank'];
                if ($_dminfo['onlinesince'] > 0) $seostatus['onlinesince'] = $_dminfo['onlinesince'];
                if ($_dminfo['inboundlinks'] > 0) {
                    $seostatus['inboundlinks'] = $_dminfo['inboundlinks'];
                    $seostatus['linkingdomains'] = $_dminfo['linkingdomains'];
                }
            }
            */

            if (!empty($seostatus)) {
                if (empty($seostatus['googlepr']) || !is_numeric($seostatus['googlepr']))
                    $exec[] = "googlepr";
                if (empty($seostatus['alexarank']) || !is_numeric($seostatus['alexarank']))
                    $exec[] = "alexainfo";
                if (empty($seostatus['onlinesince']) || !is_numeric($seostatus['onlinesince'])
                                                     || $seostatus['onlinesince'] < 658454400)
                    $exec[] = "onlinesince";
                if (empty($seostatus['inboundlinks']) || !is_numeric($seostatus['inboundlinks'])
                                                      || $seostatus['inboundlinks'] == -1)
                    $exec[] = "inboundlinks";

                //$rs[$domain] = $seostatus;
                if (!empty($exec)) {
                    $rs[$domain] = $seostatus;
                    $_rs = SeoUtils::seoInfo($homepage, $exec);
                    //print_r($rs);
                    //print_r($_rs);
                    /*
                    If you want to append array elements from the second array to the first array while not overwriting the elements from the first array and not re-indexing, use the + array union operator: 
                    Here we need overwriting the value, so Do NOT use + array union operator;
                    */
                    //$rs[$domain] += $_rs[$domain];
                    $rs[$domain] = array_merge($rs[$domain], $_rs[$domain]);
                    /*
                    foreach($exec as $ev) {
                        $rs[$domain][$ev]= $_rs[$domain][$ev];
                    }
                    */
                }
            }
            if (empty($rs)) {
                $rs = SeoUtils::seoInfo($homepage, $exec);
            }
        } else {
            $rs = SeoUtils::seoInfo($homepage, $exec);
        }

        if (!empty($rs)) {
            //$di->attributes = $rs[$domain];
            if (!empty($di)) {
                if (isset($rs[$domain]['googlepr']) && $rs[$domain]['googlepr'] > 0) 
                    $di->googlepr = $rs[$domain]['googlepr'];
                if ($di->onlinesince <= 658454400)
                    $di->onlinesince = $rs[$domain]['onlinesince'];
                $di->seostatus = serialize($rs[$domain]);
                $di->save();
            }

            /*
            if (!empty($dgs)) {
                if (isset($rs[$domain]['googlepr']) && $rs[$domain]['googlepr'] > 0) 
                    $dgs->googlepr = $rs[$domain]['googlepr'];
                if ($dgs->onlinesince <= 658454400)
                    $dgs->onlinesince = $rs[$domain]['onlinesince'];
                $dgs->seostatus = serialize($rs[$domain]);
                $dgs->save();
            }
            */

            if (!empty($dgs)) {
                if (isset($rs[$domain]['googlepr']) && $rs[$domain]['googlepr'] > 0) 
                    $dgs->googlepr = $rs[$domain]['googlepr'];
                if ($rs[$domain]['onlinesince'] > 658454400)
                    $dgs->onlinesince = $rs[$domain]['onlinesince'];

                if ($rs[$domain]['alexarank'] > 0)
                    $dgs->alexarank = $rs[$domain]['alexarank'];
                if ($rs[$domain]['inboundlinks'] > 0)
                    $dgs->inboundlinks = $rs[$domain]['inboundlinks'];
                if ($rs[$domain]['linkingdomains'] > 0)
                    $dgs->linkingdomains = $rs[$domain]['linkingdomains'];
                $dgs->seostatus = serialize(unserialize($dgs->seostatus) + $rs[$domain]);
                $dgs->save();
            } else {
                $gsmodel->setIsNewRecord(true);
                $gsmodel->id=NULL;
                $gsmodel->domain=$domain;
                $gsmodel->googlepr = $rs[$domain]['googlepr'];
                $gsmodel->onlinesince = $rs[$domain]['onlinesince'];
                $gsmodel->alexarank = $rs[$domain]['alexarank'];
                $gsmodel->inboundlinks = $rs[$domain]['inboundlinks'];
                $gsmodel->linkingdomains = $rs[$domain]['linkingdomains'];
                $gsmodel->seostatus = serialize($rs[$domain]);
                //$dgs->last_call_api_time = $rs[$domain]['last_call_api_time'];//we discard this one at tbl.lkm_google_search.
                $gsmodel->save();
            }

            /*
            if ($this->cache !== false && is_object(Yii::app()->getComponent('cache'))) {

                if (!empty($kwrs)) {
                    if (is_integer($this->cache)) {
                        $this->cache = array($this->cache, null);
                    }

                    if ($rs[$domain]['googlepr'] > 0) $kwcompetitor[$domain]['googlepr'] = $rs[$domain]['googlepr'];
                    if ($rs[$domain]['onlinesince'] > 0) 
                        $kwcompetitor[$domain]['onlinesince'] = $rs[$domain]['onlinesince'];
                    if ($rs[$domain]['alexarank'] > 0)
                        $kwcompetitor[$domain]['alexarank'] = $rs[$domain]['alexarank'];
                    if ($rs[$domain]['inboundlinks'] > 0) {
                        $kwcompetitor[$domain]['inboundlinks'] = $rs[$domain]['inboundlinks'];
                        $kwcompetitor[$domain]['linkingdomains'] = $rs[$domain]['linkingdomains'];
                    }
                    $kwrs['cpt_domain'] = $kwcompetitor;
                    Yii::app()->cache->set("{$keyword}", $kwrs, $this->cache[0], $this->cache[1]);
                }
            }
            */
        }
        /*
        //get the search status. like PR/Alexa/MozRank/Age/
        $homepage = "http://www." . $domain;
        $rs = SeoUtils::seoInfo($homepage);
        */

        unset($gsmodel);
        unset($cptmodel);
        return $rs;
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
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

}