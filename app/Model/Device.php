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
            'foreignKey' => 'device_id',
            'dependent' => true
        )
    );
    public $validate = array(
       'name' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => 'required'
            )
        ),
       'product_no' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => 'required'
            ),
            'isUnique' => array(
              'rule' => 'isUnique',
              'message' => 'is already used.'
            )
        )
       
    );

    public function beforeDelete($cascade = true) {
        $count = $this->Borrower->find("count", array(
            "conditions" => array("Borrower.device_id" => $this->id)
        ));
        if ($count == 0) {
            return true;
        }
        return false;
    }

}
?>