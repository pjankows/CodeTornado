<?php
class LoginForm extends Zend_Form
{
    public function init()
    {
        $this->setName('loginForm');
        $this->setMethod('post');
        $this->setAction('/user/login/');

        $user = new Zend_Form_Element_Text('user');
        $user->setLabel('Username');
        $user->setRequired(true);
        //$user->addValidator('Db_NoRecordExists', true, array('user', 'user') );

        $password = new Zend_Form_Element_Password('password');
        $password->setLabel('Password');
        $password->setRenderPassword(false);
        $password->setRequired(true);

        $submit = new Zend_Form_Element_Submit('login');
        $submit->setLabel('Login');

        //$hash = new Zend_Form_Element_Hash('hash');

        $this->setElements( array(
            $user,
            $password,
            $submit,
            //$hash
        ));
    }
}