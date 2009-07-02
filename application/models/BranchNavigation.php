<?php
require_once MODEL_PATH . 'Git.php';
class BranchNavigation
{
    private $_git;
    private $_branches;

    function __construct()
    {
        $this->_git = new Git();
    }

    public function getBranches()
    {
        if( ! isset( $this->_branches ) )
        {
            $this->_branches = $this->_git->getBranches();
        }
        $result = $this->_branches;
        foreach( $result as $key => $value )
        {
            $result[$key] = substr($value, 2);
        }
        return($result);
    }

    public function getActiveBranch()
    {
        $result = NULL;
        if( ! isset( $this->_branches ) )
        {
            $this->_branches = $this->_git->getBranches();
        }
        $branches = $this->_branches;
        if( count($branches) > 0 )
        {
            foreach( $branches as $value )
            {
                $first = $value[0];
                if( $first === '*' )
                {
                    $result = substr($value, 2);
                }
            }
        }
        return( $result );
    }

    public function setBranch($branch)
    {
        $this->_git->autoCommit('AutoCommit: Changing branch to '.$branch);
        $this->_git->setBranch($branch);
    }

    public function newBranch($formData)
    {
        $this->_git->autoCommit('AutoCommit: Making new branch '.$formData['name_branch']);
        if( is_array($formData) && isset($formData['name_branch']) )
        {
            $this->_git->newBranch($formData['name_branch']);
        }
        else
        {
            throw new Exception('Branch name in incorrect format');
        }
    }

    public function getState()
    {
        $msg = $this->_git->catFileP('HEAD');
        $state = $this->_git->status();
        return( "State:\n" . $state . "\nLast commit:\n" . $msg );
    }
}