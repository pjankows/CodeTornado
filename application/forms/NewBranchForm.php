<?php
class NewBranchForm extends Zend_Form
{
    const pattern = '/^[[:alnum:]]+[\.[:alnum:]]*$/';

    public function init()
    {
        parent::init();
        $this->setName('newBranchForm');
        $this->setMethod('post');
        $this->setAction('ajax/newbranch/');

        $name = new Zend_Form_Element_Text('name_branch');
        $name->setLabel('Enter new branch name');
        $name->addValidator('StringLength', true, array(1, 128));
        $name->addValidator('Regex', true, array('pattern' => self::pattern) );

        $cancel = new Zend_Form_Element_Button('cancel_branch');
        $cancel->setLabel('Cancel');
        $cancel->setAttrib('onclick', 'hideNewBranch()');
        $cancel->setAttrib('class', 'button');

        $submit = new Zend_Form_Element_Submit('new_branch');
        $submit->setLabel('New');
        $submit->setAttrib('class', 'button');

        $this->setElements( array(
            $name,
            $cancel,
            $submit
        ));
    }
}