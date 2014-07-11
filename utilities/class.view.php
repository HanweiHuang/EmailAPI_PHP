<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class view{
    
    protected $file_path;
    protected $data = array();
    
    public function __construct($file) {
        $this->file_path = $file;
    }
    
    public function setdata($key,$value){
        $this->data[$key] = $value;
    }
    
    public function getdata($key){
        return $this->data[$key];
    }
    public function output(){
        if(!file_exists($this->file_path)){
            throw new Exception("Template".$this->file_path."doesn't exist.");
        }
        extract($this->data);
        ob_start();
        include($this->file_path);
        $output=  ob_get_contents();
        ob_end_clean();
        echo $output;
        //return $output;
    }
    
    public function render($file_path,$data){
        extract($data);
        //ob_end_clean();
        ob_start();
        include($file_path);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
}