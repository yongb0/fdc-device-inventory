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
        $this->set('actionUrl', Router::url(array('controller' => strtolower($this->name), 'action' => 'edit')));
    }

    public function view($id = null) {
        $this->_set_meta('Users','','');
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }

        $this->set('user', $this->User->findById($id));
    }

    public function add() {
        $this->_set_meta('Add User','','');
        if ($this->request->is('post')) {
            $this->User->create();
            $this->User->set($this->request->data);
            // print_r($this->User->validationErrors);
            
            if ($this->User->save($this->request->data)) {
                return $this->redirect(array('action' => 'index'));
            }
            $this->set('notification', array('The user could not be saved. Please, try again.', 'alert alert-danger'));
        }
    }

    public function edit($userID = null) {
        $this->_set_meta('Users','','');
        $this->User->id = $userID;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('The user has been saved'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(
                __('The user could not be saved. Please, try again.')
            );
        } else {
            // $this->request->data = $this->User->findByUserId($userID);
            print_r($this->User->findByUserId($userID));
            // unset($this->request->data['User']['password']);
        }
    }

    public function delete($id = null) {
        $this->_set_meta('Users','','');
        $this->request->allowMethod('post');
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->User->delete()) {
            $this->Flash->success(__('User deleted'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Flash->error(__('User was not deleted'));
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