<?php

$host = "localhost"; // Altere se necessário
$usuario = "root"; // Substitua pelo usuário do seu banco de dados
$senha = "219751672Dd*"; // Substitua pela senha do seu banco de dados
$banco = "octaflow_bd"; // Nome do banco de dados

// Criar conexão
$conn = new mysqli($host, $usuario, $senha, $banco);

// Verificar conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
} else {
    echo "Conexão bem-sucedida!";
}

?>
