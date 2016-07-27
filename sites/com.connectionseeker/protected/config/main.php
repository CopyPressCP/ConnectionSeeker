<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'ConnectionSeeker Console',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
            'application.models.*',
            'application.components.*',
            'application.modules.rights.*',
            'application.modules.rights.components.*',
            'application.modules.menus.*', 
            'application.modules.menus.components.*',
            'ext.yii-mail.YiiMailMessage',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
                /*
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'iloveit',
		 	// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
                */

        'rights'=>array(
            'install'=>false,
            'superuserName'=>'Admin',
            'authenticatedName'=>'Authenticated',
            'userIdColumn'=>'id',
            'userNameColumn'=>'username',
            'enableBizRule'=>true,
            'enableBizRuleData'=>false,
            'displayDescription'=>true,
            'flashSuccessKey'=>'RightsSuccess',
            'flashErrorKey'=>'RightsError',
            'baseUrl'=>'/rights',
            'layout'=>'rights.views.layouts.main',
            'appLayout'=>'webroot.themes.connectionseeker.views.layouts.main',
            'debug'=>true,
        ),
        'menus'=>array('appLayout'=>'webroot.themes.connectionseeker.views.layouts.main'),
	),

	// application components
	'components'=>array(
		'user'=>array(
            // enable cookie-based authentication
            'allowAutoLogin'=>true,
            //use the rights module
            'class'=>'RWebUser',
		),

        'authManager'=>array(
            'class'=>'RDbAuthManager',
            'itemTable' => 'lkm_auth_item',//auth item table name
            'itemChildTable' => 'lkm_auth_item_child',//auth item child relationship table
            'assignmentTable' => 'lkm_auth_assignment',//auth item assignment table
            'rightsTable' => 'lkm_rights',//the default table for the right modules
        ),

		// uncomment the following to enable URLs in path-format
		/*
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		*/
        /*
		'db'=>array(
			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
		),
        */
		// uncomment the following to use a MySQL database
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=com_linkmev2',
			'emulatePrepare' => true,
			'username' => 'connectionseeker',
			'password' => 'see123ker6',
			'charset' => 'utf8',
            'tablePrefix' => 'lkm_',

            //##Open mysql execute sql log
            //##'enableParamLogging' => true,
		),

		'errorHandler'=>array(
			// use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
                                /*
				// uncomment the following to show log messages on web pages
				array(
					'class'=>'CWebLogRoute',
				        'levels' => 'trace, error, warning',
                                        'categories' => 'system.db.*',
                                ),
                                */
			),
		),
               'cache'  => array(
                    //'class'  => 'system.caching.CXCache',
                    'class'  => 'system.caching.CFileCache',
               ),
        'mail' => array(
            'class' => 'ext.yii-mail.YiiMail',
            'transportType' => 'smtp',
            'viewPath' => 'application.views.mail',
            'logging' => true,
            'dryRun' => false,
            'transportOptions' => array(
                                    'host' => 'smtp.sendgrid.net',
                                    'username' => 'huamarketing',
                                    'password' => 'poops830',
                                    'port' => '587',
                                  ),
        ),
	),

    'theme' => 'connectionseeker',

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'leo@infinitenine.com',
		'contentAPI'=>'https://api.copypress.com',
		//'contentAPI'=>'http://apidev.i9cms.com',
	),
);
