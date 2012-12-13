<?php
defined ( 'IS_ME' ) or exit ();
$email_cache_data = $Object_filecache->get ( 'email' );
function send_email($send_to_email, $send_to_name, $send_content) {
	global $email_cache_data;
	require_once LIB_URL . 'class_phpmailer.php';
	$mail = new PHPMailer ();
	switch ($email_cache_data ['email_method']) {
		case 0 : //SMTP
			{
				$mail->CharSet = $email_cache_data ['SMTP'] ['email_char']; //设置采用gb2312中文编码
				$mail->IsSMTP (); //设置采用SMTP方式发送邮件
				$mail->Host = $email_cache_data ['SMTP'] ['email_host']; //设置邮件服务器的地址
				$mail->Port = $email_cache_data ['SMTP'] ['email_port']; //设置邮件服务器的端口，默认为25
				$mail->From = $email_cache_data ['SMTP'] ['from_email']; //设置发件人的邮箱地址
				$mail->FromName = $email_cache_data ['SMTP'] ['from_name']; //设置发件人的姓名
				$mail->SMTPAuth = $email_cache_data ['SMTP'] ['email_auth']; //设置SMTP是否需要密码验证，true表示需要
				$mail->Username = $email_cache_data ['SMTP'] ['email_user'];
				$mail->Password = $email_cache_data ['SMTP'] ['email_password'];
				$mail->Subject = $email_cache_data ['SMTP'] ['from_email_title']; //设置邮件的标题
				$mail->AltBody = "text/html"; // optional, comment out and test
				$mail->Body = $send_content;
				$mail->IsHTML ( true ); //设置内容是否为html类型
				$mail->WordWrap = $email_cache_data ['SMTP'] ['email_wordwrap']; //设置每行的字符数
				$mail->AddReplyTo ( $email_cache_data ['SMTP'] ['replay_email'], $email_cache_data ['SMTP'] ['replay_name'] ); //设置回复的收件人的地址
				$mail->AddAddress ( $send_to_email, $send_to_name ); //设置收件的地址
				return $mail->Send ();
			}
	}
}
