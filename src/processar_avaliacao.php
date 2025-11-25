<?php
session_start();
require_once 'conexao.php'; 

if (!isset($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {
    header("Location: login.php?erro=nao_logado_avaliacao");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== "POST" || !isset($_POST['jogo_id']) || !isset($_POST['nota']) || !isset($_POST['comentario'])) {
    header("Location: home.php");
    exit();
}

$jogo_id = filter_var($_POST['jogo_id'], FILTER_VALIDATE_INT);
$nota = filter_var($_POST['nota'], FILTER_VALIDATE_INT);
$comentario = trim($_POST['comentario']);
$usuario_id = $_SESSION['usuario_id'];
$redirect_url = "detalhe.php?id=" . $jogo_id;

if ($jogo_id === false || $nota === false || $nota < 1 || $nota > 5 || empty($comentario)) {
    header("Location: " . $redirect_url . "&status_avaliacao=erro_dados");
    exit();
}

$sql = "INSERT INTO avaliacoes (jogo_id, usuario_id, nota, comentario) 
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            nota = VALUES(nota), 
            comentario = VALUES(comentario),
            data_avaliacao = CURRENT_TIMESTAMP";

$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("iiss", $jogo_id, $usuario_id, $nota, $comentario);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: " . $redirect_url . "&status_avaliacao=sucesso");
        exit();
    } else {
        $stmt->close();
        $conn->close();
        header("Location: " . $redirect_url . "&status_avaliacao=erro_execucao");
        exit();
    }
} else {
    $conn->close();
    header("Location: " . $redirect_url . "&status_avaliacao=erro_db_action");
    exit();
}
?>
