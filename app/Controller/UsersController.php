<?php
App::uses('AppController', 'Controller');

class UsersController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('login','logout','account');
    }

    private function _set_meta ($title,$description,$keywords) {
        $this->set('meta_title', $title);
        $this->set('meta_description', $description);
        $this->set('meta_keywords', $keywords);
    }

    public function index() {
        $this->_set_meta('Users','','');
        $this->User->recursive = 0;
        if($this->request->is('get') && isset($this->request->query['search'])){
            $keyword = $this->request->query['keyword']; 
            $this->paginate = array('User' => 
                array(
                    'conditions' => array(
                        'OR' => array(
                           'user_id LIKE' => "%".$keyword."%",
                           'username LIKE' => "%".$keyword."%",
                           'employee_id LIKE' => "%".$keyword."%",
                           'name LIKE' => "%".$keyword."%"
                        )
                    ),
                    'limit' => 10
                )
            );
        }
       
        $this->set('users', $this->paginate());
        $this->set('actionEdit', Router::url(array('controller' => strtolower($this->name), 'action' => 'edit')));
        $this->set('actionView', Router::url(array('controller' => strtolower($this->name), 'action' => 'view')));
    }

    public function view($id = null) {
        $this->_set_meta('Users','','');
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }

        $this->set('user', $this->User->findByUserId($id));
    }

    public function add() {
        $this->_set_meta('Add User','','');
        if ($this->request->is('post')) {
            $this->User->create();
            $this->User->set($this->request->data);
            if ($this->User->save($this->request->data)) {
                return $this->redirect(array('action' => 'index'));
            }
            $this->set('notification', array('The user could not be saved. Please see errors', 'alert alert-danger'));
        }
    }

    public function edit($id = null) {
        $this->_set_meta('Edit User','','');
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->User->save($this->request->data)) {
                $this->set('notification', array('The user has been saved', 'alert alert-success'));
            }
            else{
                $this->set('notification', array('The user could not be saved. Please, try again.', 'alert alert-danger'));
            }
        }
        $userData = $this->User->findByUserId($id);
        $this->set('user', $userData);

    }

    public function delete($id = null) {
        $this->_set_meta('Users','','');
        $this->request->allowMethod('post');
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->User->delete()) {
            return $this->redirect(array('action' => 'index'));
        }
        return $this->redirect(array('action' => 'index'));
    }

    public function login() {
        $this->_set_meta('Login','','');
        $this->layout = 'login';
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                if($this->Auth->User('status')==1){
                    $this->set('notification', array('Your account has been deactivated.', 'alert alert-danger'));
                    $this->Auth->logout();
                }
                else{
                    if($this->Auth->User('level')==0){
                        return $this->redirect($this->Auth->redirectUrl());
                    }else{
                        return $this->redirect(array('controller' => 'devices', 'action' => 'index'));
                    }
                }
            }else{
                $this->set('notification', array('Invalid username and password', 'alert alert-danger'));
            }
        }
    }

    public function account() {
        $this->_set_meta('Profile','','');
        $this->User->id = $this->Auth->User('user_id');
        if($this->userEmployee()){
            $this->layout='employee';
        }
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->User->save($this->request->data)) {
                $this->set('notification', array('Successfully updated', 'alert alert-success'));
            }
            else{
                $this->set('notification', array('Error in updating. Please, try again.', 'alert alert-danger'));
            }
        }
    }

    public function search() {
        $this->autoRender = false;
        if( $this->request->is('ajax') ) {
            $keyword = $this->request->data('keyword');
    
            $users = $this->User->find('all',
                array(
                    'recursive' =>  -1,
                    'conditions' => array(
                       "OR" => array(
                            "name LIKE" => "%".$this->request->data['keyword']."%",
                            "username LIKE" => "%".$this->request->data['keyword']."%",
                            "employee_id LIKE" => "%".$this->request->data['keyword']."%",
                        ) 
                    ),
                    'fields' => array('username','name','employee_id','user_id')
                )
            );
            return json_encode($users);
        }
    }

    public function search2() {
        $this->view='index';
        if( $this->request->is('ajax') ) {
            $users = $this->User->find('all',
                array(
                    'recursive' =>  -1,
                    'conditions' => array(
                       "OR" => array(
                            "name LIKE" => "%".$this->request->data['keyword']."%",
                            "username LIKE" => "%".$this->request->data['keyword']."%",
                            "employee_id LIKE" => "%".$this->request->data['keyword']."%",
                        ) 
                    )
                )
            );
            
        }
        $this->set('users', $this->paginate());
        return json_encode($this->paginate());
    }

    public function logout() {
        return $this->redirect($this->Auth->logout());
    }

}
?>