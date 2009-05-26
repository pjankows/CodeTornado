<?php
require_once MODEL_PATH . 'FileNavigation.php';
require_once FORM_PATH . 'NewFileForm.php';
class AjaxController extends MainController
{
    /**
     * General case new file or dir method used by newfile/newdir actions
    */
    private function _newFileDir($type)
    {
        if( $this->_user->loggedIn === false || $this->_project->active === false )
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

    public function newfileAction()
    {
        $this->_newFileDir( NewFileForm::typeFile );
    }

    public function newdirAction()
    {
        $this->_newFileDir( NewFileForm::typeDir );
    }
}
