<?php
require_once '/var/www/estate/data/www/m16-elite.ru/top-100/wp-content/themes/twentyseventeen-top100/assets/php/PHPMailer/PHPMailerAutoload.php';

$name = isset($_POST['name'])?$_POST['name']:NULL;
$email = isset($_POST['email'])?$_POST['email']:NULL;

$mail = new PHPMailer;
$mail->CharSet = 'UTF-8';

// Настройки SMTP
$mail->isSMTP();
$mail->SMTPAuth = true;
$mail->SMTPDebug = 0;

$mail->Host = "ssl://smtp.mail.ru";
$mail->Port = 465;
$mail->Username = "top100@m16.bz";
$mail->Password = "0#ri3MNcQTao";

// От кого
$mail->setFrom('top100@m16.bz', 'top100@m16.bz');        

// Кому
$mail->addAddress('mashafrlva@mail.ru', '');

// Тема письма
$mail->Subject = "Новый партнёрский запрос ТОП-100";

// Тело письма
$body = '<p><strong>Имя: '.$name.'</strong></p>
		<p><strong>Email: '.$email.'</strong></p>
		<p><strong>Время отправки: '.date('l jS \of F Y H:i:s A').'</strong></p>';
$mail->msgHTML($body);

$mail->send();



/*
$nmail = new PHPMailer;
$nmail->CharSet = 'UTF-8';

// Настройки SMTP
$nmail->isSMTP();
$nmail->SMTPAuth = true;
$nmail->SMTPDebug = 0;

$nmail->Host = "ssl://smtp.mail.ru";
$nmail->Port = 465;
$nmail->Username = "top100@m16.bz";
$nmail->Password = "0#ri3MNcQTao";

// От кого
$nmail->setFrom('top100@m16.bz', 'top100@m16.bz');        

// Кому
$nmail->addAddress($email, '');

// Тема письма
$nmail->Subject = "Партнёрский запрос ТОP-100";

// Тело письма
$body = '<p><strong>Вы отправили запрос на партнёрство с TOP-100 | М16-ELITE</strong></p>
		<p><strong>Имя: '.$name.'</strong></p>
		<p><strong>Email: '.$email.'</strong></p>
		<p><strong>Время отправки: '.date('l jS \of F Y H:i:s A').'</strong></p>';
$nmail->msgHTML($body);

$nmail->send();
*/
?>