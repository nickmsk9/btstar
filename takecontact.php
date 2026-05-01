<?php

require "include/bittorrent.php";

if ($HTTP_SERVER_VARS["REQUEST_METHOD"] != "POST")
 stderr("Error", "Method");

   dbconn(false);

    loggedinorreturn();

       $msg = trim($_POST["msg"]);
       $subject = trim($_POST["subject"]);

       if (!$msg)
    stderr("Ошибка","Напишите что нибудь!");

       if (!$subject)
    stderr("Ошибка","Вы не написали сообщение!");

     $added = "'" . get_date_time() . "'";
     $userid = $CURUSER['id'];
     $message = sqlesc($msg);
     $subject = sqlesc($subject);

 sql_query("INSERT INTO staffmessages (sender, added, msg, subject) VALUES($userid, $added, $message, $subject)") or sqlerr(__FILE__, __LINE__);

       if ($_POST["returnto"])
 {
   header("Location: " . $_POST["returnto"]);
   die;
 }

  stdhead();
  stdmsg("Отлично", "Ваше сообщение было отправленно администрации!");
       
       stdfoot();
       exit;
?>