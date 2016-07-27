<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'ConnectionSeeker RESTFul API Console',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
        'application.modules.rights.*',
        'application.modules.rights.components.*',

        'application.modules.restapi.*',
        'application.modules.restapi.components.*',
	),

	'modules'=>array(
        'rights'=>array(
            'install'=>false,
            'superuserName'=>'Admin',
            //'authenticatedName'=>'Authenticated',
            'authenticatedName'=>null,
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

        'restapi'=>array(
            'baseUrl'=>'/restapi',
            'layout'=>'restapi.views.api.output',
            'appLayout'=>'restapi.views.layouts.json',
            'debug'=>true,
        ),

	),

	// application components
	'components'=>array(
		'user'=>array(
            // enable cookie-based authentication
            'allowAutoLogin'=>true,
            //use the rights module
            'class'=>'RWebUser',
		),

        'session'=>array(
            'timeout'=>1,
        ),

        'authManager'=>array(
            'class'=>'RDbAuthManager',
            'itemTable' => 'lkm_auth_item',//auth item table name
            'itemChildTable' => 'lkm_auth_item_child',//auth item child relationship table
            'assignmentTable' => 'lkm_auth_assignment',//auth item assignment table
            'rightsTable' => 'lkm_rights',//the default table for the right modules
        ),

		// uncomment the following to enable URLs in path-format
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
                /*
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
                */
                array('api/list', 'pattern'=>'api/<model:\w+>', 'verb'=>'GET'),
                array('api/view', 'pattern'=>'api/<model:\w+>/<id:\d+>', 'verb'=>'GET'),
                //for some security reason, do the customize here! only for get client;
                //###array('api/client', 'pattern'=>'api/<model:\w+>/<id:[\w|@|\.|-]+>', 'verb'=>'GET'),
                //this is using for customize action metohd
                /*
                array('api/client', 'pattern'=>'api/<model:\w+>/<id:[\s\S]+>', 'verb'=>'GET'),
                */
                //Custmize View, this one can pass the parameters as int/string...
                array('api/cmview', 'pattern'=>'api/<model:\w+>/<id:[\s\S]+>', 'verb'=>'GET'),
                array('api/update', 'pattern'=>'api/<model:\w+>/<id:\d+>', 'verb'=>'PUT'),
                array('api/delete', 'pattern'=>'api/<model:\w+>/<id:\d+>', 'verb'=>'DELETE'),
                //array('api/client', 'pattern'=>'api/<model:client>', 'verb'=>'POST'),
                array('api/user', 'pattern'=>'api/<model:user>', 'verb'=>'POST'),//customize method
                //array('api/clientDomain', 'pattern'=>'api/<model:clientdomain>', 'verb'=>'POST', 'caseSensitive'=>false),//customize method, mapping to actionClientDomain()
                array('api/create', 'pattern'=>'api/<model:\w+>', 'verb'=>'POST'),

                // Other controllers
                '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		/*
		'urlManager'=>array(
			//'urlFormat'=>'path',
            //'showScriptName' => false, // hide the index.php in url
            //'urlSuffix' => '.html', //For url suffix

            'urlFormat'=>'get',
			'rules'=>require(dirname(__FILE__).'/../extensions/restfullyii/config/routes.php'),
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

			//Open mysql execute sql log
			'enableParamLogging' => true,
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
				// uncomment the following to show log messages on web pages
				
				array(
					'class'=>'CWebLogRoute',
                    'levels' => 'trace, error, warning',
                    'categories' => 'system.db.*'

                    ////////////////////////////////////////////////////////////////////////////
                    //'showInFireBug'=>true,
                    //'levels'=>'trace',
                    //'categories'=>'vardump',
                    ////////////////////////////////////////////////////////////////////////////
				),
				
			),
		),

        'cache'  => array(
            //'class'  => 'system.caching.CXCache',
            'class'  => 'system.caching.CFileCache',
        ),

	),
    //'language'=>'zh_cn',
    //'theme' => 'connectionseeker',

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'leo@infinitenine.com',
		//'contentAPI'=>'https://api.copypress.com',
		'contentAPI'=>'http://apidev.i9cms.com',
	),

);