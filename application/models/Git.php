<?php
require_once MODEL_PATH . 'SessionStorage.php';
class Git
{
    //const gitdirname = '/.git';
    //const gitdir = ' --git-dir=';
    //const worktree = ' --work-tree=';
    const add = ' add ';
    const commit = ' commit -a -m ';
    const init = ' init';
    const config_name = ' config user.name ';
    const config_email = ' config user.email ';
    const branch = ' branch ';
    const rm = ' rm ';

    private $_git;
    private $_worktree;
    private $_logger;

    function __construct()
    {
        $configuration = Zend_Registry::get('config');
        $this->_git = $configuration->git->command;
        $this->_worktree = SessionStorage::getInstance()->getGitPath();
        $this->_logger = Zend_Registry::get('logger');
    }

    private function _run($param)
    {
        //$command = $this->_git . self::gitdir . $this->_gitdir
        //. self::worktree . $this->_worktree . $param;
        chdir( $this->_worktree );
        $this->_logger->log($this->_worktree, Zend_Log::INFO);
        $command = $this->_git . $param;
        if( APPLICATION_ENVIRONMENT == 'development' )
        {
            //$command .= ' 2>&1';
        }

        $result = shell_exec( $command );
        //return( $command . "<br />" . $result );
        $this->_logger->log($command, Zend_Log::INFO);
        $this->_logger->log($result, Zend_Log::INFO);
        return($result);
    }

    public function addAll()
    {
        $this->addFile('.');
    }

    public function addFile($filename)
    {
        $result = $this->_run( self::add . escapeshellarg($filename) );
    }

    public function rmFile($filename)
    {
        $result = $this->_run( self::rm . escapeshellarg($filename) );
    }

    public function autoCommit($commitMessage)
    {
        $result = false;
        if( $commitMessage != '' )
        {
            $params = self::commit . escapeshellarg($commitMessage);
            $result = $this->_run($params);
        }
        return($result);
    }

    public function initRepo($name, $email)
    {
        $result = $this->_run( self::init );
        $this->_run( self::config_name . escapeshellarg($name) );
        $this->_run( self::config_email . escapeshellarg($email) );
        return($result);
    }

    public function getBranches()
    {
        $result = $this->_run( self::branch );
        //split by newlines
        $result = explode("\n", $result);
        //unset the empty element at the end of array
        $max = count($result)-1;
        unset( $result[ $max ] );
        return( $result );
    }

    public function setBranch($branch)
    {
        $result = $this->_run( self::branch . escapeshellarg($branch) );
        return($result);
    }
}