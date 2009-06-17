<?php
require_once MODEL_PATH . 'Git.php';
class HistoryNavigation
{
    const headerr = "Could not get sha1 for HEAD. Skipping.\n";
    const head = 'HEAD';

    private $_git;

    function __construct()
    {
        $this->_git = new Git();
    }

    public function getHistory()
    {
        $result = array();
        $revs = $this->_git->getRevs();
        $named = array();
        if( count($revs) > 0 )
        {
            foreach( $revs as $value )
            {
                $name = $this->_git->getRevName($value);
                $named[] = str_replace("\n", '', strstr($name, ' ') );
            }
            $result = array_combine($revs, $named);
        }
        return( $result );
    }

    public function getHeadName()
    {
        $result = $this->_git->getRevName(self::head);
        if( $result == self::headerr )
        {
            $result = '';
        }
        else
        {
            $result = strstr($result, ' ');
        }
        return( $result );
    }

    public function setRev($id)
    {
        $this->_git->checkout($id);
    }
}