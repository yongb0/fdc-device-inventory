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
        $categories = new DeviceCategory();
        $categoriesData = $categories->find('all',['fields' => ['name','category_id']]);
        $borrower = $this->Device->Borrower->find('all', array(
            'fields' => array('Borrower.device_id','borrowed_date','return_date','User.employee_id','User.name','User.user_id','borrower_id'),
            'conditions'=>array('return_date'=>'0000-00-00 00:00:00'),
            'key' => 'Borrower.device_id'                                                              
        ));
        $borrower = Hash::combine($borrower, '{n}.Borrower.device_id', '{n}');
        $this->set('borrower', $borrower);
        $this->set('categories', $categoriesData);

        if($this->userEmployee() || $this->userAdmin()){
            $this->set('actionView', Router::url(array('controller' => strtolower($this->name), 'action' => 'view')));
        }

        if($this->userAdmin()){
            $this->set('actionEdit', Router::url(array('controller' => strtolower($this->name), 'action' => 'edit')));
        }

    }

    public function view($id = null) {
        $this->_set_meta('Devices','','');
        $this->Device->id = $id;
        if (!$this->Device->exists()) {
            throw new NotFoundException(__('Invalid device'));
        }

        $this->set('device', $this->Device->findByDeviceId($id));
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
        $categories = new DeviceCategory();
        $this->set(
            'categories',
            $categories->find('all',[
                'recursive' => -1,
                'contain' => [],
                'fields' => ['name','category_id']
            ])
        );
        $this->_set_meta('Edit Device','','');
        $this->Device->id = $id;
        if (!$this->Device->exists()) {
            throw new NotFoundException(__('Invalid Device'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Device->save($this->request->data)) {
                $this->set('notification', array('The device has been saved', 'alert alert-success'));
            }
            else{
                $this->set('notification', array('The device could not be saved. Please, try again.', 'alert alert-danger'));
            }
        }
        $deviceData = $this->Device->findByDeviceId($id);
        $this->set('device', $deviceData);
    }

    public function delete($id = null) {
        $this->_set_meta('Delete Category','','');
        $this->request->allowMethod('post');
        $this->Device->id = $id;
        if (!$this->Device->exists()) {
            throw new NotFoundException(__('Invalid Device'));
        }
        if ($this->Device->delete()) {
            return $this->redirect(array('action' => 'index'));
        }
        return $this->redirect(array('action' => 'index'));
    }

    public function isAuthorized($user)
    {
        if ($this->action === 'index' || $this->action === 'view') {
            if (isset($user['level']) && $user['level'] === '1') {
                $this->layout = 'employee';
                return true;
            }
        }
        return parent::isAuthorized($user);
    }

}
?>