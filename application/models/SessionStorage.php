<?php
class SessionStorage
{
    static protected $_instance;
    protected $_session;

    private $_project;
    private $_projectPath;
    private $_userPath;
    private $_branch;
    private $_localPath;
    private $_fileName;

    static public function getInstance()
    {
        if( ! isset( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return( self::$_instance );
    }

    private function __construct()
    {
        $this->_session = new Zend_Session_Namespace('RTVCS');
        foreach( $this->_session as $key => $value )
        {
            $this->$key = $value;
        }
    }

    private function __clone()
    {}

    public function clearAll()
    {
        $this->_project = NULL;
        $this->_projectPath = NULL;
        $this->_userPath = NULL;
        $this->_branch = NULL;
        $this->_localPath = NULL;
        $this->_fileName = NULL;
        $this->_session->setExpirationHops(1);
    }

    public function storeAll()
    {
        $this->_store('_project');
        $this->_store('_projectPath');
        $this->_store('_userPath');
        $this->_store('_branch');
        $this->_store('_localPath');
        $this->_store('_fileName');
    }

    private function _store($valueName)
    {
        if( isset( $this->$valueName ) )
        {
            $this->_session->$valueName = $this->$valueName;
        }
    }

    /*
     * Start getter and setter methods
    */
    public function setProject($id)
    {
        $this->_project = $id;
        if( is_int($id) )
        {
            $this->setProjectPath( $this->_project . '/' );
        }
    }

    public function getProject()
    {
        return( isset( $this->_project ) ? $this->_project : NULL );
    }

    public function setProjectPath($path)
    {
        $this->_projectPath = $path;
    }

    public function getProjectPath()
    {
        return( isset( $this->_projectPath ) ? $this->_projectPath : NULL );
    }

    public function setUserPath($path)
    {
        $this->_userPath = $path;
    }

    public function getUserPath()
    {
        return( isset( $this->_userPath ) ? $this->_userPath : NULL );
    }

    public function setBranch($branchName)
    {
        $this->_branch = $branchName;
    }

    public function getBranch()
    {
        return( isset( $this->_branch ) ? $this->_branch : NULL );
    }

    public function setLocalPath($path)
    {
        $this->_localPath = $path;
    }

    public function getLocalPath()
    {
        return( isset( $this->_localPath ) ? $this->_localPath : NULL );
    }

    public function setFileName($name)
    {
        $this->_fileName = $name;
    }

    public function getFileName()
    {
        return( isset( $this->_fileName ) ? $this->_fileName : NULL );
    }
    /*
     * End getter and setter methods
    */

    /**
     * Retrieve full work path to currently edited file or active directory
    */
    public function getWorkLocalPath()
    {
        if( isset($this->_projectPath) && isset($this->_userPath) && isset($this->_localPath)  )
        {
            $base = Zend_Registry::getInstance()->config->data->path;
            return( $base . $this->_projectPath . $this->_userPath . $this->_localPath );
        }
        else
        {
            return( NULL );
        }
    }

    /**
     * Retrieve the path to a git repo
    */
    public function getGitUserPath()
    {
        if( isset($this->_projectPath) && isset($this->_userPath) )
        {
            $base = Zend_Registry::getInstance()->config->git->path;
            return( $base . $this->_projectPath . $this->_userPath );
        }
        else
        {
            return( NULL );
        }
    }

    /**
     * Retrieve the path to use as the file navigation base
    */
    public function getWorkUserPath()
    {
        if( isset($this->_projectPath) && isset($this->_userPath) )
        {
            $base = Zend_Registry::getInstance()->config->data->path;
            return( $base . $this->_projectPath . $this->_userPath );
        }
        else
        {
            return( NULL );
        }
    }
}