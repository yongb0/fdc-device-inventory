<?php
App::uses('AppModel', 'Model');

class Device extends AppModel {
    public $primaryKey = 'device_id';
    public $actsAs = array('Containable');
    public $belongsTo =  array(
        'DeviceCategory' => array(
            'className' => 'DeviceCategory',
            'foreignKey' => 'category_id'
        )
    );
   	public $hasMany =  array(
        'Borrower' => array(
            'className' => 'Borrower',
            'foreignKey' => 'device_id'
        )
    );
    public $validate = array(
       'name' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => 'Name is required'
            )
        )
    );

}
?>