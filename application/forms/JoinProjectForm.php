<?php
class JoinProjectForm extends Zend_Form
{
    public function manualInit($projects)
    {
        $this->setName('joinProjectForm');
        $this->setMethod('post');
        $this->setAction('/project/join/');

        $name = new Zend_Form_Element_Select('name');
        $name->setMultiOptions($projects);
        $name->setLabel('Project to Join');

        $submit = new Zend_Form_Element_Submit('join');
        $submit->setLabel('Join');

        $this->setElements( array(
            $name,
            $submit
        ));
    }
}