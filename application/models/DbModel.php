<?php
abstract class DbModel
{
    protected $_db;
    
    function __construct()
    {
        $this->_db = Zend_Registry::get('dbAdapter');
        $this->init();
    }

    abstract protected function init();
}
