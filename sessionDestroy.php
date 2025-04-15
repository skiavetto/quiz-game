<?php
session_start(); // Garante que a sessão está ativa

unset($_SESSION['quizId']);
unset($_SESSION['questions']);
unset($_SESSION['quizTitle']);
unset($_SESSION['nQuestion']);
unset($_FILES['img']['name']);

session_write_close(); // Garante que a sessão seja salva antes do script terminar
?>
