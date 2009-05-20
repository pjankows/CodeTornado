<?php
class RegisterForm extends Zend_Form
{
    public function init()
    {
        $this->setName('registerForm');
        $this->setMethod('post');
        $this->setAction('/user/register/');

        $user = new Zend_Form_Element_Text('user');
        $user->setLabel('Username');
        $user->setRequired(true);
        $user->addValidator('Alnum', false);
        $user->addValidator('StringLength', true, array(3, 30) );
        //$user->addValidator('Db_NoRecordExists', true, array('user', 'user') );

        $password = new Zend_Form_Element_Password('password');
        $password->setLabel('Password');
        $password->setRenderPassword(true);
        $password->setRequired(true);
        $password->addValidator('StringLength', true ,array(5, 50) );

        $name = new Zend_Form_Element_Text('name');
        $name->setLabel('Full Name');
        $name->setRequired(true);
        $name->addValidator('StringLength', true, array(3, 128) );

        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('Email');
        $email->setRequired(true);
        $email->addValidator('EmailAddress', true);
        $email->addValidator('StringLength', true, array(3, 128) );

        $submit = new Zend_Form_Element_Submit('register');
        $submit->setLabel('Register');

        $hash = new Zend_Form_Element_Hash('hash');

        $this->setElements( array(
            $user,
            $password,
            $name,
            $email,
            $submit,
            $hash
        ));
    }
}