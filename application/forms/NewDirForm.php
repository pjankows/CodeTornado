<?php
class NewDirForm extends Zend_Form
{
    const typeFile = 1;
    const typeDir = 2;
    const pattern = '/^[[:alnum:]]+[\.[:alnum:]]*$/';

    public function init()
    {
        parent::init();
        $this->setName('newDirForm');
        $this->setMethod('post');
        $this->setAction('ajax/newdir/');

        $name = new Zend_Form_Element_Text('name_dir');
        $name->setLabel('Enter new directory name');
        $name->addValidator('StringLength', true, array(1, 128));
        $name->addValidator('Regex', true, array('pattern' => self::pattern) );

        $cancel = new Zend_Form_Element_Button('cancel_dir');
        $cancel->setLabel('Cancel');
        $cancel->setAttrib('onclick', 'hideNewDir()');
        $cancel->setAttrib('class', 'button');

        $submit = new Zend_Form_Element_Submit('new_dir');
        $submit->setLabel('New');
        $submit->setAttrib('class', 'button');

        $this->setElements( array(
            $name,
            $cancel,
            $submit
        ));
    }
}