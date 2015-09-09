<?php 

// app/Model/User.php
App::uses('AppModel', 'Model');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');

class User extends AppModel {
	
    public $name = 'User';
    public $validate = array(
        'username' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'Un nom d\'utilisateur est requis'
            )
        ),
        'password' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'Un mot de passe est requis'
            )
        ),
        'role' => array(
            'valid' => array(
                'rule' => array('inList', array('admin', 'auteur')),
                'message' => 'Merci de rentrer un rôle valide',
                'allowEmpty' => false
            )
        )
    );
    
    public function beforeSave($options = array()) {
    	if (isset($this->data[$this->alias]['password'])) {
    		$passwordHasher = new SimplePasswordHasher();
    		$this->data[$this->alias]['password'] = $passwordHasher->hash(
    				$this->data[$this->alias]['password']
    		);
    	}
    	return true;
    }
}