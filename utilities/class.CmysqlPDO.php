<?php

/**
 * author: Harvey Huang
 * date:2014/07/16
 */

class CPDO {
    
    private $drive='mysql';
    private $host;
    private $name;
    private $pwd;
    private $dBase;
    private $charset = 'utf8'; //set charset
    private $query_count;
    
    private static $instance;
    private $result;
    
    private $config;
//    
//    private function __construct($host='',$name='',$pwd='',$dBase='') {
//        if($host  !=  ''){  $this->host  = $host;}
//        if($name  !=  ''){  $this->name  = $name;}
//        if($pwd   !=  ''){  $this->pwd   = $pwd;}
//        if($dBase !=  ''){  $this->dBase = $dBase;}
//        $this->init_conn();
//    }
  
    function __construct($config) {
        $this->config = $config;
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
    
//    //init PDO
//    function init_conn(){
//        $config = $this->drive.':host='.$this->host.';dbname='.$this->dBase.';charset='.$this->charset;
//        $this->conn = new PDO($config, $this->name, $this->pwd);
//        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//        $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
//        
//    }
    //connect 
    public function init_conn(){
        $this->conn = new PDO($this->config['dsn'], $this->config['name'], $this->config['password']);
        $this->conn->query('set names utf8');
        //$this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
//    
//    function mysql_query_result($sql){
//        if($this->conn == '') $this->init_conn();
//        $stmt = $this->conn->query($sql);
//        $this->result = $stmt->fetchAll(PDO::FETCH_ASSOC);
//        $this->query_count++;
//    }
    
    //close db
    public function close(){
        $this->conn = null;
    }
    
    /**
     * basic functions
     * 
     */
    public function query($sql){
        $result = $this->conn->query($sql);
        if($result){
            $this->result = $result;
        }
    }
    
    public function exec($sql){
        $result = $this->conn->exec($sql);
        if($result){
            $this->result = $result;
        }
    }
    
    public function fetchAll(){
        return $this->result->fetchAll();
    }
    
    public function fetch(){
        return $this->result->fetch();
    }
    
    public function fetchColumn(){
        return $this->result->fetchColumn();
    }
    
    public function lastInsertId(){
        return $this->result->lastInsertId();
    }
    
    /**
     *           int $debug : 0 off
     *                        1 open
     *                        2 open and die the program
     * 
     *           int  $mode : 0 return multi result
     *                        1 return single result
     *                        2 return num of records
     * 
     * string/array $table  : normal transfer 'tb_1' 'tb_2'
     *                        array transfer array('tb_1','tb_2')
     * 
     * string/array $fields : normal transfer 'password,username'
     *                        array transfer array('username','password')
     * 
     * string/array $sqlwhere:  normal and type = 1 and username like '%os%'
     *                          array('type=1','username like "%os%"')
     * 
     * string       $orderby: default desc
     */               
    
    public function select($debug, $mode, $table, $fields='*',$sqlwhere='',$orderby='tbid desc'){
        //deal with parameters
        if(is_array($table)){
            $table = implode(',', $table);
        }
        if(is_array($fields)){
            $fields = implode(',', $fields);
        }
        if(is_array($sqlwhere)){
            $sqlwhere = ' and '.impload(' and ',$sqlwhere);
        }
        
        //deal with database
        if($debug === 0){
            if($model===2){
                $this->query("select count(tbid) from $tbale where 1=1 $sqlwhere");
                $return = $this->fetchColumn();
            }else if($model===1){
                $this->query("select $fields from $table where 1=1 $sqlwhere order by $orderby");
                $return = $this->fetch();
            }else{
                $this->query("select $fields from $table where 1=1 $sqlwhere order by $orderby");
                $return = $this->fetchAll();
            }
            return $return;
        }
        else{
            if($mode === 2){
                echo "select count(tbid) from $tbale where 1=1 $sqlwhere";
            }else if($model===1){
                echo "select $fields from $table where 1=1 $sqlwhere order by $orderby";
            }else{
                echo "select $fields from $table where 1=1 $sqlwhere order by $orderby";
            }
            if($debug === 2){
                exit;
            }
        }
    }
    public function insert($debug,$mode,$table,$set){
        if(is_array($table)){
            $table = implode(',', $table);
        }
        if(is_array($set)){
            $set = implode(',', $set);
        }
        
        //deal with data
        if($debug === 0){
            if($mode === 2){
                $this->query("insert into $table set $set");
                $return = $this->lastInsertId();
            }else if($mode === 1){
                $this->exec("insert into $table set $set");
                $return = $this->result;
            }else{
                $this->query("insert into $table set $set");
                $return = null;
            }
            return $return;
        }else{
            echo "insert into $table set $set";
            if($debug === 2){
                exit;
            }
        }
    }
    
    
    
//    function getRowsNumPDO($sql){
//        $stmt = $this->conn->query($sql);
//        $rows_num = $stmt->rowCount();
//        return $rows_num;
//    }
//    
//    function returnRstId($sql){
//        if($this->conn == ''){
//            $this->init_conn();
//        }
//        $result = $this->conn->exec($sql);
//        $insertId = $this->conn->lastInserId();
//    }
}