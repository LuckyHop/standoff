<?php
$host = 'localhost';
$dbname = 'exam';
$username = 'root';
$pass = '';

try{
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e){
    die("Ошибка подключения к БД: ". $e->getMessage());
}