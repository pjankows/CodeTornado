<?php
class RawIO
{
    private $_pathname = NULL;
    private $_name = NULL;
    private $_session;

    function __construct()
    {
        $this->_session = new Zend_Session_Namespace('RawIO');
        if( isset( $this->_session->pathname ) )
        {
            $this->_pathname = $this->_session->pathname;
            $this->_name = $this->_session->name;
        }
    }

    public function getFile()
    {
        return( $this->_name );
    }

    public function setFile($path, $name)
    {
        $this->_pathname = $path . $name;
        $this->_name = $name;
        $this->_session->pathname = $this->_pathname;
        $this->_session->name = $this->_name;
    }

    public function getContent()
    {
        if( $this->_pathname != NULL  )
        {
            $content = file_get_contents($this->_pathname);
            if( $content === false )
            {
                throw new Exception('Error reading file: '. $this->_pathname);
            }
        }
        else
        {
            $content = 'No file selected';
        }
        return( $content );
    }

    public function saveContent( $content )
    {
        if( $this->_pathname != NULL )
        {
            $result = file_put_contents($this->_pathname, $content);
            if( $result === false )
            {
                throw new Exception('Error writing file: ' . $this->_pathname);
            }
        }
        else
        {
            throw new Exception('No file set for writing');
        }
    }
}
