<?php
App::uses('AppModel', 'Model');
App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');

class User extends AppModel {
    public $primaryKey = 'user_id';
    public $validate = array(
        'username' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => 'Username is required'
            ),
            'usernameExist' => array(
                'rule' => 'usernameExist',
                'message' => 'Username already exists'
            )
        ),
        'password' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => 'Password is required'
            )
        ),
        'employeeId' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => 'Employee ID is required'
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

    public function usernameExist($check) {
        $existingUsers = $this->find('count', array(
            'conditions' => $check,
            'recursive' => -1
        ));
        return $existingUsers < 1;
    }

    public function beforeSave($options = array()) {
        if (isset($this->data[$this->alias]['password'])) {
            $passwordHasher = new BlowfishPasswordHasher();
            $this->data[$this->alias]['password'] = $passwordHasher->hash(
                $this->data[$this->alias]['password']
            );
        }
        return true;
    }
}
?>