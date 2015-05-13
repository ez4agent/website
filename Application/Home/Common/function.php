<?php

//发邮件的方法
function sendMail($to, $subject, $content,$file="") {
    
    vendor('phpmailer.class#phpmailer');
    $mail = new PHPMailer();
        
    $mail->IsSMTP();
    $mail->SMTPAuth = true; // enable SMTP authentication// 设置为安全验证方式
    $mail->Host = "smtp.163.com"; // sets GMAIL as the SMTP server
    $mail->Port = 25; // set the SMTP port

    $mail->Username = "shl0316010134@163.com"; // GMAIL username
    $mail->Password = "cj@138165"; // GMAIL password
    $mail->CharSet = "utf-8"; // 这里指定字符集！如果是utf-8则将gb2312修改为utf-8
    $mail->Encoding = "base64";

    $mail->From = "shl0316010134@163.com";
    $pieces = explode('@', $to);
    $mail->FromName = 'hello';
    $mail->Subject = $subject;
    $mail->Body = $content; //HTML Body

    $mail->WordWrap = 50; // set word wrap
    $mail->AddAddress($to, $pieces[0]); //邮件发送地址
    $mail->AddReplyTo($to, $pieces[0]); //恢复地址

    $mail->IsHTML(true); // send as HTML
    if ($mail->Send()) {
       echo "OK";
    }
}


function sendMail1($to, $subject, $content, $file='') {

    vendor('phpmailer.class#phpmailer');
    $mail = new PHPMailer();
    $mail->SMTPDebug = 1;

    $mail->isSMTP();
    //smtp需要鉴权 这个必须是true
    $mail->SMTPAuth=true;
    //链接qq域名邮箱的服务器地址
    $mail->Host = 'smtp.qq.com';
    //设置使用ssl加密方式登录鉴权
    $mail->SMTPSecure = 'ssl';
    //设置ssl连接smtp服务器的远程服务器端口号 可选465或587
    $mail->Port = 465;
    
    $mail->CharSet = 'UTF-8';
    //设置发件人姓名（昵称） 任意内容，显示在收件人邮件的发件人邮箱地址前的发件人姓名
    $mail->FromName = '';
    //smtp登录的账号 这里填入字符串格式的qq号即可
    $mail->Username ='544075565@qq.com';
    //smtp登录的密码 这里填入“独立密码” 若为设置“独立密码”则填入登录qq的密码 建议设置“独立密码”
    $mail->Password = 'syl@138165';
    //设置发件人邮箱地址 这里填入上述提到的“发件人邮箱”
    $mail->From = '544075565@qq.com';
    //邮件正文是否为html编码 注意此处是一个方法 不再是属性 true或false
    $mail->isHTML(true);
    //设置收件人邮箱地址 该方法有两个参数 第一个参数为收件人邮箱地址 第二参数为给该地址设置的昵称 不同的邮箱系统会自动进行处理变动 这里第二个参数的意义不大
    //添加多个收件人 则多次调用方法即可
    $pieces = explode('@', $to);
    $mail->addAddress($to,$pieces[0]);
    //添加该邮件的主题
    $mail->Subject = $subject;
    //添加邮件正文 上方将isHTML设置成了true，则可以是完整的html字符串 如：使用file_get_contents函数读取本地的html文件
    $mail->Body = $content;
    
    //发附件
     //发附件
    $mail->AddAttachment("Uploads/Stu/file/20150225/54ed8fcb334c0.jpg","1.jpg"); //设置邮件中的图片
    $status = $mail->send(); 
    //简单的判断与提示信息
    if($status) {
        echo 'OK';
    }else{
        echo '发送邮件失败，错误信息未：'.$mail->ErrorInfo;
    }
}

function sendMail2($to, $subject, $content, $file='') {

    vendor('phpmailer.class#phpmailer');
    $mail = new PHPMailer();
    $mail->SMTPDebug = 1;

    $mail->isSMTP();
    //smtp需要鉴权 这个必须是true
    $mail->SMTPAuth=true;
    //链接qq域名邮箱的服务器地址
    $mail->Host = 'smtp.qq.com';
    //设置使用ssl加密方式登录鉴权
    $mail->SMTPSecure = 'ssl';
    //设置ssl连接smtp服务器的远程服务器端口号 可选465或587
    $mail->Port = 465;

    $mail->CharSet = 'UTF-8';
    //设置发件人姓名（昵称） 任意内容，显示在收件人邮件的发件人邮箱地址前的发件人姓名
    $mail->FromName = '';
    //smtp登录的账号 这里填入字符串格式的qq号即可
    $mail->Username ='544075565@qq.com';
    //smtp登录的密码 这里填入“独立密码” 若为设置“独立密码”则填入登录qq的密码 建议设置“独立密码”
    $mail->Password = 'syl@138165';
    //设置发件人邮箱地址 这里填入上述提到的“发件人邮箱”
    $mail->From = '544075565@qq.com';
    //邮件正文是否为html编码 注意此处是一个方法 不再是属性 true或false
    $mail->isHTML(true);
    //设置收件人邮箱地址 该方法有两个参数 第一个参数为收件人邮箱地址 第二参数为给该地址设置的昵称 不同的邮箱系统会自动进行处理变动 这里第二个参数的意义不大
    //添加多个收件人 则多次调用方法即可
    $pieces = explode('@', $to);
    $mail->addAddress($to,$pieces[0]);
    //添加该邮件的主题
    $mail->Subject = $subject;
    //添加邮件正文 上方将isHTML设置成了true，则可以是完整的html字符串 如：使用file_get_contents函数读取本地的html文件
    $mail->Body = $content;

   

    $status = $mail->send();
    //简单的判断与提示信息
    if($status) {
        echo 'OK';
    }else{
        echo '发送邮件失败，错误信息未：'.$mail->ErrorInfo;
    }
}
?>