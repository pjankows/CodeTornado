<?php
require_once MODEL_PATH . 'Status.php';
class TestController extends MainController
{
    public function indexAction()
    {
        $status = new Status();
        $status->setUid( $this->_user->loggedIn->uid );
        $status->addStatus("test");
        $this->_logger->log( $status->getStatusMessages(), Zend_Log::INFO );
    }
}
