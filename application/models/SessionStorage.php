<?php
class SessionStorage
{
    private $_session;

    private $_project = null;
    private $_projectPath = null;
    private $_userPath = null;
    private $_branch = null;
    private $_localPath = null;
    private $_fileName = null;

    function __construct()
    {
        $this->_session = new Zend_Session_Namespace('RTVCS');
        foreach( $this->_session as $key => $value )
        {
            $this->$key = $value;
        }
    }

    public function storeAll()
    {
        $this->_store('_project');
        $this->_store('_projectPath');
        $this->_store('_userPath');
        $this->_store('_workPath');
        $this->_store('_gitPath');
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
        $this->setProjectPath( $this->$id . PATH_SEPARATOR );
    }

    public function getProject()
    {
        return( $this->_project );
    }

    public function setProjectPath($path)
    {
        $this->_projectPath = $path;
    }

    public function getProjectPath()
    {
        return( $this->_projectPath );
    }

    public function setUserPath($path)
    {
        $this->_userPath = $path;
    }

    public function getUserPath()
    {
        return( $this->_userPath );
    }

    public function setBranch($branchName)
    {
        $this->_branch = $branchName;
    }

    public function getBranch()
    {
        return( $this->_branch );
    }

    public function setLocalPath($path)
    {
        $this->_localPath = $path;
    }

    public function getLocalPath()
    {
        return( $this->_localPath );
    }
    /*
     * End getter and setter methods
    */

    /**
     * Retrieve full work path to currently edited file or active directory
    */
    public function getWorkLocalPath()
    {
        return( DATA_PATH . $this->_projectPath . $this->_userPath . $this->_localPath );
    }

    /**
     * Retrieve the path to a git repo
    */
    public function getGitUserPath()
    {
        return( GIT_PATH . $this->_projectPath . $this->_userPath );
    }
}