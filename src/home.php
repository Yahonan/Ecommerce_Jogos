<?php
session_start();
require_once 'conexao.php';

$busca = filter_input(INPUT_GET, 'busca', FILTER_SANITIZE_SPECIAL_CHARS);
$termo = "%" . $busca . "%";

if (!empty($busca)) {
    $sql = "SELECT * FROM jogos WHERE disponivel = TRUE AND titulo LIKE ? ORDER BY titulo ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $termo);
    $stmt->execute();
    $resultado = $stmt->get_result();
} else {
    $sql = "SELECT * FROM jogos WHERE disponivel = TRUE ORDER BY titulo ASC";
    $resultado = $conn->query($sql);
}

$jogos = [];
if ($resultado) {
    $jogos = $resultado->fetch_all(MYSQLI_ASSOC);
}

$nome_usuario = null;
if (isset($_SESSION['usuario_logado']) && $_SESSION['usuario_logado'] === true){
    $nome_usuario = $_SESSION['usuario_nome'];
} 

$total_itens_carrinho = 0;
if (isset($_SESSION['carrinho'])) {
    $total_itens_carrinho = count($_SESSION['carrinho']);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Store - Início</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html { scroll-behavior: smooth; }
    </style>
</head>
<body class="bg-slate-950 text-gray-100 font-sans antialiased selection:bg-indigo-500 selection:text-white">

    <nav class="sticky top-0 z-50 bg-slate-950/80 backdrop-blur-md border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                
                <div class="flex-shrink-0 flex items-center gap-2 hover:opacity-80 transition cursor-pointer">
                    <a href="home.php" class="text-2xl font-black tracking-tighter text-white">GAME<span class="text-indigo-500">STORE</span></a>
                </div>

                <div class="hidden md:block flex-1 max-w-lg mx-8">
                    <form action="home.php" method="GET" class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-500 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="busca" 
                               value="<?php echo $busca; ?>"
                               class="block w-full pl-10 pr-3 py-2.5 border border-slate-700 rounded-full leading-5 bg-slate-900 text-gray-300 placeholder-slate-500 focus:outline-none focus:bg-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm transition duration-200" 
                               placeholder="Buscar jogos...">
                    </form>
                </div>

                <div class="ml-auto flex items-center space-x-4 md:space-x-6">
                    
                    <a href="wishlist.php" class="relative group p-2 text-slate-400 hover:text-red-500 transition duration-300" title="Minha Lista de Desejos">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    </a>

                    <a href="carrinho_view.php" class="relative group p-2 text-slate-400 hover:text-white transition" title="Meu Carrinho">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <?php if($total_itens_carrinho > 0): ?>
                            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-indigo-600 rounded-full"><?php echo $total_itens_carrinho; ?></span>
                        <?php endif; ?>
                    </a>

                    <?php if ($nome_usuario): ?>
                        <div class="flex items-center gap-4">
                            
                            <a href="meus_pedidos.php" class="hidden md:flex items-center gap-2 text-indigo-400 hover:text-white border border-indigo-500/20 hover:bg-indigo-600 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                <span>Meus Pedidos</span>
                            </a>

                            <div class="text-right hidden lg:block">
                                <p class="text-xs text-slate-400 uppercase font-bold">Bem-vindo</p>
                                <p class="text-sm font-semibold text-white"><?php echo htmlspecialchars($nome_usuario); ?></p>
                            </div>
                            <a href="logout.php" class="text-red-400 hover:text-white border border-red-500/20 hover:bg-red-500 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300" title="Sair">
                                <svg class="w-5 h-5 md:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                <span class="hidden md:inline">Sair</span>
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="flex items-center space-x-4">
                            <a href="login.php" class="text-slate-300 hover:text-white font-medium transition">Login</a>
                            <a href="cadastro.php" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-full text-sm font-bold shadow-lg shadow-indigo-500/20 transition-all hover:scale-105">Criar Conta</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="md:hidden px-4 pb-4 border-t border-slate-800 pt-4 space-y-3">
            <form action="home.php" method="GET">
                <input type="text" name="busca" value="<?php echo $busca; ?>" class="block w-full py-2 px-4 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="Buscar jogos...">
            </form>
            <?php if ($nome_usuario): ?>
                <a href="meus_pedidos.php" class="block text-center w-full bg-slate-800 text-white py-2 rounded-lg">Meus Pedidos</a>
                <a href="wishlist.php" class="block text-center w-full bg-slate-800 text-white py-2 rounded-lg">Minha Lista de Desejos</a>
            <?php endif; ?>
        </div>
    </nav>

    <?php if(isset($_GET['sucesso']) && $_GET['sucesso'] == 'adicionado'): ?>
        <div class="bg-green-500/20 border-b border-green-500 text-green-300 text-center py-3 font-bold backdrop-blur-sm">
            Jogo adicionado ao carrinho com sucesso!
        </div>
    <?php endif; ?>

    <?php if(isset($_GET['alerta']) && $_GET['alerta'] == 'carrinho_vazio'): ?>
        <div class="bg-yellow-500/20 border-b border-yellow-500 text-yellow-300 text-center py-3 font-bold backdrop-blur-sm">
            Seu carrinho está vazio. Adicione jogos para continuar!
        </div>
    <?php endif; ?>

    <?php if(empty($busca)): ?>
    <div class="relative bg-slate-900 overflow-hidden border-b border-slate-800">
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/90 to-indigo-950/40 z-10"></div>
            <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1511512578047-dfb367046420?q=80&w=2071&auto=format&fit=crop')] bg-cover bg-center opacity-20"></div>
        </div>
        <div class="relative z-20 max-w-7xl mx-auto py-20 px-4 sm:px-6 lg:px-8 flex flex-col justify-center h-full">
            <h1 class="text-4xl md:text-6xl font-black tracking-tight text-white mb-6 drop-shadow-2xl max-w-2xl">
                Sua Próxima <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-cyan-400">Aventura</span> Começa Aqui
            </h1>
            <p class="text-lg text-slate-300 max-w-xl mb-8 leading-relaxed">
                Explore mundos fantásticos, enfrente desafios épicos e descubra os melhores preços em jogos digitais.
            </p>
        </div>
    </div>
    <?php endif; ?>
    
    <main class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
        
        <div class="flex items-center justify-between mb-10">
            <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                <span class="w-2 h-8 bg-indigo-500 rounded-full"></span>
                <?php echo !empty($busca) ? "Resultados para: <span class='text-indigo-400'>\"$busca\"</span>" : "Catálogo em Destaque"; ?>
            </h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">

            <?php if (empty($jogos)): ?>
                <div class="col-span-full flex flex-col items-center justify-center py-20 bg-slate-900/50 rounded-3xl border border-slate-800 border-dashed">
                    <p class="text-slate-400 text-xl font-medium">Nenhum jogo encontrado.</p>
                    <?php if(!empty($busca)): ?>
                        <a href="home.php" class="mt-4 text-indigo-400 hover:text-indigo-300 font-semibold hover:underline">Limpar pesquisa</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                
                <?php foreach ($jogos as $jogo): ?>
                    <div class="group relative bg-slate-900 rounded-2xl overflow-hidden border border-slate-800 hover:border-indigo-500/50 shadow-xl shadow-black/20 hover:shadow-indigo-500/10 transition-all duration-300 hover:-translate-y-2">
                        
                        <div class="relative aspect-[3/4] overflow-hidden">
                            <img class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500" 
                                 src="img/<?php echo htmlspecialchars($jogo['imagem_capa']); ?>" 
                                 alt="<?php echo htmlspecialchars($jogo['titulo']); ?>">
                            
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent opacity-80"></div>

                            <div class="absolute top-3 left-3 flex flex-wrap gap-2">
                                <span class="bg-slate-950/80 backdrop-blur-sm text-[10px] font-bold px-2.5 py-1 rounded-md border border-white/10 uppercase tracking-wider text-white">
                                    <?php echo htmlspecialchars($jogo['genero']); ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="absolute bottom-0 left-0 right-0 p-5 translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                            <p class="text-xs font-bold text-indigo-400 mb-1 uppercase tracking-wide"><?php echo htmlspecialchars($jogo['plataforma']); ?></p>
                            <h3 class="text-lg font-bold text-white leading-tight mb-3 line-clamp-1 group-hover:text-indigo-300 transition-colors">
                                <?php echo htmlspecialchars($jogo['titulo']); ?>
                            </h3>
                            
                            <div class="flex items-center justify-between pt-3 border-t border-white/10">
                                <span class="text-xl font-bold text-white">
                                    <span class="text-sm text-slate-400 font-normal mr-1">R$</span><?php echo number_format($jogo['preco'], 2, ',', '.'); ?>
                                </span>
                                
                                <a href="detalhe.php?id=<?php echo $jogo['id']; ?>" 
                                   class="opacity-0 group-hover:opacity-100 translate-x-4 group-hover:translate-x-0 transition-all duration-300 bg-indigo-600 hover:bg-indigo-500 text-white p-2 rounded-lg shadow-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </a>
                            </div>
                        </div>
                        
                        <a href="detalhe.php?id=<?php echo $jogo['id']; ?>" class="absolute inset-0 z-10"></a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </main>
    
    <footer class="bg-slate-950 border-t border-slate-900 mt-auto">
        <div class="max-w-7xl mx-auto py-10 px-4 flex flex-col items-center">
            <p class="text-2xl font-black tracking-tighter text-slate-700 mb-4">GAME<span class="text-slate-600">STORE</span></p>
            <p class="text-slate-500 text-sm">&copy; 2025 Game Store. Todos os direitos reservados.</p>
        </div>
    </footer>

</body>
</html>