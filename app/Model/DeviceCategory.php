<?php
App::uses('AppModel', 'Model');

class DeviceCategory extends AppModel {
    public $primaryKey = 'category_id';
    public $hasMany =  array(
        'Device' => array(
            'className' => 'Device',
            'foreignKey' => 'category_id'
        )
    );
    public $validate = array(
       'name' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => 'required'
            )
        )
    );

    public function beforeDelete($cascade = true) {
        $count = $this->Device->find("count", array(
            "conditions" => array("Device.category_id" => $this->id)
        ));
        if ($count == 0) {
            return true;
        }
        return false;
    }
}
?>