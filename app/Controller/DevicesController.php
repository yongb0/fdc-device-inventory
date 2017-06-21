<?php
App::uses('AppController', 'Controller');
App::uses('DeviceCategory', 'Model');

class DevicesController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    private function _set_meta ($title,$description,$keywords) {
        $this->set('meta_title', $title);
        $this->set('meta_description', $description);
        $this->set('meta_keywords', $keywords);
    }

    public function index() {
        $this->_set_meta('Devices','','');
        $this->Device->recursive = 0;
        $this->set('devices', $this->paginate());
        // $this->set('actionEdit', Router::url(Router::url(array('controller' => strtolower($this->name), 'action' => 'edit'))));
        // $this->set('actionView', Router::url(Router::url(array('controller' => strtolower($this->name), 'action' => 'view'))));
    }

    public function view($id = null) {
        $this->_set_meta('Users','','');
        $this->Device->id = $id;
        if (!$this->Device->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }

        $this->set('user', $this->User->findByUserId($id));
    }

    public function add() {
        $categories = new DeviceCategory();
        $this->_set_meta('Add Device','','');
        $this->set(
            'categories',
            $categories->find('all',[
                'recursive' => -1,
                'contain' => [],
                'fields' => ['name','category_id']
            ])
        );

        if ($this->request->is('post')) {
            $this->Device->create();
            $this->Device->set($this->request->data);
            if ($this->Device->save($this->request->data)) {
                return $this->redirect(array('action' => 'index'));
            }
        
            $this->set('notification', array('The category could not be saved. Please see errors', 'alert alert-danger'));
        }
    }

    public function edit($id = null) {
        $this->_set_meta('Edit Device Category','','');
        $this->DeviceCategory->id = $id;
        if (!$this->DeviceCategory->exists()) {
            throw new NotFoundException(__('Invalid Category'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->DeviceCategory->save($this->request->data)) {
                $this->set('notification', array('The category has been saved', 'alert alert-success'));
            }
            else{
                $this->set('notification', array('The category could not be saved. Please, try again.', 'alert alert-danger'));
            }
        }
        $categoryData = $this->DeviceCategory->findByCategoryId($id);
        $this->set('categories', $categoryData);

    }

    public function delete($id = null) {
        $this->_set_meta('Delete Category','','');
        $this->request->allowMethod('post');
        $this->DeviceCategory->id = $id;
        if (!$this->DeviceCategory->exists()) {
            throw new NotFoundException(__('Invalid Category'));
        }
        if ($this->DeviceCategory->delete()) {
            // $this->Flash->success(__('User deleted'));
            return $this->redirect(array('action' => 'index'));
        }
        // $this->Flash->error(__('User was not deleted'));
        return $this->redirect(array('action' => 'index'));
    }

}
?>