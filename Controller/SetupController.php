<?php
App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class SetupController extends AppController {
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('index', 'database', 'password', 'upgrade', 'finish');
        $this->layout = 'setup';
    }
    
    public function isAuthorized($user) {
        return Configure::read('App.setupMode');
    }
    
    public $uses = array();

    public function index() {}
    
    public function database() {}
    
    public function upgrade() {}
    
    public function password() {
        $this->loadModel('User');
        $user = $this->User->findByUsername('admin');
        if ($this->request->is(array('post', 'put'))) {
            if (empty($this->request->data['User']['password1'])) {
                $this->Session->setFlash(__('Password must have a value.'));
            } else if ($this->request->data['User']['password1'] === $this->request->data['User']['password2']) { 
                $user['User']['password'] = $this->request->data['User']['password2'];
                $user['User']['locked'] = 0;
                $user['User']['email'] = $this->request->data['User']['email'];
                if ($this->User->save($user)) {
                    $this->Session->setFlash(__('Password successfully saved'), 'success');
                    return $this->redirect(array('action' => 'finish'));
                } else {
                    debug($this->User->invalidFields());
                    $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
                }
            } else {
                $this->Session->setFlash(__('Passwords did not match. Please, try again.'));
            }
        } else {
            $this->request->data = $user;
        }
    }
    
    public function finish() {}
}
