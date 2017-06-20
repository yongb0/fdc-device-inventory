<h1>Edit User</h1>
<?php
echo $this->Form->create('User');
echo $this->Form->input('username');
echo $this->Form->input('password');
echo $this->Form->input('employee_id');
echo $this->Form->input('name');
echo $this->Form->input('number');
echo $this->Form->input('level', array(
    'options' => array('0' => 'Admin', '1' => 'Employee')
));
echo $this->Form->input('status', array(
    'options' => array('0' => 'Active', '1' => 'Inactive')
));
echo $this->Form->input('id', array('type' => 'hidden'));
echo $this->Form->end('Save');
?>