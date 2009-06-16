<?php
require_once MODEL_PATH . 'SessionStorage.php';
class Git
{
    //const gitdirname = '/.git';
    //const gitdir = ' --git-dir=';
    //const worktree = ' --work-tree=';
    const add = 'add';
    const commit = 'commit -a -m';
    const init = 'init';
    const cloneRepo = 'clone --no-hardlinks';
    const config_name = 'config user.name';
    const config_email = 'config user.email';
    const branch = 'branch';
    const checkout = 'checkout';
    const rev_list = 'rev-list HEAD';
    const name_rev = 'name-rev';
    const rm = 'rm';

    private $_git;
    private $_worktree;
    private $_logger;

    function __construct()
    {
        $this->_logger = Zend_Registry::get('logger');
        $configuration = Zend_Registry::get('config');
        $this->_git = $configuration->git->command;
        $this->_worktree = SessionStorage::getInstance()->getGitPath();
        if( file_exists($this->_worktree) )
        {
            chdir( $this->_worktree );
        }
        else
        {
            $this->_logger->log($this->_worktree, Zend_Log::WARN);
        }
    }

    private function _run($param, $param2 = NULL)
    {
        //$command = $this->_git . self::gitdir . $this->_gitdir
        //. self::worktree . $this->_worktree . $param;
        //$this->_logger->log($this->_worktree, Zend_Log::INFO);
        $command = $this->_git . ' ' . $param . (isset($param2) ? ' ' . escapeshellarg($param2) : '');
        if( APPLICATION_ENVIRONMENT == 'development' )
        {
            $command .= ' 2>&1';
        }

        $result = shell_exec( $command );
        $this->_logger->log($command, Zend_Log::DEBUG);
        $this->_logger->log($result, Zend_Log::INFO);
        return($result);
    }

    public function addAll()
    {
        $this->addFile('.');
    }

    public function addFile($filename)
    {
        $result = $this->_run( self::add, $filename );
    }

    public function rmFile($filename)
    {
        $result = $this->_run( self::rm, $filename );
    }

    public function autoCommit($commitMessage)
    {
        $result = false;
        if( $commitMessage != '' )
        {
            $result = $this->_run( self::commit, $commitMessage);
        }
        return($result);
    }

    public function initRepo($name, $email)
    {
        $result = $this->_run( self::init );
        $this->_run( self::config_name, $name );
        $this->_run( self::config_email, $email );
        return($result);
    }

    public function cloneRepo($name, $email, $owner, $uid)
    {
        $ownerPath2 = substr($this->_worktree, 0, -strlen(strrchr($this->_worktree, '/')) );
        $ownerPath = substr($ownerPath2, 0, -strlen(strrchr($ownerPath2, '/')) ) . '/' . $owner . '/';

        $clonePath = SessionStorage::getInstance()->getGitClonePath();

        chdir( $clonePath );
        $target = ' ' . $uid;
        $this->_logger->log($clonePath, Zend_Log::INFO);
        $this->_logger->log($target, Zend_Log::INFO);
        $result = $this->_run( self::cloneRepo, $ownerPath . $target );
        chdir( $this->_worktree );
        $this->_run( self::config_name, $name );
        $this->_run( self::config_email, $email );
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

    public function checkout($id)
    {
        $result = $this->_run( self::checkout, $id );
        return($result);
    }

    public function setBranch($branch)
    {
        $this->checkout($branch);
    }

    public function newBranch($branch)
    {
        $result = $this->_run( self::branch, $branch );
        return($result);
    }

    public function getRevs()
    {
        $result = $this->_run( self::rev_list );
        $result = explode("\n", $result);
        $max = count($result)-1;
        unset( $result[ $max ] );
        return( $result );
    }

    public function getRevName($ident)
    {
        $result = $this->_run( self::name_rev, $ident );
        return($result);
    }
}