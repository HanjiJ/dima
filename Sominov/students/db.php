<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "Tracker";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Ошибка подключения: " . mysqli_connect_error());
}
?>