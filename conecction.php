<?php
    $host = "localhost";
    $user = "root";
    $password = "";
    $dbname = "sirca_db";

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8",$user,$password);
        echo "Conexion exitosa";
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }catch (PDOException $e){
        die("Error de conexion: ".$e->getMessage());
    }
?>