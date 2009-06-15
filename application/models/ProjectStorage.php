<?php
class ProjectStorage
{
    public $pid;
    public $owner;
    public $name;

    public function fromArray($data)
    {
        if( is_array($data) && isset($data['pid']) && isset($data['owner']) && isset($data['name']) )
        {
            $this->pid = (int) $data['pid'];
            $this->owner = $data['owner'];
            $this->name = $data['name'];
        }
        else
        {
            throw new Exception('Array to store project in incorrect format');
        }
    }
}