<?php
require_once MODEL_PATH . 'SessionStorage.php';
abstract class DbModel
{
    static protected $_db;
    static protected $_storage;

    function __construct()
    {
        $this->_db = Zend_Registry::get('dbAdapter');
        $this->_storage = SessionStorage::getInstance();
        $this->init();
    }

    abstract protected function init();
}
