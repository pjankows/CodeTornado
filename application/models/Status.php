<?php
require_once MODEL_PATH . 'DbModel.php';
class Status extends DbModel
{
    private $_pid;
    private $_uid;

    protected function init()
    {
        $this->_pid = $this->_storage->project->pid;
    }

    public function setUid($uid)
    {
        $this->_uid = $uid;
    }

    public function addStatus($msg)
    {
        $data = array(
            'pid' => $this->_pid,
            'uid' => $this->_uid,
            'action' => $msg
        );
        $this->_db->insert('status', $data);
    }

    public function getStatusMessages()
    {
        $sql = 'SELECT sid, t, action, name FROM status, users WHERE users.uid=status.uid AND pid=? ORDER BY sid DESC LIMIT 0, 4';
        $result = $this->_db->fetchAssoc($sql, array($this->_pid));
        return($result);
    }
}