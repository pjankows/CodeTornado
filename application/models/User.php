<?php
require_once MODEL_PATH . 'Brute.php';
/**
 * Manage the user login, registration and session persistence.
*/
class User extends DbModel
{
    const OK =  0;
    const BAD = 1;
    const BLOCK = 2;

    private $_auth;

    public $loggedIn = false;

    /**
     * @Override
    */
    protected function init()
    {
        $this->_auth = Zend_Auth::getInstance();
        if( $this->_auth->hasIdentity() );
        {
            $this->loggedIn = $this->_auth->getIdentity();
        }
    }

    public function logout()
    {
        $this->_auth->clearIdentity();
        $this->_auth->getStorage()->clear();
        Zend_Session::expireSessionCookie();
    }

    /**
     * Preform a login authentication attempt. Includes basic brute forcing protection.
    */
    public function login($login, $pass)
    {
        if( ( $login == '' ) ||  ( $pass == '' ) )
        {
            $result = false;
        }
        else
        {
            $protection = new Brute();
            if( $protection->testIp() )
            {
                $salt = Zend_Registry::get('salt');
                //table users field user, password hashed with given function
                $adapter = new Zend_Auth_Adapter_DbTable( $this->_db, 'users',  'user', 'password',
                'SHA1( CONCAT( ?, "' . $salt . '", salt ) ) AND active=1');
                $adapter->setIdentity($login);
                $adapter->setCredential($pass);
                $result = $this->_auth->authenticate( $adapter );
                if( $result->isValid() )
                {
                    $storage = $this->_auth->getStorage();
                    $storage->write( $adapter->getResultRowObject( array('uid', 'user', 'name', 'email') ) );
                    $result = self::OK;
                }
                else
                {
                    $protection->registerBad();
                    $result = self::BAD;
                }
            }
            else
            {
                //the protection has locked this IP address
                $result = self::BLOCK;
            }
        }
        return( $result );
    }

    /**
     * Register with given data from the registration form
    */
    public function register($data)
    {
        $result = 0;
        if( ! is_array($data) )
        {
            throw new Exception('Error: Data passed to insert is not an array');
        }
        if( isset( $data['user'] ) && isset( $data['password'] ) &&
            isset( $data['name'] ) && isset( $data['email'] ) )
        {
            unset( $data['hash'] );
            unset( $data['register'] );
            $salt = Zend_Registry::get('salt');
            $data['salt'] = sha1( rand(0,10000) + time() );
            $data['password'] = sha1( $data['password'] . $salt  . $data['salt'] );
            $result = $this->_db->insert('users', $data);
        }
        else
        {
            throw new Exception('Error: Required registration data not present in array');
        }
        return( $result );
    }

    /**
     * Returns false if the given username exists
     * @TODO: Replace with new form validotors introduced in Zend Framework 1.8
    */
    public function freeUsername($user)
    {
        $sql = 'SELECT count(user) FROM users WHERE user=?';
        $result = $this->_db->fetchOne( $sql, array($user) );
        if( $result == 0 )
        {
            $result = true;
        }
        else
        {
            $result = false;
        }
        return($result);
    }

    public function getPath()
    {
        $result = false;
        if( $this->loggedIn )
        {
            $result = $this->loggedIn->uid . '/';
        }
        return( $result );
    }
}
