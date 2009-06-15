<?php
require_once MODEL_PATH . 'RawIO.php';
require_once MODEL_PATH . 'Git.php';
require_once MODEL_PATH . 'FileNavigation.php';
require_once FORM_PATH . 'NewFileForm.php';
require_once FORM_PATH . 'NewDirForm.php';
class IndexController extends MainController
{
    const noUserCon = 'user';
    const noUserAct = 'login';
    const noProCon = 'project';
    const noProAct = 'open';

    public function preDispatch()
    {
        if( ! isset( $this->_user->loggedIn ) )
        {
            $this->_logger->log('Forwarding to user', Zend_Log::INFO);
            $this->_forward(self::noUserAct, self::noUserCon);
        }
        else if( ! isset( $this->_storage->project->pid ) )
        {
            $this->_logger->log('Forwarding to project', Zend_Log::INFO);
            $this->_forward(self::noProAct, self::noProCon);
        }
    }

    public function indexAction()
    {
        $request = $this->getRequest();
        $io = new RawIO();
        //handle file navagation
        $validFile = false;
        $fileNavigation = new FileNavigation();
        if( $request->isGet() )
        {
            if( $request->getQuery('updir') != NULL )
            {
                $updirs = (int) $request->getQuery('updir');
                $fileNavigation->upDir($updirs);
            }
            if( $request->getQuery('dir') != NULL )
            {
                $fileNavigation->enterDir( $request->getQuery('dir') );
            }
            if( $request->getQuery('file') != NULL )
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
