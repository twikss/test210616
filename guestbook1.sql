CREATE TABLE guest (
  id_msg int(8) NOT NULL auto_increment,
  name tinytext NOT NULL,
  city tinytext NOT NULL,
  email tinytext NOT NULL,
  url tinytext NOT NULL,
  msg mediumtext NOT NULL,
  answer mediumtext NOT NULL,
  puttime datetime NOT NULL default '0000-00-00 00:00:00',
  hide enum('show','hide') NOT NULL default 'show',
  PRIMARY KEY  (id_msg)
) ENGINE=MyISAM;
INSERT INTO guest VALUES (1, 'Павел', 'Смоленск',  'razirus@yandex.ru', 'http://vk.com/', 'Это первое сообщение в моей гостевой книге', '-', '2016-06-30 12:00:01', 'show');
