<?php
namespace Home\Model;
use Think\Model;
//发件
class MailModel extends Model
{
    //发邮件
    public function sendmail($to,$title,$boby)
    {
        header("content-type:text/html;charset=utf-8");
        ini_set("magic_quotes_runtime",0);
        
        $mail_config = C('MAIL');
        
        try {
            $mail = new \PHPMailer();
            $mail->IsSMTP();
            $mail->CharSet='UTF-8'; //设置邮件的字符编码，这很重要，不然中文乱码
            $mail->SMTPAuth = true; //开启认证
            $mail->Port = $info['smtp_port'];
            $mail->Host = $info['smtp_host'];
            $mail->Username = $info['smtp_user'];
            $mail->Password = $info['smtp_pass'];
            $mail->AddReplyTo($info['smtp_user'],$info['send_name']);//回复地址
            $mail->From = $info['smtp_user'];
            $mail->FromName = $info['send_name'];
            $mail->AddAddress($to);
            $mail->Subject = $title;
            $mail->Body = $body;
            $mail->WordWrap = 80; // 设置每行字符串的长度
            $mail->IsHTML(true);
            $mail->Send();
            return true;
        } catch (phpmailerException $e) {
            echo "邮件发送失败：".$e->errorMessage();
        }
    }
}
?>