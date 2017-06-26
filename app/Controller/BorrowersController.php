<?php
App::uses('AppController', 'Controller');

class BorrowersController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    private function _set_meta ($title,$description,$keywords) {
        $this->set('meta_title', $title);
        $this->set('meta_description', $description);
        $this->set('meta_keywords', $keywords);
    }

    public function deviceUsage($id = null) {
        $this->_set_meta('Device Usage History','','');
        $this->paginate =  array(
            'conditions' => array(
                'Borrower.device_id' => $id
            ),
            'fields' => array('borrowed_date','return_date','User.name','User.employee_id','Device.name'),
            'limit' => 10,
            'order' => array(
                'borrowed_date' => 'desc'
            )
        );
        $this->set('deviceUsages', $this->paginate());
    }

    public function borrow($id = null) {
        $this->_set_meta('Borrow Device','','');
        if ($this->request->is('post')) {
            if($id != null ){
                $this->Borrower->create();
                $db = $this->Borrower->getDataSource(); 
                $this->Borrower->set(array(
                    'device_id' => $id,
                    'user_id' => $this->Auth->user('user_id'),
                    'borrowed_date' => $db->expression('NOW()')
                ));
                if ($this->Borrower->save()) {
                    return $this->redirect(array('controller'=>'devices', 'action' => 'index'));
                }
            }
            $this->set('notification', array('There\'s a problem in borrowing the device', 'alert alert-danger'));
            return $this->redirect(array('controller'=>'devices', 'action' => 'index'));
        }

    }

    public function add($id = null) {
        $this->_set_meta('Borrow Device','','');
        if ($this->request->is('post')) {
            $this->Borrower->Device->id = $id;
            $this->Borrower->User->id = isset($this->request->data['Borrower']['user_id'])? $this->request->data['Borrower']['user_id'] : '' ;
            if($id != null ){
                if (!$this->Borrower->Device->exists()) {
                    $this->set('notification', array('Invalid Device', 'alert alert-danger'));
                }else{
                    if (!$this->Borrower->User->exists()) {
                        $this->set('notification', array('Unknown User', 'alert alert-danger'));
                    }else{
                        $this->Borrower->create();
                        $db = $this->Borrower->getDataSource(); 
                        $this->Borrower->set(array(
                            'device_id' => $id,
                            'user_id' => $this->request->data['Borrower']['user_id'],
                            'borrowed_date' => $db->expression('NOW()')
                        ));
                        if ($this->Borrower->save()) {
                            return $this->redirect(array('controller'=>'devices', 'action' => 'index'));
                        }else{
                            $this->set('notification', array('There\'s a problem in borrowing the device', 'alert alert-danger'));
                        }
                    }
                }
            }
            // 
        }
        $deviceData = $this->Borrower->Device->findByDeviceId($id);
        $this->set('device', $deviceData);

    }

    public function returnDevice($id = null) {
        $this->_set_meta('Return Device','','');
        $this->request->allowMethod('post');
        $this->Borrower->id = $id;
        $db = $this->Borrower->getDataSource(); 
        $this->Borrower->set(array(
            'return_date' => $db->expression('NOW()')
        ));
        if (!$this->Borrower->exists()) {
            throw new NotFoundException(__('Invalid'));
        }
        if ($this->Borrower->save()) {
           return $this->redirect(array('controller'=>'devices', 'action' => 'index'));
        }
        return $this->redirect(array('controller'=>'devices', 'action' => 'index'));
    }

    public function isAuthorized($user)
    {
        if ($this->action === 'returnDevice' || $this->action === 'borrow') {
            if (isset($user['level']) && $user['level'] === '1') {
                $this->layout = 'employee';
                return true;
            }
        }

        return parent::isAuthorized($user);
    }



}
?>