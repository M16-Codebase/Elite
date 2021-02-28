<?php
// если была нажата кнопка "Отправить"
if($_POST['submit']) {
        // $_POST['title'] содержит данные из поля "Тема"
                $title = 'TEST';
                $mess =  'TEST';
        // $to - кому отправляем
                $to = 'ЯЩИК@ДОМЕН';
        // $from - от кого
                $from='ЯЩИК@ДОМЕН';
        // функция, которая отправляет наше письмо.
                mail($to, $title, $mess, 'from:'.$from, '-f'.$from);
                echo 'Спасибо! Ваше письмо отправлено.';
                } ?>
<form action="" method=post>
   <p>Вводный текст перед формой <p>
   <div align="center">Тeма<br />
   <input type="text" name="title" size="40"><br />Сообщение<br />
   <textarea name="mess" rows="10" cols="40"></textarea>
   <br />
   <input type="submit" value="Отправить" name="submit"></div>
</form>