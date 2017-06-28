<?php
App::uses('AppController', 'Controller');

class DeviceCategoriesController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    private function _set_meta ($title,$description,$keywords) {
        $this->set('meta_title', $title);
        $this->set('meta_description', $description);
        $this->set('meta_keywords', $keywords);
    }

    public function index() {
        $this->_set_meta('Device Categories','','');
        $this->DeviceCategory->recursive = 0;
        if($this->request->is('get') && isset($this->request->query['search'])){
            $keyword = $this->request->query['keyword']; 
            $this->paginate = array('DeviceCategory' => 
                array(
                    'conditions' => array(
                        'name LIKE' => "%".$keyword."%"
                    ),
                    'limit'=>10
                )
            );
        }
        $this->set('categories', $this->paginate());
        $this->set('actionEdit', Router::url('/device-categories/edit'));
        $this->set('actionView', Router::url('/device-categories/view'));
    }

    public function view($id = null) {
        $this->_set_meta('Users','','');
        $this->DeviceCategory->id = $id;
        if (!$this->DeviceCategory->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }

        $this->set('category', $this->DeviceCategory->findByCategoryId($id));
    }

    public function add() {
        $this->_set_meta('Add Device Category','','');

        if ($this->request->is('post')) {
            $this->DeviceCategory->create();
            $this->DeviceCategory->set($this->request->data);
            if ($this->DeviceCategory->save($this->request->data)) {
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
            return $this->redirect(array('action' => 'index'));
        }else{
            $this->Session->setFlash(__('Can\'t delete because it has records. Failed to delete.'), 'default', array('class' => 'alert alert-danger'), 'bad');
        }
        $this->Session->setFlash(__('Can\'t delete because it has records. Failed to delete.'), 'default', array('class' => 'alert alert-danger'), 'bad');
        return $this->redirect(array('action' => 'index'));
    }

}
?>