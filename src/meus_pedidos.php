<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Busca pedidos e seus itens
$sql = "SELECT p.id as pedido_id, p.data_pedido, p.valor_total, p.status, p.metodo_pagamento,
               i.quantidade, i.preco_unitario, j.titulo
        FROM pedidos p
        JOIN itens_pedido i ON p.id = i.pedido_id
        JOIN jogos j ON i.jogo_id = j.id
        WHERE p.usuario_id = ?
        ORDER BY p.data_pedido DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

$pedidos = [];
while ($row = $result->fetch_assoc()) {
    $pedidos[$row['pedido_id']]['info'] = [
        'data' => $row['data_pedido'],
        'total' => $row['valor_total'],
        'status' => $row['status'],
        'pagamento' => $row['metodo_pagamento']
    ];
    $pedidos[$row['pedido_id']]['itens'][] = [
        'titulo' => $row['titulo'],
        'qtd' => $row['quantidade'],
        'preco' => $row['preco_unitario']
    ];
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meus Pedidos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-white font-sans">
    
    <nav class="bg-slate-900/80 backdrop-blur-md border-b border-slate-800 p-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <a href="home.php" class="text-2xl font-black">GAME<span class="text-indigo-500">STORE</span></a>
            <a href="home.php" class="text-slate-300 hover:text-white">Voltar à Loja</a>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto py-12 px-4">
        <h1 class="text-3xl font-bold mb-8 border-l-4 border-indigo-500 pl-4">Meus Pedidos</h1>

        <?php if (isset($_GET['sucesso'])): ?>
            <div class="bg-green-500/20 border border-green-500 text-green-300 p-4 rounded-lg mb-8 text-center">
                <p class="font-bold text-lg">Compra realizada com sucesso!</p>
                <p>Seus jogos já estão disponíveis na sua conta.</p>
            </div>
        <?php endif; ?>

        <div class="space-y-6">
            <?php if (empty($pedidos)): ?>
                <p class="text-slate-400">Você ainda não fez nenhum pedido.</p>
            <?php else: ?>
                <?php foreach ($pedidos as $id => $pedido): ?>
                    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
                        <div class="bg-slate-800/50 p-4 flex justify-between items-center border-b border-slate-700">
                            <div>
                                <p class="text-xs text-slate-400 uppercase font-bold">Pedido #<?php echo $id; ?></p>
                                <p class="text-sm"><?php echo date('d/m/Y H:i', strtotime($pedido['info']['data'])); ?></p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-indigo-400">R$ <?php echo number_format($pedido['info']['total'], 2, ',', '.'); ?></p>
                                <span class="text-xs bg-green-500/20 text-green-400 px-2 py-1 rounded uppercase font-bold">
                                    <?php echo $pedido['info']['status']; ?>
                                </span>
                            </div>
                        </div>
                        <div class="p-4">
                            <?php foreach ($pedido['itens'] as $item): ?>
                                <div class="flex justify-between py-2 border-b border-slate-800/50 last:border-0">
                                    <span class="text-slate-300"><?php echo $item['qtd']; ?>x <?php echo $item['titulo']; ?></span>
                                    <span class="text-slate-500">R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></span>
                                </div>
                            <?php endforeach; ?>
                            <p class="text-xs text-slate-500 mt-2 pt-2">Pagamento via: <?php echo ucfirst($pedido['info']['pagamento']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>