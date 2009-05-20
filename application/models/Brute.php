<?php
require_once MODEL_PATH . 'DbModel.php';
/**
 * Simple brute force password breaker prevention system using a blacklist
*/
class Brute extends DbModel
{
    const forgive_days = 1;
    const max_wrong_per_ip = 100;

    private $_ip;

    protected function init()
    {
        $this->_ip = $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Returns false if the IP address is violating the rules
    */
    public function testIP()
    {
        $sql = 'SELECT count, DATEDIFF(CURDATE(), last) AS delay FROM brute WHERE ip=?';
        $result = $this->_db->fetchRow($sql, array($this->_ip));
        if( $result['delay'] >= Brute::forgive_days )
        {
            //if the entry in the database is old remove it
            $this->_db->delete('brute', 'ip='.$this->_db->quote($this->_ip));
            $result['count'] = 0;
        }
        return( $result['count'] <= Brute::max_wrong_per_ip );
    }

    /**
     * Log bad password attempt. Create new entry or increse the counter.
    */
    public function registerBad()
    {
        $data = array(
            'count' =>  new Zend_Db_Expr('count+1')
        );
        $rows = $this->_db->update('brute', $data, 'ip='.$this->_db->quote($this->_ip));
        if( ! $rows )
        {
            $data = array(
                'ip' => $this->_ip
            );
            $this->_db->insert('brute', $data);
        }
    }
}