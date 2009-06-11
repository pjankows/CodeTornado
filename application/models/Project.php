<?php
require_once MODEL_PATH . 'Git.php';
class Project extends DbModel
{
    const NO_USER = 'No user logged in';

    private $_session;
    private $_uid = false;
    private $_userPath = false;
    private $_userName = false;
    private $_userEmail = false;

    public $active = null;

    /**
     * Restore the active project id form the session
    */
    protected function init()
    {
        //read the session variable if exists
        $this->_session = new Zend_Session_Namespace('Project');
        $this->_restore();
    }

    /**
     * Set user data from model. Called by MainController init method.
    */
    public function setUserData($uid, $userPath, $userName, $userEmail)
    {
        $this->_uid = $uid;
        $this->_userPath = $userPath;
        $this->_userName = $userName;
        $this->_userEmail = $userEmail;
    }

    /**
     * Setup new project folder and database entry
    */
    public function newProject($name)
    {
        $key = false;
        if( $this->_uid == false )
        {
            throw new Exception(NO_USER);
        }
        $data = array('owner' => $this->_uid, 'name' => $name );
        $result = $this->_db->insert('projects', $data);
        //returns the number of rows inserted
        if( $result == 1 )
        {
            //get the id of the inserted row - the project id (pid)
            $id = $this->_db->lastInsertId();
            //set the project id in object for internal usage and session storage later
            $data['pid'] = $id;
            $this->_store($data);

            if( file_exists( $this->getPath() ) )
            {
                throw new Exception('Project folder already exists');
            }
            if( ! mkdir( $this->getPath() ) )
            {
                throw new Exception('Unable to create the project directory:' . $this->getPath() );
            }
            //init the user repo while joining the project
            $this->joinProject($id);
        }
        return( $key );
    }

    /**
     * Associate user with the project. Owner is automatically assigned by newProject calling this method.
     * This method also creates the git repository and assigns basic configuration
    */
    public function joinProject($pid)
    {
        if( $this->_uid == false )
        {
            throw new Exception(NO_USER);
        }
        $data = array( 'pid' => $pid , 'uid' => $this->_uid );
        $result = $this->_db->insert('user_project', $data);
        //returns the number of rows inserted
        if($result == 1)
        {
            //select the project as the active project once the required db entry exists
            $this->selectProject($pid);
            if( file_exists( $this->getPath() . $this->_userPath ) )
            {
                throw new Exception('User directory in project folder already exists');
            }
            if( ! mkdir( $this->getPath() . $this->_userPath ) )
            {
                throw new Exception('Unable to create a user directory in the project folder');
            }
            //===== GIT INIT =====
            $git = new Git( $this->getPath() . $this->_userPath );
            $git->initRepo( $this->_userName, $this->_userEmail );
            //===== GIT INIT =====
        }
    }

    /**
     * Select a project and make it active and store in a session variable for next requests
    */
    public function selectProject($pid)
    {
        if( $this->_uid == false )
        {
            throw new Exception(NO_USER);
        }
        $sql = 'SELECT projects.pid, projects.name, projects.owner FROM projects, user_project WHERE
            user_project.uid=? AND user_project.pid=? AND projects.pid = user_project.pid';
        $result = $this->_db->fetchRow( $sql, array( $this->_uid, $pid ) );
        if( $result['pid'] = $pid )
        {
            $this->_store($result);
        }
        else
        {
            throw new Exception('User not a member of project');
        }
    }

    /**
     * Remove the project selection
    */
    public function deselectProject()
    {
        unset( $this->_session->active );
        $this->active = false;
    }

    /**
     * Returns false if the given project name exists
     * TODO: Replace with new form validotors introduced in Zend Framework 1.8
    */
    public function freeProjectName($name)
    {
        $sql = 'SELECT count(name) FROM projects WHERE name=?';
        $result = $this->_db->fetchOne( $sql, array($name) );
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

    /**
     * Get the projects which the actively selected user has NOT joined
    */
    public function getProjectsToJoin()
    {
        $sql = 'SELECT projects.pid, name FROM projects, user_project WHERE
            projects.pid = user_project.pid AND projects.pid NOT IN (
                SELECT projects.pid FROM projects, user_project WHERE
                    projects.pid = user_project.pid AND user_project.uid = ?
                )';
        $result = $this->_db->fetchPairs( $sql, array( $this->_uid ));
        return( $result );
    }

    /**
     * Get the projects which the actively selected user has joined
    */
    public function getJoinedProjects()
    {
        if( $this->_uid == false )
        {
            throw new Exception(NO_USER);
        }
        $sql = 'SELECT name, projects.pid FROM projects, user_project WHERE
            projects.pid = user_project.pid AND user_project.uid=?';
        $result = $this->_db->fetchAssoc( $sql, $this->_uid );
        return( $result );
    }

    /**
     * Return the project path including the data path
    */
    public function getPath()
    {
        if( $this->active == false )
        {
            throw new Exception('No project selected - no path can be returned');
        }
        return( DATA_PATH . $this->active->pid . '/' );
    }

    /**
     * Store the active project data in the session. Also sets class variables to these values
    */
    private function _store( $data )
    {
        if( is_array($data) && isset($data['pid']) && isset($data['owner']) && isset($data['name']) )
        {
            $this->_session->active = (object) $data;
            $this->_restore();
        }
        else
        {
            throw new Exception('Project data to store in session is incomplete');
        }
    }

    /**
     * Restore the active project data to this class variables from the session
    */
    private function _restore()
    {
        if( isset( $this->_session->active ) )
        {
            $this->active = $this->_session->active;
        }
    }
}