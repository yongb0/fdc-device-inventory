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

}
?>