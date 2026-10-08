<?php
$host = "localhost";
$user = "root";
$pass = ""; // Senha padrão do XAMPP é vazia
$dbname = "xss_practice";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>