<?php
session_start();
require_once 'conexao.php'; 

if (!isset($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {
    header("Location: login.php?erro=nao_logado_wishlist");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== "POST" || !isset($_POST['jogo_id'])) {
    header("Location: home.php");
    exit();
}

$jogo_id = filter_var($_POST['jogo_id'], FILTER_VALIDATE_INT);
$usuario_id = $_SESSION['usuario_id'];
$redirect_url = isset($_POST['redirect']) ? $_POST['redirect'] : 'home.php';

if ($jogo_id === false) {
    header("Location: " . $redirect_url . "&status_wishlist=erro_id");
    exit();
}

$sql_check = "SELECT id FROM wishlist WHERE jogo_id = ? AND usuario_id = ?";
$stmt_check = $conn->prepare($sql_check);

if ($stmt_check) {
    $stmt_check->bind_param("ii", $jogo_id, $usuario_id);
    $stmt_check->execute();
    $resultado_check = $stmt_check->get_result();
    $esta_na_wishlist = ($resultado_check->num_rows > 0);
    $stmt_check->close();
} else {
    $conn->close();
    header("Location: " . $redirect_url . "&status_wishlist=erro_db_check");
    exit();
}


if ($esta_na_wishlist) {
    $sql_action = "DELETE FROM wishlist WHERE jogo_id = ? AND usuario_id = ?";
    $status_param = "removido";
} else {
    $sql_action = "INSERT INTO wishlist (jogo_id, usuario_id) VALUES (?, ?)";
    $status_param = "adicionado";
}

$stmt_action = $conn->prepare($sql_action);
if ($stmt_action) {
    $stmt_action->bind_param("ii", $jogo_id, $usuario_id);

    if ($stmt_action->execute()) {
        $stmt_action->close();
        $conn->close();
        header("Location: " . $redirect_url . "&status_wishlist=" . $status_param);
        exit();
    } else {
        $stmt_action->close();
        $conn->close();
        header("Location: " . $redirect_url . "&status_wishlist=erro_execucao");
        exit();
    }
} else {
    $conn->close();
    header("Location: " . $redirect_url . "&status_wishlist=erro_db_action");
    exit();
}
?>