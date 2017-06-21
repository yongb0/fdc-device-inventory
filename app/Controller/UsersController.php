<?php
App::uses('AppController', 'Controller');

class UsersController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('login','logout');
    }

    private function _set_meta ($title,$description,$keywords) {
        $this->set('meta_title', $title);
        $this->set('meta_description', $description);
        $this->set('meta_keywords', $keywords);
    }

    public function index() {
        $this->_set_meta('Users','','');
        $this->User->recursive = 0;
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
            // $this->Flash->success(__('User deleted'));
            return $this->redirect(array('action' => 'index'));
        }
        // $this->Flash->error(__('User was not deleted'));
        return $this->redirect(array('action' => 'index'));
    }

    public function login() {
        $this->_set_meta('Login','','');
        $this->layout = 'login';
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                return $this->redirect($this->Auth->redirectUrl());
            }
            $this->set('notification', array('Invalid username and password', 'alert alert-danger'));
        }
    }

    public function logout() {
        return $this->redirect($this->Auth->logout());
    }

}
?>