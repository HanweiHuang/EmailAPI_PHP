<?php

class Cmysql{
    
    private $host;//host
    private $name;//username
    private $pwd; //password
    private $dBase; //database
    private $conn;  //connection
    private $result; //result result
    private $msg;  //return msg
    private $fields; //return field
    private $fieldsNum; //num of return fields
    private $rowsNum; //return num of rows
    private $rowsRst; //
    private $filesArray = array();
    private $rowsArray = array();
    private $charset = 'utf-8'; //set charset
    private $query_count = 0; //search times
    static private $instance; 
                        
    private function __construct($host='',$name='',$pwd='',$dBase='') {
        if($host  !=  ''){  $this->host  = $host;}
        if($name  !=  ''){  $this->name  = $name;}
        if($pwd   !=  ''){  $this->pwd   = $pwd;}
        if($dBase !=  ''){  $this->dBase = $dBase;}
        $this->init_conn();
    }
    
    private function __clone() {
    }
    
    public static function getInstance($host='',$name='',$pwd='',$dBase=''){
        if(FALSE == (self::$instance instanceof self)){
            self::$instance = new self($host,$name,$pwd,$dBase);
        }
        return self::$instance;
    }
    
    function init_conn(){
        $this->conn = mysql_connect($this->host,$this->name,$this->pwd) or die('connect db fail');
        mysql_select_db($this->dBase,$this->conn) or die('select db fail!');
        mysql_query("set names".$this->charset);
    }
    
    function mysql_query_result($sql){
        if($this->conn == '') $this->init_conn();
        $this->result = mysql_query($sql,$this->conn);
        $this->query_count++;
    }
    
    //get num of fields
    function getFieldsNum($sql){
        $this->mysql_query_result($sql);
        $this->fieldsNum = @mysql_num_fields($this->result);
    }
    
    //get num of raws
    function getRowsNum($sql){
        $this->mysql_query_result($sql);
        if(mysql_errno() == 0){
                return @mysql_num_rows($this->result);
        }else{
                return '';
        }	
    }
    
    function getRowsRst($sql,$type=MYSQL_BOTH){
        $this->mysql_query_result($sql);
        if(empty($this->result)) return '';
        if(mysql_error() == 0){
                $this->rowsRst = mysql_fetch_array($this->result,$type);
                return $this->rowsRst;
        }else{
                return '';
        }
    }
    //get rows array
    function getRowsArray($sql,$type=MYSQL_BOTH){
    !empty($this->rowsArray) ? $this->rowsArray=array() : '';
        $this->mysql_query_result($sql);
        if(mysql_errno() == 0){
                while($row = mysql_fetch_array($this->result,$type)) {
                        $this->rowsArray[] = $row;
                }
                return $this->rowsArray;
        }else{
                return '';
        }
    }
    
    //return recently affected num of records
    function uidRst($sql){
        if($this->conn == ''){
           $this->init_conn();
        }
        var_dump(mysql_query($sql));
        $this->rowsNum = mysql_affected_rows();
        if(mysql_errno() == 0){
                return $this->rowsNum;
        }else{
                return '';
        }
    }
    
    function getFields($sql,$fields){
        $this->mysql_query_result($sql);
        if(mysql_errno() == 0){
            if(mysql_num_rows($this->result) > 0){
                    $tmpfld = @mysql_fetch_row($this->result);
                    $this->fields = $tmpfld[$fields];

            }
            return $this->fields;
        }else{
            return '';
        }
    }
    
    function returnRstId($sql){
        if($this->conn == ''){
            $this->init_conn();
        }
        @mysql_query($sql);
        if(mysql_errno() == 0){
            return mysql_insert_id();
        }else{
            return '';
        }
    }
    
    function msg_error(){
        if(mysql_errno() != 0) {
            $this->msg = mysql_error();
        }
        return $this->msg;
    }
    
    function close_result(){
        mysql_free_result($this->result);
        $this->msg = '';
        $this->fieldsNum = 0;
        $this->rowsNum = 0;
        $this->filesArray = '';
        $this->rowsArray = '';
    }
    
    function close_conn(){
        $this->close_result();
        mysql_close($this->conn);
        $this->conn='';
    }
    
    function db_version() {
        return mysql_get_server_info();
    }
}