<?php
class PathStorage
{
    public $project;
    public $user;
    public $dirArray;
    public $fileName;

    public function fromPid($pid)
    {
        $this->project = $pid . '/';
    }

    public function fromUid($uid)
    {
        $this->user = $uid . '/';
    }

    public function setDirArray($array)
    {
        if( is_array($array) )
        {
            $this->_dirArray = $array;
        }
        else
        {
            throw new Exception('Trying to set non-array value to dirArray');
        }
    }

    public function setFileName($fileName)
    {
        if( strstr($fileName, '/') !== FALSE )
        {
            throw new Exception('Illegal character present in file name');
        }
        $this->fileName = $fileName;
    }
}