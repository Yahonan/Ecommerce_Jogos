<?php
session_start();
require_once 'conexao.php'; 

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: home.php");
    exit();
}

$jogo_id = $_GET['id'];
$jogo = null; 

$sql = "SELECT * FROM jogos WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $jogo_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    if ($resultado->num_rows === 1) {
        $jogo = $resultado->fetch_assoc();
    }
    $stmt->close();
}
$conn->close();

if (!$jogo) {
    header("Location: home.php?erro=jogo_nao_encontrado");
    exit();
}

$titulo = htmlspecialchars($jogo['titulo']);
$preco = number_format($jogo['preco'], 2, ',', '.');
$plataforma = htmlspecialchars($jogo['plataforma']);
$genero = htmlspecialchars($jogo['genero']);
$imagem_url = !empty($jogo['imagem_capa']) ? 'img/' . htmlspecialchars($jogo['imagem_capa']) : 'img/default.jpg';
$descricao_jogo = nl2br(htmlspecialchars($jogo['descricao'])); 
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?> - Detalhes</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-white min-h-screen flex flex-col overflow-x-hidden">
    
    <nav class="absolute top-0 w-full z-30 bg-transparent p-6">
        <div class="max-w-7xl mx-auto">
            <a href="home.php" class="inline-flex items-center text-white/80 hover:text-white transition font-medium gap-2 bg-black/20 hover:bg-black/40 px-5 py-2.5 rounded-full backdrop-blur-md border border-white/10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Voltar para Loja
            </a>
        </div>
    </nav>

    <div class="fixed inset-0 z-0">
        <div class="absolute inset-0 bg-slate-950/60 z-10"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/80 to-transparent z-10"></div>
        <img src="<?php echo $imagem_url; ?>" class="w-full h-full object-cover filter blur-2xl opacity-60 scale-110" alt="Background">
    </div>
    
    <main class="relative z-10 flex-grow flex items-center justify-center py-24 px-4 sm:px-6 lg:px-8">
        
        <div class="max-w-6xl w-full bg-slate-900/70 backdrop-blur-xl rounded-3xl shadow-2xl shadow-black/50 border border-white/10 overflow-hidden flex flex-col md:flex-row">
            
            <div class="md:w-5/12 relative group">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent opacity-60 md:hidden"></div>
                <img class="w-full h-full object-cover min-h-[400px] md:min-h-[600px] transition-transform duration-700" 
                     src="<?php echo $imagem_url; ?>" 
                     alt="<?php echo $titulo; ?>">
            </div>
            
            <div class="md:w-7/12 p-8 md:p-12 flex flex-col justify-center relative">
                <div class="absolute top-0 right-0 p-6 opacity-10">
                    <svg class="w-64 h-64 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M21 6H3c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-10 7H8v3H6v-3H3v-2h3V8h2v3h3v2zm4.5 2c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm4 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/></svg>
                </div>

                <div class="relative z-10">
                    <div class="flex flex-wrap gap-3 mb-6">
                        <span class="px-4 py-1.5 rounded-lg text-xs font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 uppercase tracking-widest">
                            <?php echo $genero; ?>
                        </span>
                        <span class="px-4 py-1.5 rounded-lg text-xs font-bold bg-slate-700/50 text-slate-300 border border-white/10 uppercase tracking-widest flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                            <?php echo $plataforma; ?>
                        </span>
                    </div>

                    <h1 class="text-4xl md:text-6xl font-black text-white mb-8 leading-none tracking-tight drop-shadow-lg">
                        <?php echo $titulo; ?>
                    </h1>
                    
                    <div class="prose prose-lg prose-invert mb-10 text-slate-300 leading-relaxed max-w-2xl">
                        <p><?php echo $descricao_jogo; ?></p>
                    </div>
                    
                    <div class="mt-auto pt-8 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-6">
                        
                        <div class="flex flex-col">
                            <span class="text-sm text-slate-400 uppercase font-bold tracking-wider">Preço</span>
                            <span class="text-5xl font-black text-white tracking-tight flex items-start gap-1">
                                <span class="text-2xl mt-2 text-indigo-400">R$</span><?php echo $preco; ?>
                            </span>
                        </div>
                        
                        <button class="w-full sm:w-auto flex-1 bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-4 px-8 rounded-xl shadow-lg shadow-indigo-500/30 transition-all transform hover:scale-105 active:scale-95 flex items-center justify-center gap-3 group">
                            <span>Adicionar ao Carrinho</span>
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

</body>
</html>