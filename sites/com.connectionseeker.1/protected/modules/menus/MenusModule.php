<?php

class MenusModule extends CWebModule
{
	/**
	* @property string the name of the role with superuser privileges.
	*/
	public $superuserName = 'Admin';
	/**
	* @property string the name of the guest role.
	*/
	public $authenticatedName = 'Authenticated';
	/**
	* @property string the name of the user model class.
	*/
	public $menuClass = 'Menu';
	/**
	* @property string the name of the id column in the user table.
	*/
	public $menuIdColumn = 'id';
	/**
	* @property string the name of the username column in the user table.
	*/
	public $menuNameColumn = 'name';
	/**
	* @property boolean whether to enable business rules.
	*/
	public $enableBizRule = true;
	/**
	* @property boolean whether to enable data for business rules.
	*/
	public $enableBizRuleData = false;
	/**
	* @property boolean whether to display authorization items description 
	* instead of name it is set.
	*/
	public $displayDescription = true;
	/**
	* @property string the flash message key to use for success messages.
	*/
	public $flashSuccessKey = 'MenusSuccess';
	/**
	* @property string the flash message key to use for error messages.
	*/
	public $flashErrorKey = 'MenusError';
	/**
	* @property boolean whether to install menus when accessed.
	*/
	public $install = false;
	/**
	* @property string the base url to Menus. Override when module is nested.
	*/
	public $baseUrl = '/menus';
	/**
	* @property string the path to the layout file to use for displaying Menus.
	*/
	public $layout = 'menus.views.layouts.main';
	/**
	* @property string the path to the application layout file.
	*/
	public $appLayout = 'application.views.layouts.main';
	/**
	* @property string the style sheet file to use for Menus.
	*/
	public $cssFile;
	/**
	* @property boolean whether to enable debug mode.
	*/
	public $debug = false;

	private $_assetsUrl;

	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'menus.components.*',
			'menus.components.behaviors.*',
			'menus.components.dataproviders.*',
			'menus.controllers.*',
			'menus.models.*',
		));


		// Set the required components.
		$this->setComponents(array(
			'menu'=>array(
				'class'=>'MMenu',
			),
			'generator'=>array(
				'class'=>'MGenerator',
			),
		));
        

		// Normally the default controller is Assignment.
		$this->defaultController = 'tab';

		// Set the installer if necessary.
		if( $this->install===true )
		{
			$this->setComponents(array(
				'installer'=>array(
					'class'=>'RInstaller',
					'superuserName'=>$this->superuserName,
					'authenticatedName'=>$this->authenticatedName,
					'guestName'=>Yii::app()->user->guestName,
					'defaultRoles'=>Yii::app()->authManager->defaultRoles,
				),
			));

			// When installing we need to set the default controller to Install.
			$this->defaultController = 'install';
		}
       
	}

	/**
	* Registers the necessary scripts.
	*/
	public function registerScripts()
	{
		// Get the url to the module assets
		$assetsUrl = $this->getAssetsUrl();

		// Register the necessary scripts
		$cs = Yii::app()->getClientScript();
		$cs->registerCoreScript('jquery');
		$cs->registerCoreScript('jquery.ui');
		$cs->registerScriptFile($assetsUrl.'/js/menus.js');
		$cs->registerCssFile($assetsUrl.'/css/core.css');

		// Make sure we want to register a style sheet.
		if( $this->cssFile!==false )
		{
			// Default style sheet is used unless one is provided.
			if( $this->cssFile===null )
				$this->cssFile = $assetsUrl.'/css/default.css';
			else
				$this->cssFile = Yii::app()->request->baseUrl.$this->cssFile;

			// Register the style sheet
			$cs->registerCssFile($this->cssFile);
		}
	}

	/**
	* Publishes the module assets path.
	* @return string the base URL that contains all published asset files of Menus.
	*/
	public function getAssetsUrl()
	{
		if( $this->_assetsUrl===null )
		{
			$assetsPath = Yii::getPathOfAlias('menus.assets');

			// We need to republish the assets if debug mode is enabled.
			if( $this->debug===true )
				$this->_assetsUrl = Yii::app()->getAssetManager()->publish($assetsPath, false, -1, true);
			else
				$this->_assetsUrl = Yii::app()->getAssetManager()->publish($assetsPath);
		}

		return $this->_assetsUrl;
	}
	/**
	* @return MenusAuthorizer the authorizer component.
	*/
	public function getAuthorizer()
	{
		return $this->getComponent('authorizer');
	}


	public function getMenu()
	{
		return $this->getComponent('menu');
	}

	/**
	* @return MenusInstaller the installer component.
	*/
	public function getInstaller()
	{
        
		return $this->getComponent('installer');
	}

	/**
	* @return MenusGenerator the generator component.
	*/
	public function getGenerator()
	{
		return $this->getComponent('generator');
	}


	/**
	* @return the current version.
	*/
	public function getVersion()
	{
		return '1.0';
	}


	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		}
		else
			return false;
    }
}
