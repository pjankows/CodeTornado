<?php
require_once MODEL_PATH . 'DbModel.php';
require_once MODEL_PATH . 'Project.php';
require_once MODEL_PATH . 'User.php';
//require_once MODEL_PATH . 'FileNavigation.php';
//require_once MODEL_PATH . 'BranchNavigation.php';
abstract class MainController extends Zend_Controller_Action
{
    protected $_project;
    protected $_user;
    //protected $_fileNav;
    //protected $_branchNav;

    public function init()
    {
        $this->_user = new User;
        $this->_project = new Project;
        if( $this->_user->loggedIn )
        {
            $this->_project->setUserData( $this->_user->loggedIn->uid, $this->_user->getPath() );
        }
        //$this->_project->setUserModel($this->_user);
        $this->view->loggedIn = $this->_user->loggedIn;
        $this->view->active = $this->_project->active;
        //$this->_branchNav = new BranchNavigation($this->_project, $this->_user);
    }
}
