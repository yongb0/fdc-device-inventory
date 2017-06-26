<?php
App::uses('AppModel', 'Model');
App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');

class User extends AppModel {
    public $primaryKey = 'user_id';
    public $hasMany =  array(
        'Borrower' => array(
            'className' => 'Borrower',
            'foreignKey' => 'user_id',
            'dependent' => true
        )
    );

    public $validate = array(
        'username' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => 'required'
            ),
            'isUnique' => array(
              'rule' => 'isUnique',
              'message' => 'is already used.'
            )
        ),
        'password' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'message' => 'required'
            )
        ),
        'employee_id' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => 'required'
            )
        ),
        'name' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => 'required'
            )
        ),
        'level' => array(
            'valid' => array(
                'rule' => array('inList', array('0', '1')),
                'message' => 'Please enter a valid role',
                'allowEmpty' => false
            )
        )
    );

    public function beforeSave($options = array()) {
        if (isset($this->data[$this->alias]['password']) && !empty($this->data[$this->alias]['password'])) {
            $passwordHasher = new BlowfishPasswordHasher();
            $this->data[$this->alias]['password'] = $passwordHasher->hash(
                $this->data[$this->alias]['password']
            );
        } else {
            unset($this->data[$this->alias]['password']);
        }
        return true;
    }
}
?>