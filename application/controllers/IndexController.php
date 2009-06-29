<?php
require_once MODEL_PATH . 'RawIO.php';
require_once MODEL_PATH . 'Git.php';
require_once MODEL_PATH . 'Status.php';
require_once MODEL_PATH . 'Remotes.php';
require_once MODEL_PATH . 'FileNavigation.php';
require_once MODEL_PATH . 'BranchNavigation.php';
require_once MODEL_PATH . 'HistoryNavigation.php';
require_once FORM_PATH . 'NewFileForm.php';
require_once FORM_PATH . 'NewDirForm.php';
require_once FORM_PATH . 'NewBranchForm.php';
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
        $result = '';
        $request = $this->getRequest();
        $io = new RawIO();
        //handle file navagation
        $validFile = false;
        $fileNavigation = new FileNavigation();
        $branchNavigation = new BranchNavigation();
        $historyNavigation = new HistoryNavigation();
        $remotes = new Remotes();
        $remotes->setUid( $this->_user->loggedIn->uid );
        $status = new Status();
        $status->setUid( $this->_user->loggedIn->uid );

        $git = new Git();
        if( $request->isGet() )
        {
            //===begin file navigation===
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
                    $status->addStatus('open: /' . $fileNavigation->getDir() . $request->getQuery('file'));
                }
            }
            //===end file navigation===
            if( $request->getQuery('branch') != NULL )
            {
                $result = $branchNavigation->setBranch( $request->getQuery('branch') );
                $status->addStatus('branch: ' . $request->getQuery('branch'));
            }
            if( $request->getQuery('sha') != NULL )
            {
                $result = $historyNavigation->setRev( $request->getQuery('sha') );
                $status->addStatus('history: ' . $request->getQuery('sha'));
            }
            //===begin merger===
            if( $request->getQuery('merge') != NULL )
            {
                $result = $git->merge( $request->getQuery('merge') );
                $status->addStatus('merge: ' . $request->getQuery('merge'));
            }
            if( $request->getQuery('pull') != NULL )
            {
                $result = $remotes->pullRemote( $request->getQuery('pull') );
                $status->addStatus('pull: ' . $request->getQuery('pull'));
            }
            if( $request->getQuery('avail') != NULL )
            {
                $result = $remotes->addRemote( $request->getQuery('avail') );
                $status->addStatus('add remote: ' . $request->getQuery('avail'));
            }
            //===end merger===
        }

        if( isset($_POST['code']) )
        {
            $code = $_POST['code'];
            if( $io->getFile() !== NULL )
            {
                $io->saveContent($code);
                $status->addStatus('save: ' . $io->getFile());
            }
            if( isset( $_POST['commitMessage'] ) && ($_POST['commitMessage'] != '') )
            {
                $msg = $_POST['commitMessage'];
                $result = $git->autoCommit($msg);
                $status->addStatus('commit: ' . $msg);
            }
        }
        $this->view->result = $result;
        $this->view->content = $io->getContent();

        $this->view->editing = $io->getFile();
        $this->view->path = '/' . $fileNavigation->getDir();
        $this->view->files = $fileNavigation->ls();
        $this->view->branch = $branchNavigation->getActiveBranch();
        $this->view->branches = $branchNavigation->getBranches();
        $this->view->history = $historyNavigation->getHistory();
        $this->view->headName = $historyNavigation->getHeadName();

        $this->view->status = $status->getStatusMessages();
        $this->view->avail = $remotes->getRepos();
        $this->view->remotes = $remotes->getRemotes();

        $this->view->newFileForm = new NewFileForm();
        $this->view->newDirForm = new NewDirForm();
        $this->view->newBranchForm = new NewBranchForm();
    }
}
