<?php
require_once MODEL_PATH . 'DbModel.php';
require_once MODEL_PATH . 'Git.php';
class Remotes extends DbModel
{
    private $_pid;
    private $_uid;
    private $_git;

    protected function init()
    {
        $this->_pid = $this->_storage->project->pid;
        $this->_git = new Git();
    }

    public function getRepos()
    {
        $sql = 'SELECT users.uid, users.user FROM users, user_project WHERE users.uid=user_project.uid
            AND user_project.pid=? AND user_project.uid!=?';
        $result = $this->_db->fetchPairs($sql, array($this->_pid, $this->_uid));
        return($result);
    }

    public function addRemote($uid)
    {
        $sql = 'SELECT user FROM users WHERE uid=?';
        $user = $this->_db->fetchOne( $sql, array($uid) );
        $path = $this->_storage->getGitClonePath() . $uid;
        $result = $this->_git->addRemote($user, $path);
        return( $result );
    }

    public function getRemotes()
    {
        $result = $this->_git->getRemoteBranches();
        foreach( $result as $key => $value )
        {
            $result[$key] = strpos($value, ' -> ') === FALSE ?
                substr($value, 2) :
                substr($value, 2, strpos($value, ' -> ')-2);
        }
        return( $result );
    }

    public function pullRemote($remote)
    {
        $result = $this->_git->pullRemote($remote);
        return( $result );
    }

    public function setUid($uid)
    {
        $this->_uid = $uid;
    }
}