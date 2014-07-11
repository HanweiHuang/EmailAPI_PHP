<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class MailObject{
    protected $sender;
    
    protected $to;
    
    protected $subject;
    
    protected $message;
    
    protected $unread;
    
    protected $attachments=array();
    
    protected $inlines = array();
    
    protected $msgno;
    
    protected $date;
    
    public function setInlines($inlines = array()){
        $this->inlines = $inlines;
    }
    public function getInlines(){
        return $this->inlines; 
    }
    
    public function getAttachments(){
        return $this->attachments;
    }
    
    public function setAttachments($attachements=array()){
        $this->attachments = $attachements;
    }
    
    public function getMessage(){
        return $this->message;
    }
    
    public function setMessage($message){
        $this->message = $message;
    }
    
    public function getSubject(){
        return $this->subject;
    }
    
    public function setSubject($subject){
        $this->subject = $subject;
    }
    
    public function getSender(){
        return $this->sender;
    }
    public function setSender($sender){
        $this->sender = $sender;
    }
    public function getUnread(){
        return $this->unread;
    }
    public function setUnread($unread){
        $this->unread = $unread;
    }
    public function setMsgno($msgno){
        $this->msgno = $msgno;
    }
    public function getMsgno(){
        return $this->msgno;
    }
    
    public function getDate(){
        return $this->date;
    }
    public function setDate($date){
        $this->date = $date;
    }
}


