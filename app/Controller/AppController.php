<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */

App::uses('PhptalView', 'View');

class AppController extends Controller {

	public $helpers = array(
        'Html', 
        'Form', 
        'Session'
    );

	public $viewClass = 'Phptal';
	public $ext = '.html';

    public $components = array(
    	'Session',
    	'Auth' => array(
            'loginRedirect' => array(
                'controller' => 'users',
                'action' => 'index'
            ),
            'logoutRedirect' => array(
                'controller' => 'users',
                'action' => 'login'
            ),
            'authenticate' => array(
                'Form' => array(
                    'passwordHasher' => 'Blowfish'
                )
            ),
            'authorize' => array('Controller')
        ),
        'RequestHandler'
    );

    public function beforeFilter() {
        parent::beforeFilter();
        if($this->userEmployee()){
            $this->set('role', 'employee');
        }else if( $this->userAdmin()){
            $this->set('role', 'admin');
        }
        $this->set('currentUrl',Router::url(null));
    }

    public function isAuthorized($user)
    {
        // Admin can access every action
        if (isset($user['level']) && $user['level'] === '0') {
            return true;
        }

        // Default deny
        return false;
    }

    public function userEmployee(){
        if ($this->Auth->user('level') === '1') {
            return true;
        }

        return false;
    }

    public function userAdmin(){
        if ($this->Auth->user('level') === '0') {
            return true;
        }

        return false;
    }
    
}
