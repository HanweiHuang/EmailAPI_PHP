<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Person{
    /**
     * customer name
     * @var string 
     */
    protected $name;
    
    /**
     *
     * @var type email
     */
    protected $emailAddress;
    
    /**
     * set customer name
     * @param type $name
     */
    public function setName($name){
        $this->name = $name;
    }
    /**
     * get customer name
     * @return type
     */
    public function getName(){
        return $this->name;
    }
    
    public function setEmailAddress($emailAddress){
        $this->emailAddress = $emailAddress;
    }
    
    public function getEmailAddress(){
        return $this->emailAddress;
    }
    
}