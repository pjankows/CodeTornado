<?php
require_once MODEL_PATH . 'RawIO.php';
require_once MODEL_PATH . 'Git.php';
require_once MODEL_PATH . 'FileNavigation.php';
require_once FORM_PATH . 'NewFileForm.php';
require_once FORM_PATH . 'NewDirForm.php';
class IndexController extends MainController
{
    public function indexAction()
    {
        $request = $this->getRequest();
        $io = new RawIO();
        //handle file navagation
        $validFile = false;
        if( $this->_user->loggedIn != false && $this->_project->active != false )
        {
            $fileNavigation = new FileNavigation( $this->_project->getPath(), $this->_user->getPath() );
            if( $request->isGet() )
            {
                if( $request->getQuery('updir') != null )
                {
                    $updir = (int) $request->getQuery('updir');
                    for($i=0; $i<$updir; ++$i )
                    {
                        $fileNavigation->upDir();
                    }
                }
                if( $request->getQuery('dir') != null )
                {
                    $fileNavigation->enterDir( $request->getQuery('dir') );
                }
                if( $request->getQuery('file') != null )
                {
                    if( $fileNavigation->validFile( $request->getQuery('file') ) )
                    {
                        $io->setFile( $fileNavigation->getPath(), $request->getQuery('file') );
                    }
                }
            }

            $this->view->editing = $io->getFile();
            $this->view->path = '/' . $fileNavigation->getDir();
            $this->view->files = $fileNavigation->ls();
            $this->view->newFileForm = new NewFileForm();
            $this->view->newDirForm = new NewDirForm();
        }

        if( isset($_POST['code']) )
        {
            $code = $_POST['code'];
            $io->saveContent($code);
            if( isset( $_POST['commitMessage'] ) )
            {
                $msg = $_POST['commitMessage'];
                $git = new Git( $this->_project->getPath() . $this->_user->getPath() );
                $result = $git->autoCommit($msg);
                $this->view->result = $result;
            }
        }
        $this->view->content = $io->getContent();
    }
}
