<?php
App::uses('AppModel', 'Model');

class Borrower extends AppModel {
    public $primaryKey = 'borrower_id';
    public $belongsTo =  array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id'
        ),
        'Device' => array(
            'className' => 'Device',
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