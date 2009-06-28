<?php
class RawIO
{
    private $_pathname;
    private $_name;
    private $_storage;
    //private $_session;

    function __construct()
    {
        $this->_storage = SessionStorage::getInstance();
        if( isset( $this->_storage->path->fileName ) )
        {
            $this->_pathname = $this->_storage->path->filePath;
            $this->_name = $this->_storage->path->fileName;
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
        $this->_storage->path->filePath = $this->_pathname;
        $this->_storage->path->fileName = $this->_name;
    }

    public function getContent()
    {
        if( $this->_pathname != NULL  )
        {
            if( file_exists($this->_pathname) )
            {
                $content = file_get_contents($this->_pathname);
                if( $content === false )
                {
                    throw new Exception('Error reading file: '. $this->_pathname);
                }
            }
            else
            {
                $content = 'File does not exist';
                $this->setFile(NULL, NULL);
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
