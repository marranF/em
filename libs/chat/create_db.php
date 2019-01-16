<?php

$bd=mysql_connect('localhost','root','rootroot');
if($bd)
  $idbd=mysql_query("create database app",$bd);
mysql_selectdb('app');


mysql_query("CREATE TABLE chat_users(
      id INT AUTO_INCREMENT NOT NULL,
      login text,
      PRIMARY KEY(id))")or die (mysql_error());


mysql_query("CREATE TABLE chat_messages(
      id INT AUTO_INCREMENT NOT NULL,
      id_user int,
      message text,
      likes int,
      data datetime,
      PRIMARY KEY(id),
      FOREIGN KEY(id_user) REFERENCES chat_users(id))")or die (mysql_error());
      
mysql_query("CREATE TABLE chat_additions(
      id INT AUTO_INCREMENT NOT NULL,
      id_message INT NOT NULL,
      addition text,
      type int,
      PRIMARY KEY(id),
      FOREIGN KEY(id_message) REFERENCES chat_messages(id))")or die (mysql_error());
      

mysql_query("INSERT INTO chat_users (login) VALUES ('admin')") or die (mysql_error());
mysql_query("INSERT INTO chat_users (login) VALUES ('user1')") or die (mysql_error());
mysql_query("INSERT INTO chat_messages (id_user,message,likes,data) VALUES (2,'message 1',0,'".date('Y-m-d H:i:s')."')") or die (mysql_error());
mysql_query("INSERT INTO chat_messages (id_user,message,likes,data) VALUES (1,'message 2',0,'".date('Y-m-d H:i:s')."')") or die (mysql_error());
mysql_query("INSERT INTO chat_messages (id_user,message,likes,data) VALUES (2,'message 3',0,'".date('Y-m-d H:i:s')."')") or die (mysql_error());
mysql_query("INSERT INTO chat_messages (id_user,message,likes,data) VALUES (1,'message 4',0,'".date('Y-m-d H:i:s')."')") or die (mysql_error());