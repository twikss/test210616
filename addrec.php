<?php
session_start();
$sid_add_theme = session_id();
// Устанавливаем соединение с базой данных
include "config.php";
$error = "";
$action = "";
// Возвращаем значение переменной action, переданной в урле
$action = $_POST["action"];
// Если оно не пусто - добавляем сообщение в базу данных
if (!empty($action)) 
{
  // Проверяем совпадает ли идентификатор сессии с
  // переданным в форме - защита а авто-постинга
  if($sid_add_theme != $_POST['sid_add_theme'])
  {
    $action = ""; 
    $error = $error."<LI>Ошибка добавления сообщения в гостевую книгу\n";
  }
  // Проверяем сообщение на слишком длинные слова
  $lenmsg = strlen($msg);
  $templen = 0;
  $temp = strtok($msg, " ");
  if (strlen($msg)>60)
  {
    while ($templen < $lenmsg)
    { 
      if (strlen($temp)>60)
      {
        $action = ""; 
        $error = $error."<LI>Текст сообщения содержит слишком много символов без пробелов\n";
        break;
      }
      else
      {
        $templen = $templen + strlen($temp) + 1;
      }
      $temp = strtok(" ");            
    }       
  }
  
  // Проверяем правильность ввода информации в поля формы
  if (empty($_POST["msg"])) 
  {
    $action = ""; 
    $error = $error."<LI>Вы не ввели сообщение\n";
  }
  if (empty($_POST["name"])) 
  {
    $action = ""; 
    $error = $error."<LI>Вы не ввели имя\n";
  }

  // При помощи регулярных выражений проверяем правильность ввода e-mail
  if(!empty($_POST["email"]))
  {
    if (!preg_match("/[0-9a-z_]+@[0-9a-z_^\.]+\.[a-z]{2,3}/i", $_POST["email"]))
    {
      $action = "";
      $error = $error."<LI>Неверно введен е-mail.&nbsp Введите e-mail в виде <i>something@server.com</i> \n";
    }
  }
  
  // Обрабатываем HTML-тэги и скрипты в сообщении и информации
  // об авторе, ограничиваем объём сообщения
  $name = substr($_POST["name"],0,32);
  $name = htmlspecialchars(stripslashes($name));
  $city = substr($_POST["city"],0,32);
  $city = htmlspecialchars(stripslashes($city));
  $email = substr($_POST["email"],0,32);
  $email = htmlspecialchars(stripslashes($email));
  $url = substr($_POST["url"],0,60);
  $url = htmlspecialchars(stripslashes($url));
  $msg = substr($_POST["msg"],0,1024);
  $msg = htmlspecialchars(stripslashes($msg));
  
  // Добавляем протокол в url, если пользователь забыл это сделать сам
  $url = strtr($url, "HTPF", "htpf");
  if (trim($url)!="")
  { 
    if (strtolower((substr($url, 0, 7))!="http://") && (strtolower(substr($url, 0, 7))!="ftp://")) $url="http://".$url;
  }   
      
  // Пытаемся вырезать мат, насколько это возможно ;-)
  $search_bad_words = array("'хуй'si","'пизд'si","'ёб'si",
                          "'сука'si","'суки'si","'дроч'si","'хуя'si","'ссуч'si");
  $replace = array("*","*","*","*","*","*","*","*");
  $msg = preg_replace($search_bad_words,$replace,$msg);
  $name = preg_replace($search_bad_words,$replace,$name);
  $city = preg_replace($search_bad_words,$replace,$city);

  if (empty($error)) 
  {
    $msg = nl2br($msg);
    // Обрабатываем встроенные тэги
    $msg = str_replace("[u]","<u>",$msg);
    $msg = str_replace("[U]","<u>",$msg);
    $msg = str_replace("[i]","<i>",$msg);
    $msg = str_replace("[I]","<i>",$msg);
    $msg = str_replace("[b]","<B>",$msg);
    $msg = str_replace("[B]","<B>",$msg);
    $msg = str_replace("[sub]","<SUB>",$msg);
    $msg = str_replace("[SUB]","<SUB>",$msg);
    $msg = str_replace("[sup]","<SUP>",$msg);
    $msg = str_replace("[SUP]","<SUP>",$msg);
    $msg = str_replace("[/u]","</u>",$msg);
    $msg = str_replace("[/U]","</u>",$msg);
    $msg = str_replace("[/i]","</i>",$msg);
    $msg = str_replace("[/I]","</i>",$msg);
    $msg = str_replace("[/b]","</B>",$msg);
    $msg = str_replace("[/B]","</B>",$msg);
    $msg = str_replace("[/SUB]","</SUB>",$msg);
    $msg = str_replace("[/sub]","</SUB>",$msg);
    $msg = str_replace("[/SUP]","</SUP>",$msg);
    $msg = str_replace("[/sup]","</SUP>",$msg);
    $msg = eregi_replace("(.*)\\[url\\](.*)\\[/url\\](.*)","\\1<a href=\\2>\\2</a>\\3",$msg);
    $msg = str_replace("\n"," ",$msg);
    $msg = str_replace("\r"," ",$msg);
    // Заменяем все одинарные кавычки обратными
    // защита от инъекционных запросов
    $name = str_replace("'","`",$name);
    $city = str_replace("'","`",$city);
    $email = str_replace("'","`",$email);
    $url = str_replace("'","`",$url);
    $msg = str_replace("'","`",$msg);
    // Запрос к базе данных на добавление сообщения
    $query = "INSERT INTO guest VALUES (0,
                                        '$name',
                                        '$city',
                                        '$email',
                                        '$url',
                                        '$msg',
                                        '-',
                                        NOW(),
                                        'show');";
    if(mysql_query($query))
    {
      // Если в конфигурационном файле $sendmail = true отправляем уведомление
      if($sendmail)
      {
        $thm = "guestbook - a new post";
        $msg = "post: $msg\nname: $name";
        mail($valmail, $thm, $msg);
      }
      // Возвращаемся на главную страницу если всё прошло удачно
      print "<HTML><HEAD>\n";
      print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=index.php'>\n";
      print "</HEAD></HTML>\n";
      exit();
    }
    else
    {
      // Выводим сообщение об ошибке в случае неудачи
      echo "<a href='index.php'>Вернуться</a>";
      echo("<P> Ошибка при добавлении сообщения</P>");
      echo("<P> $query</P>");
      exit();
    }
  }
}

if (empty($action)) 
{
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<title></title>
<link rel="StyleSheet" type="text/css" href="guestbook.css">
</head>
<body bottommargin="0" marginheight="0" marginwidth="0" rightmargin="0" leftmargin="0" topmargin="0">
<table border="0" cellspacing="0" width="100%" cellpadding="0">
    <tr>
        <td colspan="3" height="35"><p class="pcolor1"><nobr><b>Гостевая книга</nobr></b></p></td>
        <td width="100%" colspan="2"><nop></td>
    </tr>
    <tr align="center">
        <td width="150" colspan="2"><nop></td>
        <td height="4" bgcolor="#EAEAEA"><nop></td>
        <td bgcolor="silver"><nop></td>
        <td bgcolor="gray"><nop></td>       
    </tr>
</table>
<table width="100%">    
    <tr align="right">
        <td>
            <a class=link href="index.php" title="Вернуться в гостевую книгу">гостевая книга</a>&nbsp;&nbsp;
            <a class=link href="http://localhost" title="Вернуться на сайт">главная</a>
        </td>
        <td width="10%">&nbsp;</td>	
    </tr>   
</table>
<form action=addrec.php method=post>
<input type=hidden name=sid_add_theme value='<?php echo $sid_add_theme; ?>'>
<input type=hidden name=action value=post>
<table><tr valign="top"><td width="25%">&nbsp;</td><td>
<table border="0" align="center" cellpadding="6" cellspacing="0">
    <tr valign="top">
        <td colspan="3" height="60">
            <p class="pcolor2"><b>Добавление сообщения</b>
        </td>
    </tr>
    <tr>
        <td width="50"><p class=ptd><b><em class=em>Имя *</em></b></td>
        <td><input type=text name=name maxlength=32 size=25 value='<? echo $name; ?>'></td>
        <td rowspan="3" width="120">
            <p class=help>* Красным цветом выделены поля, обязательные для заполнения
        </td>
    </tr>
    <tr>
        <td><p class=ptd><b>&nbsp;&nbsp;&nbsp;Город</b></td>
        <td><input type=text name=city maxlength=32 size=25 value='<? echo $city; ?>'></td>
    </tr>
    <tr>
        <td><p class=ptd><b>&nbsp;&nbsp;&nbsp;<nobr>E-mail</nobr></b></td>
        <td><input type=text name=email size=25 maxlength=32 value='<? echo $email; ?>'></td>
    </tr>
    <tr>
        <td><p class=ptd><b>&nbsp;&nbsp;&nbsp;URL</b></td>
        <td colspan="2"><input type=text size=40 name=url maxlength=36 value='<? echo $url; ?>'></td>
    </tr>
    <tr>
        <td colspan="3" height="10"><nop></td>
    </tr>   
    <tr>
        <td colspan="3">
            <p class=ptd><b><em class=em>Сообщение *<em></b><br>
            <textarea cols=42 rows=5 name=msg><? echo $msg; ?></textarea>
        </td>
    </tr>   
    <tr>
        <td colspan="3">
            <input type="submit" value="Добавить">&nbsp;&nbsp;&nbsp;
        </td>
    </tr>           
</table>
</td><td>
<table border="0" cellspacing="1" cellpadding="4">
    <tr align="left"><td><p class=ptext><u><i><b><nobr>Поддерживаемые  тэги:</nobr></b></i></u></td></tr>
    <tr><td><p class=ptext><nobr>[b]<b>Жирный</b>[/b]</nobr></td></tr>
    <tr><td><p class=ptext><nobr>[i]<i>Наклонный</i>[/i]</nobr></td></tr>
    <tr><td><p class=ptext><nobr>[u]<u>Подчеркнутый</u>[/u]</nobr></td></tr>
    <tr><td><p class=ptext><nobr>[sup]<sup>Верхний индекс</sup>[/sup]</nobr></td></tr>
    <tr><td><p class=ptext><nobr>[sub]<sub>Нижний индекс</sub>[/sub]</nobr></td></tr>   
</table>
</td></tr></table>
</form>
</body>
</html>
<?php
  // Выводим сообщение об ошибке
  if (!empty($error)) 
  {
    print "<P><font color=green>Во время добавления записи произошли следующие ошибки: </font></P>\n";
    print "<UL>\n";
    print $error;
    print "</UL>\n";
  }
}
?>