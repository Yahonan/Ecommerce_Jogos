<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['carrinho']) || empty($_SESSION['carrinho'])) {
    header("Location: home.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$metodo_pagamento = $_POST['pagamento'] ?? 'Indefinido';


$itens_carrinho = array_count_values($_SESSION['carrinho']);
$ids_jogos = array_keys($itens_carrinho);
$placeholders = implode(',', array_fill(0, count($ids_jogos), '?'));
$tipos = str_repeat('i', count($ids_jogos));

$sql = "SELECT id, preco FROM jogos WHERE id IN ($placeholders)";
$stmt = $conn->prepare($sql);

$refs = [];
$refs[] = $tipos;
foreach ($ids_jogos as $key => $value) {
    $refs[] = &$ids_jogos[$key];
}
call_user_func_array([$stmt, 'bind_param'], $refs);

$stmt->execute();
$resultado = $stmt->get_result();

$valor_total_pedido = 0;
$dados_jogos = [];

while ($jogo = $resultado->fetch_assoc()) {
    $qtd = $itens_carrinho[$jogo['id']];
    $valor_total_pedido += $jogo['preco'] * $qtd;
    $dados_jogos[$jogo['id']] = ['preco' => $jogo['preco'], 'qtd' => $qtd];
}
$stmt->close();

$sql_pedido = "INSERT INTO pedidos (usuario_id, valor_total, metodo_pagamento) VALUES (?, ?, ?)";
$stmt_pedido = $conn->prepare($sql_pedido);
$stmt_pedido->bind_param("ids", $usuario_id, $valor_total_pedido, $metodo_pagamento);

if ($stmt_pedido->execute()) {
    $pedido_id = $stmt_pedido->insert_id;
    $stmt_pedido->close();

    $sql_item = "INSERT INTO itens_pedido (pedido_id, jogo_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)";
    $stmt_item = $conn->prepare($sql_item);

    foreach ($dados_jogos as $id_jogo => $dados) {
        $stmt_item->bind_param("iiid", $pedido_id, $id_jogo, $dados['qtd'], $dados['preco']);
        $stmt_item->execute();
    }
    $stmt_item->close();
    $_SESSION['carrinho'] = [];
    
    // Redireciona para uma página de "Meus Pedidos" ou Home com sucesso
    header("Location: meus_pedidos.php?sucesso=compra_realizada");
    exit();

} else {
    die("Erro ao processar pedido: " . $conn->error);
}

?>