<?php
require_once MODEL_PATH . 'RawIO.php';
require_once MODEL_PATH . 'Git.php';
require_once MODEL_PATH . 'FileNavigation.php';
require_once FORM_PATH . 'NewFileForm.php';
class IndexController extends MainController
{
    public function indexAction()
    {
        $request = $this->getRequest();
        //$testfile = APPLICATION_PATH . '/../data/test.php';

        $io = new RawIO();
        //$fileNavigation = new FileNavigation($this->_project, $this->_user);
        //$this->view->files = $fileNavigation->ls();

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
                        $file = $fileNavigation->getPath() . '/' . $request->getQuery('file');
                        $io->setFile($file);

                    }
                }
            }

            $this->view->editing = $io->getFile();
            $this->view->path = '/' . $fileNavigation->getDir();
            $this->view->files = $fileNavigation->ls();
            $this->view->newFileForm = new NewFileForm();
        }

        //save changes and do a commit
        //if( isset( $_POST['commitMessage'] ) && isset($_POST['code']) )
        if( isset($_POST['code']) )
        {
            //$msg = $_POST['commitMessage'];
            $code = $_POST['code'];
            $io->saveContent($code);
            //$git = new Git();
            //$result = $git->autoCommit($msg);
            //$this->view->result = $result;
        }
        $this->view->content = $io->getContent();
    }
}
