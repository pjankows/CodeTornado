<?php
require_once MODEL_PATH . 'DbModel.php';
require_once MODEL_PATH . 'Project.php';
require_once MODEL_PATH . 'User.php';
require_once MODEL_PATH . 'SessionStorage.php';
//require_once MODEL_PATH . 'FileNavigation.php';
//require_once MODEL_PATH . 'BranchNavigation.php';
abstract class MainController extends Zend_Controller_Action
{
    protected $_project;
    protected $_user;
    protected $_storage;
    protected $_git;
    //protected $_fileNav;
    //protected $_branchNav;

    public function init()
    {
        $this->_storage = SessionStorage::getInstance();
        $this->_user = new User();
        $this->_project = new Project();
        if( isset( $this->_user->loggedIn ) )
        {
            $loggedIn = $this->_user->loggedIn;
            //project model also uses this data for project selection
            $this->_project->setUserData( $loggedIn->uid, $this->_user->getPath(),
                                          $loggedIn->name, $loggedIn->email );
            if( isset( $this->_procject->active ) )
            {
                $this->_storage->setProject( $this->_project->active );
            }

        }
        $this->view->loggedIn = $this->_user->loggedIn;
        $this->view->active = $this->_project->active;
        //$this->_branchNav = new BranchNavigation($this->_project, $this->_user);
    }

    public function postDispatch()
    {
        $this->_storage->storeAll();
    }
}
