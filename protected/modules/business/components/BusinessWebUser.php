<?php

class BusinessWebUser extends CWebUser {

    public function login($identity, $duration=NULL) {
        return parent::login($identity, $duration);
    }

    
    public function getId() {
        return $this->getState('id');
    }
    
}

?>