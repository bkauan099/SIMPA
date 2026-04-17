<?php

$host = 'db.mjudzspubsmoeedejrlw.supabase.co';
$port = '5432';
$dbname = 'postgres';
$user = 'postgres';
$pass = 'Simpas@2026';

try {
    // Usamos 'pgsql:' pois o Supabase roda PostgreSQL por baixo dos panos
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $pass);
    
    // Configuração para o PHP avisar caso dê algum erro na comunicação
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
?>