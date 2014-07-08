<?php
//error_reporting(0);
/**
 * include Mail.php because this program used pear:mail to send mail
 */
require_once "Mail.php";
/**
 * model of mail object
 */
require_once "class.mail.php";
require_once "Mail/mime.php";
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Cmail{
    //version control
    const VERSION = '1.0';
    ///////////////////////////////////////////////
    /*
     * message body
     * type string 
     */
    public $Body = '';
    /**
     * subject of message
     * @var type string
     */
    public $Subject='';
    //////////////////////////////////////////smtp//////////////////////////
    /** 
     * a smtp connection
     * @var type 
     */
    public $smtp = null;
    
    /**
     * SMTP host
     * @var type 
     */
    public $smtpHost = 'localhost';
    
    /**
     * the defalut SMTP server port
     * @var type 
     */
    public $smtpPort = '25';
    /**
     * whether to use SMTP authentication 
     * @var type boolean
     */
    public $smtpAuth = true;
    /**
     * the SMTP username
     * @var type 
     */
    public $smtpUsername = '';
    /**
     * the SMTP password
     * @var type 
     */
    public $smtpPassword = '';
    /**
     * whether to use debug
     * @var type 
     */
    public $smtpdebug = false;
    
    /**
     * the from email address for the message
     */
    public $smtpFrom = 'example@classicbet.au';
    
    /////////////////////////////////////////////////////
    //for debug config 
    protected $Debugoutput = 'html';
    
    protected $do_debug = '4';
    //////////////////////////////////////////////
    //get list config
    protected $num_records = 10;
    
    //protected $imap_host = '';
    
    protected $unRead = false;
 
    protected $senderEmail = null;
    
    /////////////////////////////////////////////////
    //imap config
    protected $imapHost = '';
    
    protected $imapPort = '143';
    
    protected $imapUsername = '';
    
    protected $imapPassword = '';
     
    protected $imapMailbox = null;
    
    //////////////////////////////////
    protected $messageList = array();

    protected $savedirpath = __FILE__;
    
    protected $attachements = array();
    
    protected $is_attachment = false;
    ////////////////////////////
    
    
    
   
    
    protected function edebug($str)
    {
        switch ($this->Debugoutput) {
            case 'error_log':
                //Don't output, just log
                error_log($str);
                break;
            case 'html':
                //Cleans up output a bit for a better looking, HTML-safe output
                echo htmlentities(
                    preg_replace('/[\r\n]+/', '', $str),
                    ENT_QUOTES,
                    'UTF-8'
                )
                . "<br>\n";
                break;
            case 'echo':
            default:
                echo gmdate('Y-m-d H:i:s')."\t".trim($str)."\n";
        }
    }
    
    
    /**
     * 1.different message should be treated by different encoding method
     * @param type $message
     * @param type $coding
     * @return type
     */
    public function getdecodevalue($message,$coding) {
            switch($coding) {
                    case 0:
                    case 1:
                            $message = imap_8bit($message);
                            break;
                    case 2:
                            $message = imap_binary($message);
                            break;
                    case 3:
                    case 5:
                            $message=imap_base64($message);
                            break;
                    case 4:
                            $message = imap_qprint($message);
                            break;
            }
            return $message;
    }
    
    /**
     * 1.set the config parameters:
     * 
     * @param type $configArray
     * @param type $particularArray
     * @return boolean|array
     */
    public function getEmailList($configArray=array(),$particularArray=array()) {
        //email object list
        $emailList = array();
        
        //check the particular elements (numRecords,unread,senderEmail)
        if(isset($particularArray['numRecords'])) $this->num_records = $particularArray['numRecords'];
    
        if(isset($particularArray['unRead']))  $this->unRead = $particularArray['unRead'];
        
        if(isset($particularArray['senderEmail'])) $this->senderEmail =  $particularArray['senderEmail'];
        
        //check the config elements (host,password,username)
        if(isset($configArray['imapHost'])){
            $this->imapHost = $configArray['imapHost'];
        }
        if(isset($configArray['imapPort'])){
            $this->imapPort = $configArray['imapPort'];
        }
        if(isset($configArray['imapUsername'])){
            $this->imapUsername = $configArray['imapUsername'];
        }
        if(isset($configArray['imapPassword'])){
            $this->imapPassword = $configArray['imapPassword'];
        }
        //config $server
        $server = '{'.$this->imapHost.'}INBOX';
        
        //connect server, if fail, output error message and return false
        if(!($this->imapMailbox=imap_open($server,$this->imapUsername,$this->imapPassword))){
            if($this->do_debug>=3){
                $this->edebug('fail to connect: '.$server);
                $this->closeImapConnection();
                return false;
            }
        }
        
        //begin search emails by imap
        /**
         * 4 situations 1.unread emails from sender 
         *              2.read emails recently from sender 
         *              3.recently unread emails 
         *              4.recently emails
         */
        if($this->unRead && isset($this->senderEmail)){
            $emails = imap_search($this->imapMailbox,'UNSEEN FROM '.$this->senderEmail);
        }
        
        else if(!($this->unRead) && isset ($this->senderEmail)){
            echo $str = 'ALL FROM "'.$this->senderEmail.'"';
            $emails = imap_search($this->imapMailbox,'ALL FROM "'.$this->senderEmail.'"');
        }
        else if($this->unRead && (!isset($this->senderEmail))){
            $emails = imap_search($this->imapMailbox,'UNSEEN');
        }
        else{
            $emails = imap_search($this->imapMailbox,'ALL');
        }
        
        //check if no eamils found, return error message 
        if(!$emails){
            if($this->do_debug>=2){
                $this->edebug('there is no Emails meet your requirements');
                return false;
            }
        }
        else{
            rsort($emails);//sort emails, begin from the recently emails
            //if not enough emails been found, output all of them
            if(count($emails)<=$this->num_records){
                foreach($emails as $emailnumber){
                    $email = new MailObject();
                    /////////////////////////
                    $structure = imap_fetchstructure($this->imapMailbox, $emailnumber);
                    //var_dump($structure);
                    
                    //echo "<br>"."----------------------------------"."<br>";
                    $header = imap_header($this->imapMailbox,$emailnumber);
                    //var_dump($header);
                    //echo "<br>"."----------------------------------"."<br>";
                    //////////////////////////
                    
                    $overview = imap_fetch_overview($this->imapMailbox,$emailnumber,0);
                    //var_dump($overview);
                    //get content of emails
                    $text = $this->getMessage($this->imapMailbox, $emailnumber,$overview[0]->uid);
                    // if it doesnt return text,there are some parts in this email, should find data in messageList
                    if(!isset($text)){
                        if(!isset($this->messageList['html'])){
                            $data = quoted_printable_decode($this->messageList['plain']);
                        }
                        else{
                            $data = quoted_printable_decode($this->messageList['html']);
                        }
                        $email->setMessage($data);
                    }else{
                        //no parts in this email, get content directly
                        $email->setMessage($text);
                    }
                    
                    //set subject
                    $email->setSubject(htmlspecialchars($overview[0]->subject));
                    //set unread
                    $email->setUnread($this->unRead);
                    //set sender
                    $header = imap_headerinfo($this->imapMailbox,$emailnumber);
                    $fromaddr = $header->from[0]->mailbox . "@" . $header->from[0]->host;
                    $email->setSender($fromaddr);
                    
                    //set data
                    $email->setDate($overview[0]->date);
                    
                    //set msgno
                    $msgno = $overview[0]->uid;
                    $email->setMsgno($msgno);
                    //set attachment path
                    if($this->is_attachment==true){
                        $email->setAttachments($this->attachements);
                        $this->is_attachment = false;
                        unset($this->attachements);
                    }
                    //put oubject into list
                    //array_push($emailList, $email);
                    $emailList[$msgno] = $email;
                }
            }
            else{
                $i=1;
                foreach($emails as $emailnumber){
                    $email = new MailObject();
                    $i++;
                    $overview = imap_fetch_overview($this->imapMailbox,$emailnumber,0);
                    //var_dump($overview);
                    $bodyText = imap_fetchbody($this->imapMailbox,$emailnumber,1);
                    //var_dump($bodyText);
                    //$text = trim(utf8_encode(quoted_printable_decode(imap_fetchbody($this->imapMailbox, $emailnumber, 1)))); 

                    $structure = imap_fetchstructure($this->imapMailbox, $emailnumber);
                    ///////////////////////////////////////////////////////datatest//////////////////////////////////////////
                    //var_dump($structure);
                    //echo "hahah".$structure->parts[0]->encoding."<br>";
                    //$text = $this->getdecodevalue(imap_fetchbody($this->imapMailbox, $emailnumber, 1),$structure->encoding);
                    //$text = quoted_printable_decode($text);
                    //echo $text;
                    //////////////////////////////////////////////////////////////////////////////////////////////////////////
                    
                    //get content of email
                    $text = $this->getMessage($this->imapMailbox, $emailnumber,$overview[0]->uid);
                    if(!isset($text)){
                        if(!isset($this->messageList['html'])){
                            $data = quoted_printable_decode($this->messageList['plain']);
                        }
                        else{
                            $data = quoted_printable_decode($this->messageList['html']);
                        }
                        //echo $data;
                        $email->setMessage($data);
                    }else{
                        $email->setMessage($text);
                    }
                    
                    //test for delete/////////////////////////////////
//                    $check = imap_mailboxmsginfo($this->imapMailbox);
//                    var_dump($check);
//                    imap_delete($this->imapMailbox, 1);
//                    var_dump($check);
//                    imap_expunge($this->imapMailbox);
//                    var_dump($check);
//                    die();
                    //////////////////////////////////////////////////////
                    
                    //setsubject
                    $email->setSubject(htmlspecialchars($overview[0]->subject));
                    //setunread
                    $email->setUnread($this->unRead);
                    //set sender
                    $header = imap_headerinfo($this->imapMailbox,$emailnumber);
                    $fromaddr = $header->from[0]->mailbox . "@" . $header->from[0]->host;
                    $email->setSender($fromaddr);
                    
                    //set data
                    $email->setDate($overview[0]->date);
                    
                    //set msgno
                    $msgno = $overview[0]->uid;
                    $email->setMsgno($msgno);
                    
                    //set attachment
                    if($this->is_attachment==true){
                        $email->setAttachments($this->attachements);
                        $this->is_attachment = false;
                         unset($this->attachements);
                    }
                    //put object into list
                    array_push($emailList, $email);
                    //make sure display the number of emails under requirement
                    if($i>$this->num_records){
                        break;
                    }
                }               
            }
        }

        $this->closeImapConnection();
        return $emailList;
    }
   
    /**
     * record the final filename which will not to be the same with the exist files
     * @var type 
     */
    protected $finalFilename;
    public function getAttachDate($absoult_savedirpath,$relative_savepath=null,$part,$mailbox,$mailnumber,$partno){
        //var_dump($part);
        $absoult_savedirpath = str_replace('\\', '/', $absoult_savedirpath);
        if(substr($absoult_savedirpath, strlen($absoult_savedirpath)-1) !='/'){
            $absoult_savedirpath .='/';
        }
        if($relative_savepath){
            $relative_savepath = str_replace('\\', '/', $relative_savepath);
            if(substr($relative_savepath, strlen($relative_savepath)-1) !='/'){
                $relative_savepath .='/';
            }
        }
        $message = array();
        $message['attachment']['type'][0] = 'text';
        $message['attachment']['type'][1] = 'multipart';
        $message['attachment']['type'][2] = 'message';
        $message['attachment']['type'][3] = 'application';
        $message['attachment']['type'][4] = 'audio';
        $message['attachment']['type'][5] = 'image';
        $message['attachment']['type'][6] = 'video';
        $message['attachment']['type'][7] = 'other';

        $messageType = $message["attachment"]["type"][$part->type] . "/" . strtolower($part->subtype);
        $messageSubtype = $part->subtype;

        $params = $part->dparameters;
        //var_dump($params);
        $filename=$part->dparameters[0]->value;
//        if(is_file($savedirpath.$filename)){
//            
//        }
//        $this->checkFilename($savedirpath,$filename);
//        echo $this->finalFilename;
//        die();      
        //echo $relative_savepath;
        
        $this->attachements[$filename] = $relative_savepath;
        
        $mege = imap_fetchbody($mailbox,$mailnumber,$partno);
        $fp=fopen($absoult_savedirpath.$filename,'w');
        $data=$this->getdecodevalue($mege,$part->encoding);
        //echo $part->type;
        
        fputs($fp,$data);
        fclose($fp);
    }
    
    /**
     * check for same files in the same fold 
     * @param type $savedirpath
     * @param type $filename
     */
    protected $i_rec = 1;
    protected $file_rec = null;
    function checkFilename($savedirpath,$filename){
        if(is_file($savedirpath.$filename)){
            $this->file_rec = '('.$this->i_rec.')'.$filename;
            $this->i_rec = $this->i_rec+1;
            $this->checkFilename($savedirpath,$this->file_rec);
        }else{
            return $this->file_rec;
           
        }
        return $this->file_rec;
    }
    
    /**
     * close imapConnection
     */
    public function closeImapConnection(){
        if(is_resource($this->imapMailbox)){
            imap_close($this->imapMailbox);
        }
    }
     
    /**
     * close imap server connection
     */
    public function closeSmtpConnection(){
        if(is_object($this->smtp)){
            $this->smtp = null;
        }
    }
    
    //get email content
    public function getMessage($imapMailbox,$imapnumber,$uid=0){
        //header
        $header = imap_header($imapMailbox,$imapnumber);
        
        //body
        $structure = imap_fetchstructure($imapMailbox, $imapnumber);
        //type = 0 if mail doesnt exist parts
        if(!isset($structure->parts)){
            $data = $this->getPart($imapMailbox,$imapnumber,$structure,0,$uid);
            $data = quoted_printable_decode($data); 
            return $data;
        }
        else{
            //exits parts
            foreach ($structure->parts as $partno0=>$p){
                $this->getPart($imapMailbox,$imapnumber,$p,$partno0+1,$uid);
            }
        }
    }
    
    protected function getPart($imapMailbox,$emailnumber,$p,$partno,$uid=0){
        if($partno==0){
           $data = imap_body($imapMailbox,$emailnumber);
           $data = $this->getdecodevalue($data, $p->encoding);
           $data = quoted_printable_decode($data); 
           return $data;
        }//simple
        //with parts
        else {
            if(isset($p->parts)){
                foreach ($p->parts as $partno0=>$p2){
                    //echo $partno.'.'.($partno0+1);
                    $this->getPart($imapMailbox,$emailnumber,$p2,$partno.'.'.($partno0+1));  // 1.2, 1.2.1, etc.
                }
            }
            else{
                if(isset($p->disposition) && strtolower($p->disposition)=='attachment'){
                    $this->is_attachment = true;
                    //atachment
                    //absolute path
                    $absolute_savepath = dirname($this->savedirpath).'/download';
                    //relative path
                    $relative_savepath = dirname($_SERVER['PHP_SELF']).'/download';
                   
                    //whether exists download fold
                    if(is_dir($absolute_savepath)){
                        //absolute path
                        $absolute_savepath = $absolute_savepath.'/'.$uid.$this->imapUsername.'/';
                        //relative path
                        $relative_savepath = $relative_savepath.'/'.$uid.$this->imapUsername.'/';
                        //whether exists user fold
                        if(is_dir($absolute_savepath)){
                            $this->getAttachDate($absolute_savepath,$relative_savepath,$p,$imapMailbox,$emailnumber,$partno);  
                            //if dir exits means already download file
                            
//                            $handler = opendir($savepath);
//                            while (($filename = readdir($handler)) !== false) {
//                                if($filename != "." && $filename != "..") {  
//                                   $this->attachements[$filename] = $savepath;
//                                }  
//                            }
//                            closedir($handler);
                        }
                        else{
                            mkdir($absolute_savepath,$mode=0777);
                            chmod($absolute_savepath, $mode=0777);
                            $this->getAttachDate($absolute_savepath,$relative_savepath,$p,$imapMailbox,$emailnumber,$partno);    
                        }
                    }else{
                        mkdir($absolute_savepath,$mode=0777);
                        chmod($absolute_savepath,$mode=0777);
                        $absolute_savepath = $absolute_savepath.'/'.$uid.$this->imapUsername;
                        $relative_savepath = $relative_savepath.'/'.$uid.$this->imapUsername;
                        mkdir($absolute_savepath,$mode=0777);
                        chmod($absolute_savepath, $mode=0777);
                        $this->getAttachDate($absolute_savepath,$relative_savepath,$p,$imapMailbox,$emailnumber,$partno);    
                    }
                }
                else{
                    if(strtolower($p->subtype) == 'plain'){
                        $data = imap_fetchbody($imapMailbox,$emailnumber,$partno);
                       
                        $data = $this->getdecodevalue($data, $p->encoding);
                       // echo $data;
                       // die();
                        $this->messageList['plain'] = $data;
                    }
                    else if(strtolower($p->subtype)=='html'){
                        $data = imap_fetchbody($imapMailbox,$emailnumber,$partno);
                        
                        $data = $this->getdecodevalue($data, $p->encoding);
                        
                        $this->messageList['html'] = $data;
                    }
                }
            }
        }
        
        
       
//        if (isset($p->parts)) {
//        foreach ($p->parts as $partno0=>$p2)
//            $this->getPart($imapMailbox,$emailnumber,$p2,$partno.'.'.($partno0+1));  // 1.2, 1.2.1, etc.
//        }
//        $data = $this->getdecodevalue($data, $p->encoding);
//        echo $data = quoted_printable_decode($data); 
//        return $data;
//        /////
//        $params = array();
//        if (isset($p->parameters)){
//            foreach ($p->parameters as $x){
//                $params[strtolower($x->attribute)] = $x->value; 
//                }
//            }
//        if (isser($p->dparameters)){
//            foreach ($p->dparameters as $x){
//                $params[strtolower($x->attribute)] = $x->value;
//            }
//        }
//        
//        //attachment
//        if ($params['filename'] || $params['name']) {
//            // filename may be given as 'Filename' or 'Name' or both
//            $filename = ($params['filename'])? $params['filename'] : $params['name'];
//            // filename may be encoded, so see imap_mime_header_decode()
//            $attachments[$filename] = $data;  // this is a problem if two files have same name
//        }
    }
    /**
     * send email
     */
    public function sendEmail($configArray = array(),$person,$subject='',$body='',$file="") {

        if(isset($configArray['smtpFrom']))  $this->smtpFrom = $configArray['smtpFrom'];
        
        if(isset($configArray['smtpHost']))  $this->smtpHost = $configArray['smtpHost'];
        
        if(isset($configArray['smtpPort']))  $this->smtpPort = $configArray['smtpPort'];
    
        if(isset($configArray['smtpUsername'])){
            $this->smtpUsername = $configArray['smtpUsername'];
        }
        if(isset($configArray['smtpPassword'])){
            $this->smtpPassword = $configArray['smtpPassword'];
        }
        if(isset($configArray['smtpAuth'])){
            $this->smtpAuth = $configArray['smtpAuth'];
        }
        if(isset($configArray['smtpdebug'])){
            $this->smtpdebug = $configArray['smtpdebug'];
        }
        //make sure smtp configed or not
        if(is_null($this->smtp)){
            $this->smtpConnect();
        }
        //make sure there is have email address
        if(is_null($to = $person->getEmailAddress())){
            if($this->do_debug>=1){
                $this->edebug('no email address be provided');
                return false;
            }
        }

        
        $mime = new Mail_mime();
        $mime->_build_params['html_charset'] = "utf-8";
        $mime->_build_params['head_charset'] = "utf-8";
        $mime->setHTMLBody($body);
        
        echo $file;
        die();
        if($file <> ""){
            $mime->addAttachment($file, "text/html");
        }
        $new_body = $mime->get();     
        //create boundary
        $random_hash = md5(date('r', time()));
        $boundary = "PHP-alt-".$random_hash;
        
        $header = array(
            'From'=>$this->smtpFrom,
            'To'=>$to,
            'Subject'=>$subject,
            'Content-Type'=>'multipart/mixed; boundary="' . $boundary . '"'
            );
        
        $headers = $mime->headers($header);
        
        //var_dump($mail = $this->smtp->send($to, $header, $body));
        $mail = $this->smtp->send($to, $headers, $new_body);
        if (PEAR::isError($mail)) {
            echo "fail to send this mail";
            return false;
        }
        else{
            echo "success";
            return true;
        }
    }
    
    public function smtpConnect(){
        
        $this->smtp = Mail::factory('smtp', array(
                    'host' => $this->smtpHost,
                    'port' => $this->smtpPort,
                    'auth' => $this->smtpAuth,
                    'username' => $this->smtpUsername,
                    'password' => $this->smtpPassword,
                    'debug' => $this->smtpdebug
            ));
        //var_dump($this->smtp);        
    }
    
      
    public function deleteImapMail(){
        echo $this->imapHost;
        echo "this is deleteImapMail function";
    }
    
    
}