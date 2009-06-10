<?php
require_once MODEL_PATH . 'SessionStorage.php';
class Git
{
    const gitdirname = '/.git';
    const gitdir = ' --git-dir=';
    const worktree = ' --work-tree=';
    const add = ' add ';
    const commit = ' commit -a -m ';
    const init = ' init';
    const config_name = ' config user.name ';
    const config_email = ' config user.email ';
    const branch = ' branch ';

    private $_git;
    private $_worktree;

    function __construct($worktree)
    {
        $configuration = Zend_Registry::get('config');
        $this->_git = $configuration->git->command;
        $this->_worktree = $worktree;
    }

    private function _run($param)
    {
        //$command = $this->_git . self::gitdir . $this->_gitdir
        //. self::worktree . $this->_worktree . $param;
        chdir( $this->_worktree );
        $command = $this->_git . $param;

        $result = shell_exec( $command );
        //return( $command . "<br />" . $result );
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

    private function _branches()
    {
        $result = $this->_run( self::branch );
        //split by newlines
        $result = explode("\n", $result);
        //unset the empty element at the end of array
        $max = count($result)-1;
        unset( $result[ $max ] );
        return( $result );
    }

    private function _branchStrip2($string)
    {
        $result;
    }

    public function getBranches()
    {
        $result = $this->_branches();
        foreach( $result as $key => $value )
        {
            $result[$key] = substr($value, 2);
        }
        return($result);
    }

    public function getActiveBranch()
    {
        $result = null;
        $branches = $this->_branches();
        foreach( $result as $value )
        {
            $first = $value[0];
            if( $first === '*' )
            {
                $result = substr($value, 2);
            }
        }
        return( $result );
    }

    public function setBranch($branch)
    {
        $result = $this->_run( self::branch . escapeshellarg($branch) );
        return($result);
    }
}