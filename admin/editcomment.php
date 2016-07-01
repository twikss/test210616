<?php
  ///////////////////////////////////////////////////
  // Гостевая книга с использованием MySQL
  // 
  // Крючков П.С. (razirus@yandex.ru)
  ///////////////////////////////////////////////////
  // Получаем соединение с базой данных
  include "../config.php";
  // Добавляем ответ модератора на сообщение с первичным ключом $id_msg
  $query = "UPDATE guest SET answer = '".$_POST["answer"]."',
                             msg = '".$_POST["msg"]."' 
           WHERE id_msg=".$_POST["id_msg"];
  if(mysql_query($query))
  {
      // После удачного добавления переходим к
      // дальнейшему администрированию гостевой книги
      print "<HTML><HEAD>\n";
      print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=index.php?start=".$_POST["start"]."'>\n";
      print "</HEAD></HTML>\n";
  }
  // В случае неудачи выводим сообщение об ошибке
  else puterror("Ошибка при обращении к гостевой книге");
?>