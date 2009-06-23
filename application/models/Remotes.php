<?php
require_once MODEL_PATH . 'DbModel.php';
require_once MODEL_PATH . 'Git.php';
class Remotes extends DbModel
{
    private $_pid;
    private $_uid;

    protected function init()
    {
        $this->_pid = $this->_storage->project->pid;
    }

    public function getRepos()
    {
        $sql = 'SELECT users.uid, users.user FROM users, user_project WHERE users.uid=user_project.uid
            AND user_project.pid=? AND user_project.uid!=?';
        $result = $this->_db->fetchPairs($sql, array($this->_pid, $this->_uid));
        return($result);
    }

    public function getRemotes()
    {
        //$sql = 'SELECT '
    }

    public function setUid($uid)
    {
        $this->_uid = $uid;
    }
}