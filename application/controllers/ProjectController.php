<?php
require_once FORM_PATH . 'NewProjectForm.php';
require_once FORM_PATH . 'JoinProjectForm.php';
class ProjectController extends MainController
{
    const MUST_LOGIN = 'Must be logged in to create a project';
    const NAME_TAKEN = 'Project with the given name already exists';
    const FORM_INVALID = 'Please correct the form';

    public function newAction()
    {
        $form = new NewProjectForm();
        $request = $this->getRequest();
        if( $request->isPost() && $request->getPost('create') == 'Create' )
        {
            $post = $request->getPost();
            if( $form->isValid($post) )
            {
                if( $this->_user->loggedIn !== false )
                {
                    if( $this->_project->freeProjectName($post['name']) )
                    {
                        $this->_project->newProject($post['name'], $this->_user->loggedIn->uid);
                        $this->_redirect('/');
                    }
                    else
                    {
                        $this->view->newMsg = self::NAME_TAKEN;
                    }
                }
                else
                {
                    $this->view->newMsg = self::MUST_LOGIN;
                }
            }
            else
            {
                $this->view->newMsg = self::FORM_INVALID;
            }
        }
        $this->view->form = $form;
    }

    public function joinAction()
    {
        $projects = $this->_project->getProjectsToJoin();
        $form = new JoinProjectForm();
        $form->manualInit($projects);
        $request = $this->getRequest();
        if( $request->isPost() && $request->getPost('join') == 'Join' )
        {
            $post = $request->getPost();
            if( $form->isValid($post) )
            {
                if( $this->_user->loggedIn !== false )
                {
                    $this->_project->joinProject($post['name']);
                    $this->_redirect('/');
                }
            }
        }
        //print_r($projects);
        $this->view->form = $form;
    }

    public function selectAction()
    {
        $this->view->projects = $this->_project->getJoinedProjects();
        $request = $this->getRequest();
        if( $request->isGet() && $request->getQuery('pro') !== null )
        {
            $project = $request->getQuery('pro');
            $this->_project->selectProject($project);
            $this->_redirect('/');
        }
    }
}