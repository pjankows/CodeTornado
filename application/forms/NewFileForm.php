<?php
class NewFileForm extends Zend_Form
{
    const typeFile = 1;
    const typeDir = 2;
    const pattern = '/^[[:alnum:]]+[\.[:alnum:]]*$/';

    public function init()
    {
        $this->setName('newFileForm');
        $this->setMethod('post');
        $this->setAction('/');

        $type = new Zend_Form_Element_Radio('type');
        $type->setLabel('New file type');
        $type->setSeparator('');
        $type->setMultiOptions( array( self::typeFile => 'file', self::typeDir => 'dir' ) );

        $name = new Zend_Form_Element_Text('name');
        $name->setLabel('New file name');
        $name->addValidator('StringLength', true, array(1, 128));
        $name->addValidator('Regex', true, array('pattern' => self::pattern) );

        $submit = new Zend_Form_Element_Submit('new');
        $submit->setLabel('New');

        $this->setElements( array(
            $type,
            $name,
            $submit
        ));
    }
}