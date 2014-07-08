<?php 
$mailuser="harvey850331@gmail.com";
$mailpass="32140hhw";
 
$mailhost="{imap.gmail.com/imap/ssl/novalidate-cert}INBOX";
//$mailhost="{imap.gmail.com:993/imap/ssl}INBOX";
 
 
$mailbox = imap_open($mailhost,$mailuser,$mailpass) or die("<br />\nFAILLED! ".imap_last_error());

$list = imap_list($mailbox, $mailhost, "*");
if(is_array($list)){
    foreach ($list as $val){
        echo imap_utf7_decode($val) ."hahahah"."\n";
    }
}
else {
    echo "failed:".  imap_last_error();
}

$emails = imap_search($mailbox,'SEEN FROM "noreply@insideicloud.icloud.com"');

var_dump($emails);
$output = '';
rsort($emails);
 

echo count($emails); 
/* for every email... */
foreach($emails as $email_number) {
	
	/* get information specific to this email */
	$overview = imap_fetch_overview($mailbox,$email_number,0);
	$message = imap_fetchbody($mailbox,$email_number,1);
	var_dump($message);
        
	var_dump($overview[0]);
	//echo $message[0];
	/* output the email header information */
	$output.= '<div class="toggler '.($overview[0]->seen ? 'read' : 'unread').'">';
	//$output.= '<span class="subject">'.$overview[0]->subject.'</span> ';
	$output.= '<span class="from">'.$overview[0]->from.'</span>';
	//$output.= '<span class="date">on '.$overview[0]->date.'</span>';
	$output.= '</div>';
	
	/* output the email body */
//	$output.= '<div class="body">'.$message.'</div>';
}

echo $output;
imap_close($mailbox);
 












