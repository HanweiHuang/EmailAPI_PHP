
<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once 'utilities/class.ImpCmysql.php';
include_once 'class.Cmail.php';
include_once 'utilities/class.view.php';


$db = ImpCmysql::getInstance();
$c = new Cmail();

$list = $c->getEmailList(
        array(
            'imapHost'=>'imap.gmail.com:993/ssl/novalidate-cert',
            'imapPort'=>'993',
            'imapUsername'=>'wei2215038@gmail.com',
            'imapPassword'=>'sally19870627'
        ),
        array(
            'numRecords'=>1,
            'unRead'=>false,
            'senderEmail'=>'claire@performance.edu.au'
        )
        );

if(!$list){}

else{
    foreach($list as $email){
        $sender = $email->getSender();
        $uid = $email->getMsgno();
        $date = $email->getDate();
        $unread = $email->getUnread();
        
        echo $content = $email->getMessage();       
        //$content = mysql_real_escape_string($content);
        $subject = $email->getSubject();
        $attachments = $email->getAttachments();
        //var_dump($attachments);
        $attarray = array();
        foreach ($attachments as $key=>$attachment){
            $attarray[$key] = $attachment.$key;
        }
        $attachment_path = implode(',', $attarray);
        

        //$attachment_path='';\
        if(!empty($email->getInlines())){
            $inlines = $email->getInlines();
            $inlinearray = array();
            foreach($inlines as $key=>$inline){
                $inlinearray[$key] = $inline.$key;
            }
        }
        
        
        
        //add to database;
        var_dump($db->addMailFromImap($uid,$content,$subject,$date,$sender,$unread,$attachment_path));
        
        
    }
}


//$view = new view('newMain.php');
//$view->setdata('list', $list);
//$view->output();