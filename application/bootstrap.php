<?php
defined('APPLICATION_PATH')
    or define('APPLICATION_PATH', dirname(__FILE__));
defined('APPLICATION_ENVIRONMENT')
    or define('APPLICATION_ENVIRONMENT', 'development');

require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();

Zend_Session::start();

$configuration = new Zend_Config(require APPLICATION_PATH . '/config.php');

define( 'DATA_PATH', $configuration->data->path );
define( 'MODEL_PATH', APPLICATION_PATH . '/models/' );
define( 'FORM_PATH', APPLICATION_PATH . '/forms/' );
define( 'CONTROLLER_PATH', APPLICATION_PATH . '/controllers' );
define( 'LAYOUT_PATH', APPLICATION_PATH . '/views/layouts');

$dbAdapter = Zend_Db::factory($configuration->database);
//Zend_Db_Table_Abstract::setDefaultAdapter($dbAdapter);

$registry = Zend_Registry::getInstance();
$registry->config = $configuration;
$registry->salt = $configuration->salt;
$registry->dbAdapter = $dbAdapter;

$frontController = Zend_Controller_Front::getInstance();
$frontController->setControllerDirectory( CONTROLLER_PATH );
$frontController->setParam('env', APPLICATION_ENVIRONMENT);

Zend_Layout::startMvc(LAYOUT_PATH);
$view = Zend_Layout::getMvcInstance()->getView();
$view->doctype('XHTML1_STRICT');

require_once CONTROLLER_PATH . '/MainController.php';

unset($frontController, $view, $configuration, $dbAdapter, $registry);
