<?php
//require_once MODEL_PATH . 'RawIO.php';
//require_once MODEL_PATH . 'Git.php';
//require_once MODEL_PATH . 'FileNavigation.php';
//require_once FORM_PATH . 'NewFileForm.php';
class TestController extends MainController
{
    public function indexAction()
    {
//         $git = new Git( $this->_project->getPath() . $this->_user->getPath() );
//         $result = $git->getBranches();


        $logger = Zend_Registry::get('logger');
        $logger->log($result, Zend_Log::INFO);
        $this->view->test = $result;
    }
}
