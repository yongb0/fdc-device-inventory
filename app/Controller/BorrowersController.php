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

    public function deviceUsageReport($id = null) {
        $this->_set_meta('Device Usage Report','','');
        $this->set('data', array());
        if ($this->request->is('post')) {
            $date = $this->request->data['date'];
            $returndate = $this->request->data['returnedDate'];
            $employeeId = $this->request->data['employee_id'];
            $employeeName = $this->request->data['employee_name'];
            $device = $this->request->data['device'];
            $productNo = $this->request->data['product_no'];
            $conditions = array();

            if($date != ""){
                $conditions["borrowed_date LIKE"] = $date."%";
            }
            if($returndate != ""){
                $conditions["return_date LIKE"] = $returndate."%";
            }else if($returndate == "none"){
                $conditions["return_date LIKE"] = "0000-00-00%";
            }
            if($employeeId != ""){
                $conditions["User.employee_id"] = $employeeId;
            }
            if($employeeName != ""){
                $conditions["User.name"] = $employeeName;
            }
            if($device != ""){
                $conditions["Device.name"] = $device;
            }
            if($productNo != ""){
                $conditions["Device.product_no"] = $productNo;
            }

            // print_r($conditions);
            $data = $this->Borrower->find('all',
                array(
                    'conditions' => array(
                        "AND" => $conditions
                    ),
                    'order' => array(
                        "Device.name ASC"
                    )
                )
            );
            $this->set('data', $data);
            $this->set('date', $date);
            
        }
    }

    public function borrow($id = null) {
        $this->_set_meta('Borrow Device','','');
        if ($this->request->is('post')) {
            if($id != null ){
                // setting device to in use
                $this->Borrower->Device->id = $id;
                if (!$this->Borrower->Device->exists()) {
                    throw new NotFoundException(__('Invalid Device'));
                }
                $this->Borrower->Device->set(array(
                    'in_use' => "true",
                ));
                $this->Borrower->Device->save();

                // adding the borrower
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
                        // setting device to in use
                        $this->Borrower->Device->set(array(
                            'in_use' => "true",
                        ));
                        $this->Borrower->Device->save();

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
        $db = $this->Borrower->getDataSource();
        //getting the data and setting the id
        $this->Borrower->id = $id;
        $borrowerData = $this->Borrower->findByBorrowerId($this->Borrower->id);
        if(!empty($borrowerData) && isset($borrowerData['Borrower']['device_id'])){
            $this->Borrower->Device->id = $borrowerData['Borrower']['device_id'];
        }
        if (!$this->Borrower->exists() && $this->Borrower->Device->exists()) {
            throw new NotFoundException(__('Invalid'));
        }
        if (!empty($borrower)) {
            $this->Borrower->Device->save($this->request->data);
            $this->Borrower->Device->id =  $this->Borrower->id;
        }
        if ($this->Borrower->save(array('return_date'=> $db->expression('NOW()'))) &&  $this->Borrower->Device->save(array('in_use'=> 'false'))) {
           return $this->redirect(array('controller'=>'devices', 'action' => 'index'));
        }
        // return $this->redirect(array('controller'=>'devices', 'action' => 'index'));
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

    public function export() {

        $this->response->download("export.csv");

        $data = $this->Borrower->find('all');
        $this->set(compact('data'));

        $this->layout = 'ajax';

        return;

    }



}
?>