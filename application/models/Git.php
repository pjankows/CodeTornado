<?php
class Git
{
    const gitdirname = '/.git';
    const gitdir = ' --git-dir=';
    const worktree = ' --work-tree=';
    const add = ' add ';
    const commit = ' commit -m ';
    const init = ' init';

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
        //$result = shell_exec( 'git' );
        //return($result);
        return( $command . "<br />" . $result );
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

    public function init()
    {
        $result = $this->_run( self::init );
        return($result);
    }
}