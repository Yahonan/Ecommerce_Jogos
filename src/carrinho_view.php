<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['carrinho']) || empty($_SESSION['carrinho'])) {
    header("Location: home.php?alerta=carrinho_vazio");
    exit();
}

$itens_carrinho = array_count_values($_SESSION['carrinho']);
$ids_jogos = array_keys($itens_carrinho);

$placeholders = implode(',', array_fill(0, count($ids_jogos), '?'));
$tipos = str_repeat('i', count($ids_jogos));

$jogos_no_carrinho = [];
$total_carrinho = 0;

$sql = "SELECT id, titulo, preco, imagem_capa FROM jogos WHERE id IN ($placeholders)";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $refs = [];
    $refs[] = $tipos; 
    foreach ($ids_jogos as $key => $value) {
        $refs[] = &$ids_jogos[$key]; 
    }
    call_user_func_array([$stmt, 'bind_param'], $refs);
    
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    while ($jogo = $resultado->fetch_assoc()) {
        $jogo['quantidade'] = $itens_carrinho[$jogo['id']];
        $jogo['subtotal'] = $jogo['preco'] * $jogo['quantidade'];
        $total_carrinho += $jogo['subtotal'];
        $jogos_no_carrinho[] = $jogo;
    }
    $stmt->close();
}
$conn->close();

$total_formatado = number_format($total_carrinho, 2, ',', '.');
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Game Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-white font-sans antialiased">

    <nav class="sticky top-0 z-50 bg-slate-950/80 backdrop-blur-md border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <a href="home.php" class="text-2xl font-black tracking-tighter text-white">GAME<span class="text-indigo-500">STORE</span></a>
                <a href="home.php" class="text-slate-400 hover:text-white transition flex items-center gap-2 font-medium text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Continuar Comprando
                </a>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-white mb-10 flex items-center gap-3">
            <span class="w-2 h-8 bg-indigo-500 rounded-full"></span>
            Seu Carrinho e Checkout
        </h1>

        <div class="flex flex-col lg:flex-row gap-10">
            
            <div class="lg:w-2/3 space-y-6">
                <div class="bg-slate-900/50 border border-slate-800 rounded-2xl overflow-hidden shadow-lg">
                    <div class="p-6 border-b border-slate-800">
                        <h2 class="text-xl font-bold text-white">Itens no Carrinho</h2>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        <?php if (empty($jogos_no_carrinho)): ?>
                            <p class="text-slate-400 text-center py-8">Seu carrinho está vazio.</p>
                        <?php else: ?>
                            <?php foreach ($jogos_no_carrinho as $jogo): ?>
                                <div class="flex flex-col sm:flex-row items-center gap-6 pb-6 border-b border-slate-800 last:border-0 last:pb-0">
                                    
                                    <img src="<?php echo 'img/' . htmlspecialchars($jogo['imagem_capa'] ?? 'default.jpg'); ?>" 
                                         alt="Capa" class="w-24 h-32 object-cover rounded-lg shadow-md">
                                    
                                    <div class="flex-1 text-center sm:text-left">
                                        <h3 class="font-bold text-lg text-white mb-1"><?php echo htmlspecialchars($jogo['titulo']); ?></h3>
                                        <p class="text-sm text-slate-400">Valor unitário: R$ <?php echo number_format($jogo['preco'], 2, ',', '.'); ?></p>
                                    </div>
                                    
                                    <div class="flex items-center bg-slate-950 rounded-lg border border-slate-800 p-1">
                                        <form action="carrinho.php" method="POST" class="flex">
                                            <input type="hidden" name="jogo_id" value="<?php echo $jogo['id']; ?>">
                                            <button type="submit" name="acao" value="reduzir" 
                                                    class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800 rounded transition">
                                                -
                                            </button>
                                        </form>
                                        
                                        <span class="w-10 text-center font-bold text-white text-sm">
                                            <?php echo $jogo['quantidade']; ?>
                                        </span>

                                        <form action="carrinho.php" method="POST" class="flex">
                                            <input type="hidden" name="jogo_id" value="<?php echo $jogo['id']; ?>">
                                            <button type="submit" name="acao" value="aumentar" 
                                                    class="w-8 h-8 flex items-center justify-center text-indigo-400 hover:text-indigo-300 hover:bg-indigo-500/10 rounded transition">
                                                +
                                            </button>
                                        </form>
                                    </div>

                                    <div class="text-right min-w-[100px]">
                                        <p class="text-xs text-slate-500 uppercase font-bold">Subtotal</p>
                                        <p class="font-bold text-xl text-indigo-400">R$ <?php echo number_format($jogo['subtotal'], 2, ',', '.'); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="bg-slate-900/50 border border-slate-800 rounded-2xl p-6">
                    <h3 class="font-bold text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        Cupom de Desconto
                    </h3>
                    <div class="flex gap-3">
                        <input type="text" placeholder="Insira seu código" class="flex-grow bg-slate-950 border border-slate-700 text-white text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-3 outline-none transition-colors">
                        <button class="bg-slate-800 hover:bg-slate-700 text-white font-bold py-2 px-6 rounded-lg border border-slate-700 transition duration-150">Aplicar</button>
                    </div>
                </div>
            </div>
            
            <div class="lg:w-1/3">
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-8 shadow-xl sticky top-24">
                    <h2 class="text-xl font-bold mb-6 text-white border-b border-slate-800 pb-4">Resumo do Pedido</h2>
                    
                    <div class="flex justify-between text-slate-400 mb-2">
                        <p>Subtotal:</p>
                        <p>R$ <?php echo $total_formatado; ?></p>
                    </div>
                    <div class="flex justify-between text-slate-400 mb-6 border-b border-slate-800 pb-4">
                        <p>Descontos:</p>
                        <p class="text-green-400">- R$ 0,00</p>
                    </div>
                    
                    <div class="flex justify-between text-2xl font-black text-white mb-8">
                        <p>Total:</p>
                        <p class="text-indigo-400">R$ <?php echo $total_formatado; ?></p>
                    </div>

                    <h2 class="text-lg font-bold mb-4 text-white">Pagamento</h2>
                    
                    <form id="checkoutForm" action="processar_pagamento.php" method="POST">
                        
                        <div class="mb-6 space-y-3">
                            <label class="flex items-center space-x-3 p-3 rounded-lg border border-slate-800 bg-slate-950 cursor-pointer hover:border-indigo-500 transition">
                                <input type="radio" name="pagamento" value="pix" class="form-radio text-indigo-600 bg-slate-800 border-slate-600 focus:ring-indigo-500" checked onchange="toggleCartaoFields()">
                                <span class="font-medium">PIX (Aprovação Imediata)</span>
                            </label>
                            <label class="flex items-center space-x-3 p-3 rounded-lg border border-slate-800 bg-slate-950 cursor-pointer hover:border-indigo-500 transition">
                                <input type="radio" name="pagamento" value="credito" class="form-radio text-indigo-600 bg-slate-800 border-slate-600 focus:ring-indigo-500" onchange="toggleCartaoFields()">
                                <span class="font-medium">Cartão de Crédito</span>
                            </label>
                            <label class="flex items-center space-x-3 p-3 rounded-lg border border-slate-800 bg-slate-950 cursor-pointer hover:border-indigo-500 transition">
                                <input type="radio" name="pagamento" value="boleto" class="form-radio text-indigo-600 bg-slate-800 border-slate-600 focus:ring-indigo-500" onchange="toggleCartaoFields()">
                                <span class="font-medium">Boleto Bancário</span>
                            </label>
                        </div>

                        <div id="cartao-fields" class="space-y-4 p-4 border border-indigo-500/30 bg-indigo-900/10 rounded-xl hidden mb-6">
                            
                            <div id="parcelas-field" class="hidden">
                                <label class="block text-slate-300 text-xs font-bold mb-2 uppercase" for="parcelas">Parcelas</label>
                                <select id="parcelas" name="parcelas" class="w-full bg-slate-950 border border-slate-700 text-white text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 outline-none">
                                    <option value="1">1x de R$ <?php echo $total_formatado; ?> sem juros</option>
                                    <option value="2">2x sem juros</option>
                                    <option value="3">3x sem juros</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-slate-300 text-xs font-bold mb-2 uppercase" for="numero_cartao">Número do Cartão</label>
                                <input type="text" id="numero_cartao" name="numero_cartao" placeholder="0000 0000 0000 0000" class="w-full bg-slate-950 border border-slate-700 text-white text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 outline-none">
                            </div>
                            
                            <div class="flex gap-4">
                                <div class="w-1/2">
                                    <label class="block text-slate-300 text-xs font-bold mb-2 uppercase" for="vencimento">Vencimento</label>
                                    <input type="text" id="vencimento" name="vencimento" placeholder="MM/AA" class="w-full bg-slate-950 border border-slate-700 text-white text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 outline-none">
                                </div>
                                <div class="w-1/2">
                                    <label class="block text-slate-300 text-xs font-bold mb-2 uppercase" for="cvv">CVV</label>
                                    <input type="text" id="cvv" name="cvv" placeholder="123" class="w-full bg-slate-950 border border-slate-700 text-white text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 outline-none">
                                </div>
                            </div>

                            <div>
                                <label class="block text-slate-300 text-xs font-bold mb-2 uppercase" for="nome_cartao">Nome no Cartão</label>
                                <input type="text" id="nome_cartao" name="nome_cartao" class="w-full bg-slate-950 border border-slate-700 text-white text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 outline-none">
                            </div>
                        </div>
                        
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-500 text-white font-extrabold py-4 rounded-xl text-lg shadow-lg shadow-green-900/20 transition duration-200 hover:scale-[1.02] transform">
                            Confirmar Pagamento
                        </button>
                        
                        <p class="text-center text-xs text-slate-500 mt-4 flex justify-center items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            Ambiente 100% Seguro
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </main>
    
    <script>
        function toggleCartaoFields() {
            const pagamento = document.querySelector('input[name="pagamento"]:checked').value;
            const cartaoFields = document.getElementById('cartao-fields');
            const parcelasField = document.getElementById('parcelas-field');
            
            if (pagamento === 'credito' || pagamento === 'debito') {
                cartaoFields.classList.remove('hidden');
                
                if (pagamento === 'credito') {
                    parcelasField.classList.remove('hidden');
                } else {
                    parcelasField.classList.add('hidden');
                }

                document.querySelectorAll('#cartao-fields input').forEach(input => {
                    if (input.id !== 'parcelas') input.required = true;
                });

            } else {
                cartaoFields.classList.add('hidden');
                parcelasField.classList.add('hidden');

                document.querySelectorAll('#cartao-fields input').forEach(input => {
                    input.required = false;
                });
            }
        }

        document.addEventListener('DOMContentLoaded', toggleCartaoFields);
    </script>

</body>
</html>
