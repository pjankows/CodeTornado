<?php
class FileNavigation
{
    private $_session;
    private $_pathBase;
    private $_path;
    private $_dir;
    private $_dirArray = array();
    private $_files;
    private $_dirs;
    private $_file;

    /**
     * Constructor recreates current directory array
    */
    function __construct($projectPath, $userPath)
    {
        $this->_pathBase = $projectPath . $userPath;
        $this->_session = new Zend_Session_Namespace('FileNav');
        if( isset( $this->_session->dirArray ) )
        {
            $this->_dirArray = $this->_session->dirArray;
        }
        $this->_updatePath();
    }

    /**
     * Update the _path variable and store the directory array in the session
    */
    private function _updatePath()
    {
        $this->_session->dirArray = $this->_dirArray;
        $this->_path = $this->_pathBase;
        $this->_dir = '';
        foreach( $this->_dirArray as $dir )
        {
            $this->_path .= $dir . '/';
            $this->_dir .= $dir . '/';
        }
    }

    /**
     * Enter a directory that exists in the current listing
    */
    public function enterDir($dirname)
    {
        $this->ls();
        $key = array_search($dirname, $this->_dirs);
        if( $key !== false )
        {
            $this->_dirArray[] = $this->_dirs[$key];
            $this->_updatePath();
        }
        else
        {
            throw new Exception('Error: The specified directory does not exist');
        }
    }

    public function validFile($filename)
    {
        $result = false;
        $this->ls();
        $key = array_search($filename, $this->_files);
        if( $key !== false )
        {
            $result = true;
        }
        else
        {
            throw new Exception('Error: Specified file is not valid');
        }
        return( $result );
    }

    public function newFile($formData)
    {
        if( is_array($formData) && isset($formData['type']) && isset($formData['name']) )
        {
            if( $formData['type'] == NewFileForm::typeFile )
            {
                touch( $this->getPath() . $formData['name'] );
            }
            else if( $formData['type'] == NewFileForm::typeDir )
            {
                mkdir( $this->getPath() . $formData['name'] );
            }
            else
            {
                throw new Exception('Invalid type specified');
            }
        }
        else
        {
            throw new Exception('New file form data in incorrect format');
        }
    }

    /**
     * Go up dir equals cd ..
    */
    public function upDir()
    {
        if( count($this->_dirArray) > 0 )
        {
            array_pop( $this->_dirArray );
            $this->_updatePath();
        }
    }

    /**
     * Get method for the _path
    */
    public function getPath()
    {
        return( $this->_path );
    }

    /**
     * Get the dir without the path base and project/user directory
    */
    public function getDir()
    {
        return( $this->_dir );
    }

    /**
     * Return the listing of current directory. Also updates _dirs and _files
    */
    public function ls()
    {
        $result = array( 'dirs' => array(), 'files' => array() );
        $content = scandir( $this->_path );
        if( is_array($content) && count($content) > 0 )
        {
            foreach( $content as $name )
            {
                if( $name !== '.' && $name !== '..' )
                {
                    if( is_dir($this->_path . '/' .$name) )
                    {
                        $result['dirs'][] = $name;
                    }
                    else
                    {
                        $result['files'][] = $name;
                    }
                }
            }
        }
        //store the values in the model for further request
        $this->_files = $result['files'];
        $this->_dirs = $result['dirs'];
        return( $result );
    }
}