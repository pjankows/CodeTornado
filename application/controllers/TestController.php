<?php
//require_once MODEL_PATH . 'RawIO.php';
//require_once MODEL_PATH . 'Git.php';
//require_once MODEL_PATH . 'FileNavigation.php';
//require_once FORM_PATH . 'NewFileForm.php';
class TestController extends MainController
{
    public function indexAction()
    {
        $result = array();
//         $this->_storage->clearAll();
//         $this->_storage->setProject(1);
//         $this->_storage->setUserPath('1/');
//         $this->_storage->setLocalPath('/');
//         $result['project'] = $this->_storage->getProject();
//         $result['projectPath'] = $this->_storage->getProjectPath();
//         $result['userPath'] = $this->_storage->getUserPath();
//         $result['gitPath'] = $this->_storage->getGitUserPath();
//         $result['localPath'] = $this->_storage->getWorkLocalPath();
//         $this->_storage->setProject(111);
        //$this->_storage->path->fromUid(23);
        $git = new Git();
        $result = $git->getHistory();
        $this->_logger = Zend_Registry::get('logger');
        $this->_logger->log($result, Zend_Log::INFO);
        $this->view->test = $result;
    }
}
