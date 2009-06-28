<?php
require_once MODEL_PATH . 'SessionStorage.php';
class Git
{
    const fatal = 'fatal';
    const add = 'add';
    const commit = 'commit -a -m';
    const initRepo = 'init';
    const cloneRepo = 'clone --no-hardlinks';
    const config_name = 'config user.name';
    const config_email = 'config user.email';
    const branch = 'branch';
    const branch_remote = 'branch -r';
    const checkout = 'checkout';
    const rev_list = 'rev-list HEAD';
    const name_rev = 'name-rev';
    const rm = 'rm';
    const pull = 'pull';
    const remote_update = 'remote update';
    const remote_add = 'remote add -f';
    const merge = 'merge';
    const fetch = 'fetch';
    const cd = 'cd';

    private $_shell;
    private $_git;
    private $_worktree;
    private $_logger;

    function __construct($worktree = NULL)
    {
        $this->_logger = Zend_Registry::get('logger');
        $configuration = Zend_Registry::get('config');
        $this->_git = $configuration->git->command;
        $this->_shell = $configuration->git->shell;
        $this->_worktree = SessionStorage::getInstance()->getGitPath();
        if($this->_shell === '')
        {
            if(  file_exists($this->_worktree) )
            {
                chdir( $this->_worktree );
                $this->_logger->log('Switched to: ' . $this->_worktree, Zend_Log::DEBUG);
            }
            else
            {
                $this->_logger->log('Path not found: ' . $this->_worktree, Zend_Log::WARN);
            }
        }
        else
        {
            $this->_logger->log('Remote shell path to be set: ' . $this->_worktree, Zend_Log::INFO);
        }
    }

    private function _splitByN($str)
    {
        //split by newlines
        $result = explode("\n", $str);
        //unset the empty element at the end of array
        $max = count($result)-1;
        unset( $result[ $max ] );
        return( $result );
    }

    private function _escapeParams($params)
    {
        $result = '';
        if( is_array($params) )
        {
            foreach( $params as $par )
            {
                $result .= ' ' . escapeshellarg($par);
            }
        }
        else
        {
            $result .= ' ' . escapeshellarg($params);
        }
        return($result);
    }

    private function _run($param, $param2 = NULL)
    {
        //$this->_logger->log($this->_worktree, Zend_Log::INFO);
        $git = $this->_git . ' ' . $param . (isset($param2) ? $this->_escapeParams($param2)  : '');
        $command = '';
        //ssh to remote host if needed to run git via the shell config variable
        if( $this->_shell !== '' )
        {
            //if the remote shell is required perform directory switch on each command
            $cd = self::cd . ' ' . $this->_worktree . ';';
            $git .= ';';
            $command = $this->_shell . ' ' . escapeshellarg( $cd . $git );
        }
        else
        {
            $command = $git;
        }

        if( APPLICATION_ENVIRONMENT == 'development' )
        {
            $command .= ' 2>&1';
        }

        $result = shell_exec( $command );
        if( $param !== self::name_rev )
        {
            $this->_logger->log($command, Zend_Log::DEBUG);
            $this->_logger->log($result, Zend_Log::INFO);
        }
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
        chdir( $this->_worktree );
        $result = $this->_run( self::initRepo );
        $this->_run( self::config_name, $name );
        $this->_run( self::config_email, $email );
        return($result);
    }

    public function cloneRepo($name, $email, $owner, $uid)
    {
        $ownerPath2 = substr($this->_worktree, 0, -strlen(strrchr($this->_worktree, '/')) );
        $ownerPath = substr($ownerPath2, 0, -strlen(strrchr($ownerPath2, '/')) ) . '/' . $owner . '/';

        $clonePath = SessionStorage::getInstance()->getGitClonePath();

        $properPath = $this->_worktree;
        //worktree needs to be set up for ssh based git shells
        $this->_worktree = $clonePath;
        chdir( $this->_worktree );
        $target = $uid;
        $this->_logger->log($clonePath, Zend_Log::INFO);
        $this->_logger->log($target, Zend_Log::INFO);
        $result = $this->_run( self::cloneRepo, array($ownerPath, $target) );
        //after running clone the proper user folder should exist now
        $this->_worktree = $properPath;
        chdir( $this->_worktree );
        $this->_run( self::config_name, $name );
        $this->_run( self::config_email, $email );
    }

    public function getBranches()
    {
        $result = $this->_run( self::branch );
        $result = $this->_splitByN( $result );
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
        //$result = $this->_run( self::branch, '_auto_' . $branch );
        $result = $this->checkout( '_auto_' . $branch );
        return($result);
    }

    public function getRevs()
    {
        $result = $this->_run( self::rev_list );
        if( strpos($result, self::fatal ) === FALSE )
        {
            $result = $this->_splitByN( $result );
        }
        else
        {
            //for repo with no commits
            $result = array();
        }
        return( $result );
    }

    public function getRevName($ident)
    {
        $result = $this->_run( self::name_rev, $ident );
        return($result);
    }

    private function pull($repopath, $branch = '')
    {
        $result = $this->_run( self::pull, array($repopath, $branch) );
        return($result);
    }

    public function pullRemote($remoteBranch)
    {
        $result = $this->pull('.', "remotes/$remoteBranch");
        return($result);
    }

    public function addRemote($name, $url)
    {
        $result = $this->_run( self::remote_add, array($name, $url) );
        return($result);
    }

    public function getRemoteBranches()
    {
        $this->remoteUpdate();
        $result = $this->_run( self::branch_remote );
        $result = $this->_splitByN( $result );
        return($result);
    }

    public function merge($branch)
    {
        $result = $this->_run( self::merge, $branch );
        return($result);
    }

    public function remoteUpdate()
    {
        $result= $this->_run( self::remote_update );
        return($result);
    }
}
