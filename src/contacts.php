<?php
$recipient_email    = $_POST["admin_email"]; //recepient
$from_email         = "no-reply@tato.agency"; //from email using site domain.


// if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
// 		die('Sorry Request must be Ajax POST'); //exit script
// }

if($_POST){

		$sender_name    = filter_var($_POST["name"], FILTER_SANITIZE_STRING); //capture sender name
		$send_email    = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL); //capture sender name
		$phone_number   = filter_var($_POST["phone"], FILTER_SANITIZE_NUMBER_INT);
		$subject = 'Заявка с сайта Tato.agency';
		$mailTheme        = filter_var($_POST["form_subject"], FILTER_SANITIZE_STRING);
		$attachments = $_FILES['fileFF'];
		
		
		$file_count = count($attachments['name']); //count total files attached
		$boundary = md5("sanwebe.com"); 
		
		//construct a message body to be sent to recipient
		$message_body .=  "Тема письма: $mailTheme\n";
		$message_body .=  "Имя: $sender_name\n";
		$message_body .=  "Email: $send_email\n";
		$message_body .=  "Телефон: $phone_number\n";
		$message_body .=  "Документы: $document\n";
		
		
		if($file_count > 0){ //if attachment exists
				//header
				$headers = "MIME-Version: 1.0\r\n"; 
				$headers .= "From:".$from_email."\r\n"; 
				$headers .= "Reply-To: ".$sender_email."" . "\r\n";
				$headers .= "Content-Type: multipart/mixed; boundary = $boundary\r\n\r\n"; 
				
				//message text
				$body = "--$boundary\r\n";
				$body .= "Content-Type: text/plain; charset=UTF-8\r\n";
				$body .= "Content-Transfer-Encoding: base64\r\n\r\n"; 
				$body .= chunk_split(base64_encode($message_body)); 
				// $body .= $message_body . "\r\n";

				//attachments
				for ($x = 0; $x < $file_count; $x++){       
						if(!empty($attachments['name'][$x])){

								if($attachments['error'][$x]>0) //exit script and output error if we encounter any
								{
										$mymsg = array( 
												1=>"The uploaded file exceeds the upload_max_filesize directive in php.ini", 
												2=>"The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form", 
												3=>"The uploaded file was only partially uploaded", 
												4=>"No file was uploaded", 
												6=>"Missing a temporary folder" ); 
										print  json_encode( array('type'=>'error',$mymsg[$attachments['error'][$x]]) ); 
										exit;
								}
								
								//get file info
								$file_name = $attachments['name'][$x];
								$file_size = $attachments['size'][$x];
								$file_type = $attachments['type'][$x];
								
								//read file 
								$handle = fopen($attachments['tmp_name'][$x], "r");
								$content = fread($handle, $file_size);
								fclose($handle);
								$encoded_content = chunk_split(base64_encode($content)); //split into smaller chunks (RFC 2045)
								
								$body .= "--$boundary\r\n";
								$body .="Content-Type: $file_type; name=".$file_name."\r\n";
								$body .="Content-Disposition: attachment; filename=".$file_name."\r\n";
								$body .="Content-Transfer-Encoding: base64\r\n";
								$body .="X-Attachment-Id: ".rand(1000,99999)."\r\n\r\n"; 
								$body .= $encoded_content; 
						}
				}

		}else{ //send plain email otherwise
			 $headers = "From:".$from_email."\r\n".
			 "Reply-To: ".$sender_email. "\n" .
			 "X-Mailer: PHP/" . phpversion();
			 $body = $message_body;
	 }

	 $sentMail = mail($recipient_email, $subject, $body, $headers);
	 
		if($sentMail) //output success or failure messages
		{       
				print json_encode(array('type'=>'done', 'text' => 'Thank you for your email'));
				exit;
		}else{
				print json_encode(array('type'=>'error', 'text' => 'Could not send mail! Please check your PHP mail configuration.'));  
				exit;
		}
}