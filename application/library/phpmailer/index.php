<?php
require("class.phpmailer.php");

$mail = new PHPMailer();

$mail->IsSMTP();                   // 设置使用 SMTP
$mail->Host = "61.172.255.101";          // 指定的 SMTP 服务器地址
$mail->SMTPAuth = true;                // 设置为安全验证方式
$mail->Username = "service@kaible.com";             // SMTP 发邮件人的用户名
$mail->Password = "kaibleservice";             // SMTP 密码

$mail->From = "service@kaible.com";
$mail->FromName = "凯搏网";
$mail->AddAddress("xiahui@kaible.com");
//$mail->AddAddress("terryxiahui@yahoo.com.cn","dalilng");
//$mail->AddAddress("xiahui@kaible.com","daling");  // 可选
//$mail->AddReplyTo("xiahui@kaible.com", "TERRY2");

$mail->WordWrap = 50;                 // set word wrap to 50 characters
//$mail->AddAttachment("/var/tmp/file.tar.gz");     // 加附件
//$mail->AddAttachment("/tmp/image.jpg", "new.jpg");  // 附件，也可选加命名附件
$mail->IsHTML(true);                  // 设置邮件格式为 HTML

$mail->Subject = "请迅速给我回邮件,好么";     // 标题
$mail->Body  = '<B>邮件内容为空</B>'; // 内容
//$mail->AltBody = "This is the body in plain text for non-HTML mail clients"; // 附加内容

if(!$mail->Send())
{
  echo "Message could not be sent. <p>";
  echo "Mailer Error: " . $mail->ErrorInfo;
  exit;
}

echo "Message has been sent";
?>

