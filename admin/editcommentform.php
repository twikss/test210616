<?php
  ///////////////////////////////////////////////////
  // Гостевая книга с использованием MySQL
  // 
  // Крючков П.С. (razirus@yandex.ru)
  ///////////////////////////////////////////////////
  // Это форма для добавления ответа администратора на сообщение.
  if($title == "") $title = "Ответ администратора на сообщение";
  $titlepage='Добавление мероприятий';  
  $helppage='Для добавления мероприятия заполните поля: "Название" и "Содержание". Нажмите кнопку "Добавить"';
  // Получаем соединение с базой данных
  include "../config.php";
  // Извлекаем параметр $id_msg из строки запроса
  $id_msg = $_GET['id_msg'];
  $start = $_GET['start'];
  // Запрос к базе данных для извлечения сообщении с
  // первичным ключом $id_msg
  $query = "SELECT * FROM guest 
            WHERE id_msg = $id_msg";
  $gst = mysql_query($query);
  if ($gst)
  {
    // Преобразуем полученную информацию в ассоциативный массив
    $guest = mysql_fetch_array($gst);
  }
  // В случае неудачи выводим сообщение об ошибке
  else puterror("Ошибка при обращении к гостевой книге");
  include "topadmin.php";
?>
<table><tr><td>
<p class=boxmenu><a class=menu href="index.php">Вернуться в администрирование гостевой книги</a></p>
</td></tr></table>
<center><br>
<table><tr><td>
<form action=editcomment.php method=post>
<textarea class=input cols=42 rows=5 name=msg><? echo $guest['msg']; ?></textarea><br>
<textarea class=input cols=42 rows=5 name=answer><? echo $guest['answer']; ?></textarea><br>
<input class=button type=submit value="Исправить">
<input type=hidden name=id_msg value=<?php echo $id_msg; ?>>
<input type=hidden name=start value=<?php echo $start; ?>>
</form>
</td></tr></table>
</center>
<?  include "bottomadmin.php"; ?>