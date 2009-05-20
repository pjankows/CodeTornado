<?php
class Git
{
    const git = 'git';
    const gitdir = ' --git-dir=';
    const worktree = ' --work-tree=';

    private $_config;
    
    function __construct()
    {
        $configuration = Zend_Registry::get('config');
    }
    
    private function _run($param)
    {
        $gitdir = '"/home/reveil/mgr/data/.git"';
        $worktree = '"/home/reveil/mgr/data"';
        $command = Git::git . Git::gitdir . $gitdir .
        Git::worktree . $worktree  . ' ' . $param;
        
        $result = shell_exec( $command );
        //$result = shell_exec( 'git' );
        //return($result);
        return( $command . "<br />" . $result );
    }
    
    public function addAll()
    {
        $params = 'add .';
        $result = $this->_run($params);
    }
    
    public function addFile($filename)
    {
        $params = 'add ' . escapeshellarg($filename);
        $result = $this->_run($params);
    }
    
    public function autoCommit($commitMessage)
    {
        $result = false;
        if( $commitMessage != '' )
        {
            $params = 'commit -a -m ' . escapeshellarg($commitMessage);
            $result = $this->_run($params);
        }
        return($result);
    }
    
    public function repoInit()
    {
        $params = 'init';
        $result = $this->_run($params);
        return($result);
    }
}