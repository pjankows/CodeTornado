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

        $name = new Zend_Form_Element_Text('name');
        $name->setLabel('Enter new branch name');
        $name->addValidator('StringLength', true, array(1, 128));
        $name->addValidator('Regex', true, array('pattern' => self::pattern) );

        $cancel = new Zend_Form_Element_Button('cancel');
        $cancel->setLabel('Cancel');
        $cancel->setAttrib('onclick', 'hideNewBranch()');

        $submit = new Zend_Form_Element_Submit('new');
        $submit->setLabel('New');

        $this->setElements( array(
            $name,
            $cancel,
            $submit
        ));
    }
}