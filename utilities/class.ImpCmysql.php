<?php
include_once 'class.Cmysql.php';

class ImpCmysql{
    
    private $dao;
    
    private static $host = '127.0.0.1';
    
    private static $username = 'root';
    
    private static $password = 'root';
    
    private static $database = 'harvey';
    
    private static $instance;
    
    function __construct($dao){
        $this->dao = $dao;
    }
    
    public static function getInstance() {
        if(!(self::$instance instanceof self)){
            //$c = new Cmysql();
            //$Cmy = $c->getInstance($this->host, $this->username, $this->password, $this->database);
            $Cmy = Cmysql::getInstance(self::$host,self::$username,self::$password,self::$database);
            self::$instance = new self($Cmy);
        }
        return self::$instance;
    }
    
    
    public function addMailFromImap($uid='',$content='',$subject='',$date='',$sender='',$unread='',$attachment_path=''){
        $sql = "insert into imap_mails(id,content,subject,date,sender,unread,attachment_path) "
                . "values('$uid','$content','$subject','$date','$sender','$unread','$attachment_path')";
        $sql;
        if($this->dao->uidRst($sql)!=''){
            return true;
        }
        else {return false;}
    }

}