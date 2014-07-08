<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<html>
    <head></head>
    
    <body>

<?php
include_once '../class.Cmail.php';
include_once '../class.person.php';
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$t = new Cmail();

$p = new Person();
$p->setName("harvey");
$p->setEmailAddress("wei2215038@gmail.com");
//set 

$subject ='welcome to classicbet';
$message = 'hello world for test email';


//$res = $t->sendEmail(
//        array(
//            'smtpFrom'=>'harvey@classicbet.com.au',
//            'smtpHost'=>'mail.classicbet.com.au',
//            'smtpPort'=>'587',
//            'smtpUsername'=>'harvey@classicbet.com.au',
//            'smtpPassword'=>'Classy01',
//            'smtpAuth'=>true,
//            'debug'=>false
//        ),
//        $p, $subject , $message
//        );
//$t->closeImapConnection();
//
//$list = $t->getEmailList(
//        array(
//            'imapHost'=>'mail.classicbet.com.au:993/ssl/novalidate-cert',
//            'imapPort'=>'993',
//            'imapUsername'=>'harvey@classicbet.com.au',
//            'imapPassword'=>'Classy01'
//        ),
//        array(
//
//            'numRecords'=>6,
//
//            'unRead'=>false,
//            //'senderEmail'=>'noreply@insideicloud.icloud.com'
//        )
//        );


$list = $t->getEmailList(
        array(
            'imapHost'=>'imap.gmail.com:993/ssl/novalidate-cert',
            'imapPort'=>'993',
            'imapUsername'=>'wei2215038@gmail.com',
            'imapPassword'=>'sally19870627'
        ),
        array(
            'numRecords'=>6,
            'unRead'=>false,
            //'senderEmail'=>'noreply@insideicloud.icloud.com'
        )
        );

//if(!$list){
//    echo "no emails found";
//}
//
//else{
//    foreach ($list as $email){
//        echo "is_read: ";
//        if(!$email->getUnread()){
//           echo "false";
//        }
//        else{
//            echo "true";
//        }
//        echo "<br>";
//        echo "Subject: ".$email->getSubject()."<br>";
//        echo "Sender: ".$email->getSender()."<br>";
//        echo "Message: ".$email->getMessage()."<br>";
//        $ar = $email->getAttachments();
//        var_dump($ar);
//        //echo implode(',', $ar);
////        foreach($ar as $filename => $attachment){
////            echo "attachment: ".$filename."<br>";
////            echo "attachment path: ".$attachment;
////            echo "<br>";
////        }
//        echo "messageno:".$email->getMsgno()."<br>";
//        echo "date:".$email->getDate()."<br>";
//        echo '-----------------------------------------------------------------------------------------------------<br>';
//        echo '<br>';
//    }
//}

//$t->deleteImapMail();
//
//$t->checkFilename("/opt/lampp/htdocs/emailAPI/download/2harvey@classicbet.com.au/", "1.html");
//
//echo preg_replace('/\([0-9]\)/', '', '(4)(3)(2)(1)1.html');

?>
    </body>
</html>