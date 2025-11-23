<?php
session_start();
require_once 'conexao.php'; 

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: home.php");
    exit();
}

$jogo_id = $_GET['id'];
$usuario_id = $_SESSION['usuario_id'] ?? 0; 


$sql = "SELECT * FROM jogos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $jogo_id);
$stmt->execute();
$jogo = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$jogo) {
    header("Location: home.php?erro=jogo_nao_encontrado");
    exit();
}

$na_wishlist = false;
if ($usuario_id > 0) {
    $sql_wish = "SELECT id FROM wishlist WHERE jogo_id = ? AND usuario_id = ?";
    $stmt_w = $conn->prepare($sql_wish);
    $stmt_w->bind_param("ii", $jogo_id, $usuario_id);
    $stmt_w->execute();
    if ($stmt_w->get_result()->num_rows > 0) $na_wishlist = true;
    $stmt_w->close();
}


$sql_av = "SELECT a.*, u.nome as nome_usuario FROM avaliacoes a 
           JOIN usuarios u ON a.usuario_id = u.id 
           WHERE a.jogo_id = ? ORDER BY a.data_avaliacao DESC";
$stmt_av = $conn->prepare($sql_av);
$stmt_av->bind_param("i", $jogo_id);
$stmt_av->execute();
$result_av = $stmt_av->get_result();
$avaliacoes = $result_av->fetch_all(MYSQLI_ASSOC);
$stmt_av->close();


$media_nota = 0;
if (count($avaliacoes) > 0) {
    $soma = array_sum(array_column($avaliacoes, 'nota'));
    $media_nota = $soma / count($avaliacoes);
}

$titulo = htmlspecialchars($jogo['titulo']);
$preco = number_format($jogo['preco'], 2, ',', '.');
$imagem_url = !empty($jogo['imagem_capa']) ? 'img/' . htmlspecialchars($jogo['imagem_capa']) : 'img/default.jpg';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?> - Detalhes</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-white min-h-screen flex flex-col">
    
    <nav class="absolute top-0 w-full z-30 bg-transparent p-6">
        <div class="max-w-7xl mx-auto flex justify-between">
            <a href="home.php" class="inline-flex items-center text-white/80 hover:text-white transition font-medium gap-2 bg-black/20 hover:bg-black/40 px-5 py-2.5 rounded-full backdrop-blur-md border border-white/10">
                ← Voltar
            </a>
            
            <?php if($usuario_id > 0): ?>
            <form action="processar_wishlist.php" method="POST">
                <input type="hidden" name="jogo_id" value="<?php echo $jogo_id; ?>">
                <input type="hidden" name="redirect" value="detalhe.php?id=<?php echo $jogo_id; ?>">
                <button type="submit" class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-black/20 hover:bg-black/40 backdrop-blur-md border border-white/10 transition group">
                    <svg class="w-6 h-6 <?php echo $na_wishlist ? 'text-red-500 fill-current' : 'text-white group-hover:text-red-400'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                </button>
            </form>
            <?php endif; ?>
        </div>
    </nav>

    <div class="relative w-full h-[60vh] lg:h-[70vh]">
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-slate-950/60 to-slate-950 z-10"></div>
        <img src="<?php echo $imagem_url; ?>" class="w-full h-full object-cover object-top" alt="Capa">
        
        <div class="absolute bottom-0 left-0 w-full z-20 p-8 lg:p-16">
            <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-end justify-between gap-8">
                <div>
                    <div class="flex items-center gap-4 mb-4">
                        <span class="bg-indigo-600 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide">
                            <?php echo htmlspecialchars($jogo['genero']); ?>
                        </span>
                        <div class="flex items-center text-yellow-400">
                            <span class="text-lg font-bold mr-1"><?php echo number_format($media_nota, 1); ?></span>
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <span class="text-slate-400 text-sm ml-2">(<?php echo count($avaliacoes); ?> avaliações)</span>
                        </div>
                    </div>
                    <h1 class="text-5xl lg:text-7xl font-black text-white mb-2 drop-shadow-lg"><?php echo $titulo; ?></h1>
                    <p class="text-xl text-slate-300 max-w-2xl"><?php echo nl2br(htmlspecialchars($jogo['descricao'])); ?></p>
                </div>

                <div class="bg-slate-900/80 backdrop-blur-md p-6 rounded-2xl border border-white/10 min-w-[300px]">
                    <p class="text-sm text-slate-400 mb-1">Preço atual</p>
                    <p class="text-4xl font-black text-white mb-6">R$ <?php echo $preco; ?></p>
                    
                    <form action="carrinho.php" method="POST">
                        <input type="hidden" name="jogo_id" value="<?php echo $jogo_id; ?>">
                        <button type="submit" name="acao" value="adicionar" class="w-full bg-green-600 hover:bg-green-500 text-white font-bold py-4 px-6 rounded-xl transition flex items-center justify-center gap-2 shadow-lg shadow-green-900/20">
                            <span>Comprar Agora</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <section class="max-w-4xl mx-auto px-4 py-16 w-full">
        <h2 class="text-2xl font-bold mb-8 flex items-center gap-3">
            <span class="w-1 h-8 bg-indigo-500 rounded-full"></span>
            Avaliações da Comunidade
        </h2>

        <?php if($usuario_id > 0): ?>
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 mb-10">
            <h3 class="text-lg font-bold mb-4">Deixe sua opinião</h3>
            <form action="processar_avaliacao.php" method="POST">
                <input type="hidden" name="jogo_id" value="<?php echo $jogo_id; ?>">
                
                <div class="mb-4">
                    <label class="block text-sm text-slate-400 mb-2">Sua Nota</label>
                    <div class="flex gap-4">
                        <?php for($i=1; $i<=5; $i++): ?>
                        <label class="cursor-pointer">
                            <input type="radio" name="nota" value="<?php echo $i; ?>" class="hidden peer" required>
                            <span class="w-10 h-10 flex items-center justify-center rounded-lg bg-slate-800 border border-slate-700 text-slate-400 peer-checked:bg-yellow-500 peer-checked:text-black peer-checked:border-yellow-500 hover:bg-slate-700 transition font-bold">
                                <?php echo $i; ?>
                            </span>
                        </label>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm text-slate-400 mb-2">Seu Comentário</label>
                    <textarea name="comentario" rows="3" class="w-full bg-slate-950 border border-slate-700 rounded-lg p-3 text-white focus:border-indigo-500 outline-none" placeholder="O que você achou do jogo?" required></textarea>
                </div>
                
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white px-6 py-2 rounded-lg font-bold text-sm transition">
                    Publicar Avaliação
                </button>
            </form>
        </div>
        <?php else: ?>
            <div class="bg-slate-900/50 border border-slate-800 rounded-xl p-6 mb-10 text-center">
                <p class="text-slate-400">Faça <a href="login.php" class="text-indigo-400 hover:underline">login</a> para avaliar este jogo.</p>
            </div>
        <?php endif; ?>

        <div class="space-y-6">
            <?php if (empty($avaliacoes)): ?>
                <p class="text-slate-500 text-center italic">Ainda não há avaliações para este jogo. Seja o primeiro!</p>
            <?php else: ?>
                <?php foreach ($avaliacoes as $av): ?>
                <div class="border-b border-slate-800 pb-6 last:border-0">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-indigo-900 flex items-center justify-center font-bold text-indigo-300">
                                <?php echo strtoupper(substr($av['nome_usuario'], 0, 1)); ?>
                            </div>
                            <div>
                                <p class="font-bold text-white"><?php echo htmlspecialchars($av['nome_usuario']); ?></p>
                                <p class="text-xs text-slate-500"><?php echo date('d/m/Y', strtotime($av['data_avaliacao'])); ?></p>
                            </div>
                        </div>
                        <div class="flex text-yellow-500">
                            <?php for($i=0; $i<$av['nota']; $i++) echo '★'; ?>
                        </div>
                    </div>
                    <p class="text-slate-300 leading-relaxed ml-13 pl-13">
                        <?php echo nl2br(htmlspecialchars($av['comentario'])); ?>
                    </p>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

</body>
</html>