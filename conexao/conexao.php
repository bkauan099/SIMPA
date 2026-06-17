<?php

// 1. Buscando os dados dinamicamente
$host = getenv('DB_HOST') ?: $_ENV['DB_HOST'];
$port = getenv('DB_PORT') ?: $_ENV['DB_PORT'];
$dbname = getenv('DB_NAME') ?: $_ENV['DB_NAME'];
$user = getenv('DB_USER') ?: $_ENV['DB_USER'];
$pass = getenv('DB_PASS') ?: $_ENV['DB_PASS'];

try {
    // 2. Conectando ao PostgreSQL (Supabase)
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require", $user, $pass);
    
    // 3. Configuração de erros do PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
?>