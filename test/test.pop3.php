<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('Net/POP3.php');

$pop3 =& new Net_POP3;
$pop3->connect('ssl://pop.gmail.com', 995);
$pop3->login('harvey850331@gmail.com', '32140hhw', true);
$msgCnt = $pop3->numMsg();
for($i=$msgCnt;$i>0;$i--){
    $headers = $pop3->getParsedHeaders($i); 
    $from = mb_decode_mimeheader($headers['From']);
    $subject = mb_decode_mimeheader($headers['Subject']);
    echo "From: $from<br>";
    //echo "Subject: $subject\n";
}

//$from = mb_decode_mimeheader($headers['From']);
//$subject = mb_decode_mimeheader($headers['Subject']);
//$content = $pop3->getBody($msgCnt); 
$pop3->disconnect(); 


//echo "From: $from\n";
//echo "Subject: $subject\n";
//echo "Body: $content\n";

?>