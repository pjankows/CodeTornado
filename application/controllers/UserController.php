<?php
require_once MODEL_PATH . 'Status.php';
require_once FORM_PATH . 'RegisterForm.php';
require_once FORM_PATH . 'LoginForm.php';
class UserController extends MainController
{
    const LOG_OK = 'You are now logged in.';
    const LOG_BAD = 'Login failed. Bad username or password.';
    const LOG_BLOCK = 'Your IP address has exceded bad login attempts. Try again tomorrow.';

    const LOGOUT = 'You have logged out.';

    const REG_OK = 'Your account has been created. You can now log in.';
    const REG_INVALID = 'Please correct the registration form.';
    const REG_TAKEN = 'This username is already taken.';
    const REG_ERROR = 'Registration failed.';


    public function loginAction()
    {
        $form = new LoginForm();
        $request = $this->getRequest();
        if( $request->isPost() && $request->getPost('login') == 'Login' )
        {
            $post = $request->getPost();
            if( $form->isValid($post) )
            {
                $result = $this->_user->login($post['user'], $post['password']);
                //print_r($result);
                switch( $result )
                {
                    case User::OK:
                            $this->view->loginMsg = self::LOG_OK;
                            $this->_redirect('/');
                        break;
                    case User::BAD:
                            $this->view->loginMsg = self::LOG_BAD;
                        break;
                    case User::BLOCK:
                            $this->view->loginMsg = self::LOG_BLOCK;
                        break;
                }
            }
        }
        $this->view->form = $form;
    }

    public function logoutAction()
    {
        if( isset( $this->_user->loggedIn->uid ) )
        {
            $status = new Status();
            $status->setUid( $this->_user->loggedIn->uid );
            $status->addStatus('logout');
        }
        $this->_user->logout();
        $this->view->loginMsg = self::LOGOUT;
        $this->_forward('login');
    }

    public function registerAction()
    {
        $form = new RegisterForm();
        $request = $this->getRequest();
        if( $request->isPost() && $request->getPost('register') == 'Register' )
        {
            $post = $request->getPost();
            if( $form->isValid($post) )
            {
                if( $this->_user->freeUsername($post['user']) )
                {
                    $result = $this->_user->register($post);
                    if( $result == 1 )
                    {
                        $this->view->loginMsg = self::REG_OK;
                        $this->_forward('login');
                    }
                    else
                    {
                        $this->view->registerMsg = self::REG_ERROR;
                    }
                }
                else
                {
                    $this->view->registerMsg = self::REG_TAKEN;
                }
            }
            else
            {
                $this->view->registerMsg = self::REG_INVALID;
            }
        }
        $this->view->form = $form;
    }
}