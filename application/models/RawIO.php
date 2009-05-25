<?php
class RawIO
{
    private $_file = null;
    private $_session;

    function __construct()
    {
        $this->_session = new Zend_Session_Namespace('RawIO');
        if( isset( $this->_session->filename ) )
        {
            $this->_file = $this->_session->filename;
        }
    }

    public function getFile()
    {
        return( $this->_file );
    }

    public function setFile($filename)
    {
        $this->_file = $filename;
        $this->_session->filename = $filename;
    }

    public function getContent()
    {
        if( $this->_file != null  )
        {
            $content = file_get_contents($this->_file);
            if( $content === false )
            {
                throw new Exception('Error reading file: '. $this->_file);
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
        if( $this->_file != null )
        {
            $result = file_put_contents($this->_file, $content);
            if( $result === false )
            {
                throw new Exception('Error writing file: ' . $this->_file);
            }
        }
        else
        {
            throw new Exception('No file set for writing');
        }
    }
}
