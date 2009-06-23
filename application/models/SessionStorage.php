<?php
/**
 * The class that uses all other storage classes and stores these objects in a session
*/
class SessionStorage
{
    static protected $_instance;
    private $_session;

    /*
     * ->pid
     * ->owner
     * ->name
    */
    public $project;
    /*
     * ->project
     * ->user
     * ->dirArray
     * ->fileName
    */
    public $path;

    static public function getInstance()
    {
        if( ! isset( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return( self::$_instance );
    }

    /**
     * Private
    */
    private function __construct()
    {
        $this->_session = new Zend_Session_Namespace('RTVCS');
        foreach( $this->_session as $key => $value )
        {
            $this->$key = $value;
        }
        if( ! isset( $this->project ) )
        {
            $this->project = new ProjectStorage();
        }
        if( ! isset( $this->path ) )
        {
            $this->path = new PathStorage();
        }
    }

    /**
     * __clone is restricted to private as part of the sigleton pattern
    */
    private function __clone()
    {}

    public function clearAll()
    {
        $this->project = NULL;
        $this->path = NULL;
        $this->_session->setExpirationHops(1);
    }

    public function storeAll()
    {
        if( isset( $this->project ) && isset( $this->path ) )
        {
            $this->_session->project = $this->project;
            $this->_session->path = $this->path;
        }
        else
        {
            $this->_session->setExpirationHops(1);
        }
    }

    public function getDirArray()
    {
        return( isset($this->path->dirArray) ? $this->path->dirArray : array() );
    }

    /**
     * Retrieve the path to a git repo: /configGitPath/project/user/
    */
    public function getGitPath()
    {
        if( isset($this->path->project) && isset($this->path->user) )
        {
            $base = Zend_Registry::getInstance()->config->git->path;
            return( $base . $this->path->project . $this->path->user );
        }
        else
        {
            throw new Exception('Error getting git path');
        }
    }

    public function getGitClonePath()
    {
        if( isset($this->path->project) )
        {
            $base = Zend_Registry::getInstance()->config->git->path;
            return( $base . $this->path->project );
        }
        else
        {
            throw new Exception('Error getting git clone path');
        }
    }

    /**
     * Retrieve the path to use as the file navigation base: /configDataPath/project/user/
    */
    public function getDataPath()
    {
        if( isset($this->path->project) && isset($this->path->user) )
        {
            $base = Zend_Registry::getInstance()->config->data->path;
            return( $base . $this->path->project . $this->path->user );
        }
        else
        {
            throw new Exception('Error getting data path');
        }
    }
}