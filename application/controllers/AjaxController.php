<?php
require_once MODEL_PATH . 'FileNavigation.php';
require_once FORM_PATH . 'NewFileForm.php';
require_once FORM_PATH . 'NewDirForm.php';
class AjaxController extends MainController
{
    /**
     * Temporary action while siple ajax in the prototype is based on innerHTML
     * TODO: rewrite all actions to use JSON and remove this method
    */
    public function postDispatch()
    {
        $this->_helper->layout->disableLayout();
    }

    /**
     * Check if a user is logged in and a project is set as active
    */
    private function _check()
    {
        if( $this->_user->loggedIn == false || $this->_project->active == false )
        {
            throw new Exception('AJAX: User or project not active in session');
        }
    }

    /**
     * General case new file or dir method used by newfile/newdir actions
    */
    private function _newFileDir($type)
    {
        if( $this->_user->loggedIn == false || $this->_project->active == false )
        {
            throw new Exception('User not logged in or project not selected');
        }
        $this->_helper->layout->disableLayout();
        $fileNavigation = new FileNavigation( $this->_project->getPath(), $this->_user->getPath() );
        $newFileForm = new NewFileForm();
        $request = $this->getRequest();
        if( $request->isPost() )
        {
            $post = $request->getPost();
            if( $newFileForm->isValid($post) )
            {
                $post['type'] = $type;
                $fileNavigation->newFile( $post );
            }
        }
        $this->view->path = '/' . $fileNavigation->getDir();
        $this->view->files = $fileNavigation->ls();
    }

    public function enterdirAction()
    {
        $this->_helper->layout->disableLayout();
        if( $request->getQuery('dir') != null )
        {
            $fileNavigation->enterDir( $request->getQuery('dir') );
        }
    }

    private function newFileDir( $form, $newMethod )
    {
        $this->_check();
        $fileNavigation = new FileNavigation( $this->_project->getPath(), $this->_user->getPath() );
        $request = $this->getRequest();
        if( $request->isPost() )
        {
            $post = $request->getPost();
            if( $form->isValid($post) )
            {
                $fileNavigation->$newMethod($post);
            }
        }
        $this->view->path = '/' . $fileNavigation->getDir();
        $this->view->files = $fileNavigation->ls();
    }

    public function newfileAction()
    {
        $this->newFileDir( new NewFileForm(), 'newfile' );
    }

    public function newdirAction()
    {
        $this->newFileDir( new NewDirForm(), 'newdir' );
    }
}
