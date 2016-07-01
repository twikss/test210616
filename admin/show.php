<?php
  ///////////////////////////////////////////////////
  // Гостевая книга с использованием MySQL
  // 
  // Крючков П.С. (razirus@yandex.ru)
  ///////////////////////////////////////////////////
  // Получаем соединение с базой данных
  include "../config.php";
  // Формируем SQL-запрос
  $query = "UPDATE guest SET hide = 'show' 
            WHERE id_msg = ".$_GET["id_msg"];
  // Отображаем сообщение с первичным ключом $id_msg
  if(mysql_query($query))
  {
      // После удачного удаления сообщения переходим к
      // дальнейшему администрированию гостевой книги
      print "<HTML><HEAD>\n";
      print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=index.php?start=".$HTTP_GET_VARS["start"]."'>\n";
      print "</HEAD></HTML>\n";
  }
  // В случае неудачи выводим сообщение об ошибке
  else puterror("Ошибка при обращении к гостевой книге");
?>