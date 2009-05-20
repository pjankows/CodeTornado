<?php
class NewProjectForm extends Zend_Form
{
    public function init()
    {
        $this->setName('newProjectForm');
        $this->setMethod('post');
        $this->setAction('/project/new/');

        $name = new Zend_Form_Element_Text('name');
        $name->setLabel('New Project Name');
        $name->addValidator('Alnum', false);
        //$name->addValidator('Regex', false, array('pattern' => '/[\w]/') );

        $submit = new Zend_Form_Element_Submit('create');
        $submit->setLabel('Create');

        $this->setElements( array(
            $name,
            $submit
        ));
    }
}